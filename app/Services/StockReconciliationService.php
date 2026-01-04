<?php

namespace App\Services;

use App\Models\{Product, InventoryPeriod, StockAdjustment};
use Carbon\Carbon;

class StockReconciliationService
{
    /**
     * Calculate complete stock reconciliation for a product in a period
     * 
     * Returns array with:
     * - opening_stock: from products.opening_stock
     * - purchases: sum of purchase_items
     * - sales: sum of sale_items
     * - system_calculated_stock: opening + purchases - sales
     * - physical_count: from stock_adjustments
     * - variance: physical_count - system_calculated_stock
     * - variance_percentage: variance / system_calculated_stock * 100
     * - final_accepted_stock: physical_count (or system if no count)
     * - reconciliation_adjustment: variance
     * - status: reconciled or unreconciled
     */
    public static function calculateReconciliation(Product $product, Carbon $periodStart, Carbon $periodEnd): array
    {
        // 1. Opening Stock
        $openingStock = $product->opening_stock;

        // 2. Purchases (sum of all purchase_items in period)
        $purchases = $product->purchaseItems()
            ->whereHas('purchase', function ($q) use ($product, $periodStart, $periodEnd) {
                $q->where('business_id', $product->business_id)
                    ->whereBetween('created_at', [$periodStart, $periodEnd]);
            })
            ->sum('quantity');

        // 3. Sales (sum of all sale_items in period)
        $sales = $product->saleItems()
            ->whereHas('sale', function ($q) use ($product, $periodStart, $periodEnd) {
                $q->where('business_id', $product->business_id)
                    ->whereBetween('created_at', [$periodStart, $periodEnd]);
            })
            ->sum('quantity');

        // 4. System Calculated Stock
        $systemCalculatedStock = $openingStock + $purchases - $sales;

        // 5. Physical Count (from stock_adjustments - latest approved count)
        $latestAdjustment = StockAdjustment::where('product_id', $product->id)
            ->where('status', 'approved')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->latest('adjustment_date')
            ->first();

        $physicalCount = $latestAdjustment?->physical_count;
        
        // 6. Variance (Loss/Gain)
        $variance = 0;
        $variancePercentage = 0;
        
        if ($physicalCount !== null) {
            $variance = $physicalCount - $systemCalculatedStock;
            $variancePercentage = $systemCalculatedStock > 0 
                ? ($variance / $systemCalculatedStock) * 100 
                : 0;
        }

        // 7. Final Accepted Stock
        $finalAcceptedStock = $physicalCount ?? $systemCalculatedStock;

        // 8. Reconciliation Adjustment
        $reconciliationAdjustment = $physicalCount !== null ? $variance : 0;

        return [
            // Original data
            'opening_stock' => (float) $openingStock,
            'purchases' => (float) $purchases,
            'sales' => (float) $sales,
            
            // System calculated
            'system_calculated_stock' => (float) $systemCalculatedStock,
            'system_label' => self::formatStockLine(
                'System Calculated Stock',
                $openingStock,
                '+',
                $purchases,
                '-',
                $sales,
                $systemCalculatedStock
            ),
            
            // Physical count & variance
            'physical_count' => $physicalCount ? (float) $physicalCount : null,
            'variance' => (float) $variance,
            'variance_percentage' => (float) $variancePercentage,
            'variance_label' => self::formatVarianceLine(
                'Variance (Loss)',
                $physicalCount,
                $systemCalculatedStock,
                $variance,
                $variancePercentage
            ),
            
            // Final reconciliation
            'reconciliation_adjustment' => (float) $reconciliationAdjustment,
            'final_accepted_stock' => (float) $finalAcceptedStock,
            
            // Status
            'is_reconciled' => $physicalCount !== null,
            'has_variance' => abs($variance) > 0.01, // Using small threshold for float comparison
            'is_loss' => $variance < -0.01,
            'is_gain' => $variance > 0.01,
            
            // Metadata
            'latest_adjustment' => $latestAdjustment,
            'adjustment_date' => $latestAdjustment?->adjustment_date,
            'adjustment_reason' => $latestAdjustment?->reason,
            'adjustment_notes' => $latestAdjustment?->notes,
        ];
    }

    /**
     * Get reconciliation for an inventory period record
     */
    public static function getReconciliationFromPeriod(InventoryPeriod $period): array
    {
        return [
            'opening_stock' => (float) $period->opening_stock,
            'purchases' => (float) $period->purchases,
            'sales' => (float) $period->sales,
            'system_calculated_stock' => (float) $period->calculated_stock,
            'physical_count' => $period->physical_count ? (float) $period->physical_count : null,
            'variance' => (float) $period->variance,
            'variance_percentage' => (float) $period->variance_percentage,
            'final_accepted_stock' => (float) ($period->physical_count ?? $period->calculated_stock),
            'reconciliation_adjustment' => (float) $period->variance,
            'is_reconciled' => $period->physical_count !== null,
            'has_variance' => abs($period->variance) > 0.01,
            'is_loss' => $period->variance < -0.01,
            'is_gain' => $period->variance > 0.01,
            'period' => $period,
        ];
    }

    /**
     * Format stock calculation line for display
     */
    private static function formatStockLine(
        string $label,
        float $opening,
        string $purchaseOp,
        float $purchases,
        string $salesOp,
        float $sales,
        float $result
    ): array {
        return [
            'label' => $label,
            'opening_stock' => $opening,
            'opening_label' => "Opening Stock",
            'purchase_op' => $purchaseOp,
            'purchases' => $purchases,
            'purchases_label' => "Purchases",
            'sales_op' => $salesOp,
            'sales' => $sales,
            'sales_label' => "Sales",
            'result' => $result,
            'result_label' => $label,
        ];
    }

    /**
     * Format variance line for display
     */
    private static function formatVarianceLine(
        string $label,
        ?float $physical,
        float $system,
        float $variance,
        float $percentage
    ): array {
        return [
            'label' => $label,
            'physical_count' => $physical,
            'physical_label' => 'Physical Count',
            'system_stock' => $system,
            'system_label' => 'System Calculated Stock',
            'variance' => $variance,
            'variance_label' => $label,
            'percentage' => $percentage,
            'type' => $variance < 0 ? 'loss' : ($variance > 0 ? 'gain' : 'match'),
        ];
    }

    /**
     * Get all reconciliations for a business in a period
     */
    public static function getBusinessReconciliations(int $businessId, Carbon $periodStart, Carbon $periodEnd): array
    {
        $products = Product::where('business_id', $businessId)->get();
        
        $reconciliations = [];
        foreach ($products as $product) {
            $reconciliations[] = [
                'product' => $product,
                'reconciliation' => self::calculateReconciliation($product, $periodStart, $periodEnd),
            ];
        }
        
        return $reconciliations;
    }

    /**
     * Calculate summary statistics for all reconciliations
     */
    public static function getSummaryStats(array $reconciliations): array
    {
        $totalOpeningStock = 0;
        $totalPurchases = 0;
        $totalSales = 0;
        $totalSystemCalculated = 0;
        $totalPhysicalCount = 0;
        $totalVariance = 0;
        $totalReconciled = 0;
        $itemsWithLoss = 0;
        $itemsWithGain = 0;
        $totalLoss = 0;
        $totalGain = 0;

        foreach ($reconciliations as $item) {
            $reconciliation = $item['reconciliation'];
            
            $totalOpeningStock += $reconciliation['opening_stock'];
            $totalPurchases += $reconciliation['purchases'];
            $totalSales += $reconciliation['sales'];
            $totalSystemCalculated += $reconciliation['system_calculated_stock'];
            $totalVariance += $reconciliation['variance'];
            
            if ($reconciliation['is_reconciled']) {
                $totalReconciled++;
                $totalPhysicalCount += $reconciliation['physical_count'];
                
                if ($reconciliation['is_loss']) {
                    $itemsWithLoss++;
                    $totalLoss += abs($reconciliation['variance']);
                } elseif ($reconciliation['is_gain']) {
                    $itemsWithGain++;
                    $totalGain += $reconciliation['variance'];
                }
            }
        }

        $overallVariancePercentage = $totalSystemCalculated > 0 
            ? ($totalVariance / $totalSystemCalculated) * 100 
            : 0;

        return [
            'total_opening_stock' => $totalOpeningStock,
            'total_purchases' => $totalPurchases,
            'total_sales' => $totalSales,
            'total_system_calculated' => $totalSystemCalculated,
            'total_physical_count' => $totalPhysicalCount,
            'total_variance' => $totalVariance,
            'overall_variance_percentage' => $overallVariancePercentage,
            'total_reconciled_items' => $totalReconciled,
            'items_with_loss' => $itemsWithLoss,
            'items_with_gain' => $itemsWithGain,
            'total_loss_amount' => $totalLoss,
            'total_gain_amount' => $totalGain,
            'total_items' => count($reconciliations),
        ];
    }
}
