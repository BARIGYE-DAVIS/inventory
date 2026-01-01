<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventory Period Closed</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>📊 Inventory Period Closed</h2>
        
        <p>Hi {{ $user->name }},</p>
        
        <p>Your inventory period for <strong>{{ $business->name }}</strong> has been automatically closed.</p>
        
        <p><strong>Period:</strong> {{ $periodStart->format('M d, Y') }} - {{ $periodEnd->format('M d, Y') }}</p>
        
        <h3>Summary</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <tr style="background-color: #f0f0f0;">
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Metric</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Value</strong></td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Total Products</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $totalProducts }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Products with Variance</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $productsWithVariance }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Total Variance</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($totalVariance, 2) }} units</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Overstock</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($overstock, 2) }} units</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Shortage</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($shortage, 2) }} units</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Avg Variance</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($variancePercentage, 2) }}%</td>
            </tr>
        </table>
        
        <h3>What Happened</h3>
        <ul>
            <li>✓ All product stock levels have been locked for this period</li>
            <li>✓ Closing stock becomes next period's opening stock</li>
            <li>✓ Period data is now available for reporting and analysis</li>
        </ul>
        
        <h3>Next Steps</h3>
        <ol>
            <li>Review the Period Details in your inventory system</li>
            <li>Investigate any significant variances</li>
            <li>Approve pending adjustments for next period</li>
        </ol>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <p style="font-size: 12px; color: #666;">
            This is an automated notification. Your inventory system continues to track transactions normally.
        </p>
    </div>
</body>
</html>
