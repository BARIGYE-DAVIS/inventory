@extends('layouts.cashier-layout')

@section('title', 'Point of Sale (POS)')

@section('page-title')
    <i class="fas fa-cash-register text-green-600 mr-2"></i>Point of Sale
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- LEFT SIDE: Products -->
    <div class="lg:col-span-2 space-y-4">
        
        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search Products -->
                <div class="relative">
                    <input type="text" 
                           id="productSearch" 
                           placeholder="Search products (Name, SKU, Barcode)..." 
                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                </div>

                <!-- Category Filter -->
                <div>
                    <select id="categoryFilter" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-box text-green-600 mr-2"></i>Products
            </h3>
            
            <div id="productsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[calc(100vh-300px)] overflow-y-auto">
                @forelse($products as $product)
                    <div class="product-card border border-gray-200 rounded-lg p-3 hover:shadow-lg transition cursor-pointer"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->name }}"
                         data-price="{{ $product->selling_price }}"
                         data-stock="{{ $product->quantity }}"
                         data-unit="{{ $product->unit ?? 'pcs' }}"
                         data-category="{{ $product->category_id ?? '' }}"
                         onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price }}, '{{ $product->unit ?? 'pcs' }}', {{ $product->quantity }})">
                        
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-100 rounded-lg mb-2 overflow-hidden">
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-box text-4xl text-gray-300"></i>
                            </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <h4 class="font-semibold text-sm text-gray-900 truncate" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h4>
                        @if($product->sku)
                        <p class="text-xs text-gray-500">{{ $product->sku }}</p>
                        @endif
                        <p class="text-lg font-bold text-green-600 mt-1">
                            UGX {{ number_format($product->selling_price, 0) }}
                        </p>
                        <p class="text-xs {{ $product->quantity < 10 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                            Stock: {{ $product->quantity }} {{ $product->unit ?? 'pcs' }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-2"></i>
                        <p>No products available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Cart & Checkout -->
    <div class="lg:col-span-1 space-y-4">
        
        <!-- ✅ CUSTOMER SELECTION (OPTIONAL) -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-user text-green-600 mr-1"></i>Customer (Optional)
            </h3>

            <!-- Customer Options -->
            <div class="space-y-3">
                <!-- Walk-in Customer (Default) -->
                <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="radio" 
                           name="customer_option" 
                           value="walk_in" 
                           checked
                           onchange="toggleCustomerFields()"
                           class="h-4 w-4 text-green-600 focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">Walk-in Customer</span>
                </label>

                <!-- Existing Customer -->
                <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="radio" 
                           name="customer_option" 
                           value="existing"
                           onchange="toggleCustomerFields()"
                           class="h-4 w-4 text-green-600 focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">Existing Customer</span>
                </label>

                <!-- New Customer -->
                <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="radio" 
                           name="customer_option" 
                           value="new"
                           onchange="toggleCustomerFields()"
                           class="h-4 w-4 text-green-600 focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">New Customer</span>
                </label>
            </div>

            <!-- Existing Customer Dropdown -->
            <div id="existingCustomerDiv" class="hidden mt-3">
                <select id="existingCustomerId" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                    @endforeach
                </select>
            </div>

            <!-- New Customer Form -->
            <div id="newCustomerDiv" class="hidden mt-3 space-y-2 p-3 bg-green-50 rounded-lg">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="newCustomerName" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                           placeholder="Customer name">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="newCustomerPhone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                           placeholder="0700123456">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" 
                           id="newCustomerEmail" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                           placeholder="customer@email.com">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" 
                           id="newCustomerAddress" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                           placeholder="Kampala, Uganda">
                </div>
            </div>
        </div>

        <!-- Cart -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex justify-between items-center">
                <span><i class="fas fa-shopping-cart text-green-600 mr-2"></i>Cart</span>
                <button onclick="clearCart()" class="text-xs text-red-600 hover:text-red-800">
                    <i class="fas fa-trash mr-1"></i>Clear
                </button>
            </h3>

            <!-- Cart Items -->
            <div id="cartItems" class="space-y-2 max-h-64 overflow-y-auto mb-4">
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                    <p>Cart is empty</p>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold">UGX <span id="subtotalAmount">0</span></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Discount:</span>
                    <input type="number" 
                           id="discountAmount" 
                           value="0" 
                           min="0" 
                           step="100"
                           onchange="updateTotals()"
                           class="w-24 px-2 py-1 border border-gray-300 rounded text-right">
                </div>
                
                <!-- Optional Tax Checkbox -->
                <div class="flex justify-between items-center text-sm py-2 border-t">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               id="addTaxCheckbox" 
                               onchange="updateTotals()"
                               class="mr-2 h-4 w-4 text-green-600 focus:ring-green-500 rounded">
                        <span class="text-gray-600">Add Tax (18%)</span>
                    </label>
                    <span class="font-semibold">UGX <span id="taxAmount">0</span></span>
                </div>
                
                <div class="flex justify-between text-lg font-bold text-green-600 pt-2 border-t">
                    <span>TOTAL:</span>
                    <span>UGX <span id="totalAmount">0</span></span>
                </div>
            </div>
        </div>

        <!-- Payment -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Payment (Cash)
            </h3>

            <!-- Amount Paid -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount Paid</label>
                <input type="number" 
                       id="amountPaid" 
                       value="0" 
                       min="0" 
                       step="100"
                       onchange="calculateChange()"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-lg font-bold text-right">
            </div>

            <!-- Exact Amount Button -->
            <div class="mb-4">
                <button onclick="exactAmount()" 
                        class="w-full px-4 py-2 bg-blue-100 hover:bg-blue-200 rounded-lg text-sm font-semibold text-blue-700">
                    <i class="fas fa-equals mr-1"></i> Exact Amount
                </button>
            </div>

            <!-- Change -->
            <div class="mb-4 p-3 bg-green-50 rounded-lg" id="changeBox">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Change:</span>
                    <span class="text-xl font-bold text-green-600">UGX <span id="changeAmount">0</span></span>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="saleNotes" 
                          rows="2" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                          placeholder="Add any notes..."></textarea>
            </div>

            <!-- Checkout Button -->
            <button onclick="processSale()" 
                    id="checkoutBtn"
                    class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold text-lg disabled:bg-gray-300 disabled:cursor-not-allowed">
                <i class="fas fa-check-circle mr-2"></i>Complete Sale
            </button>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full" id="receiptContent">
        <!-- Receipt content will be injected here -->
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Cart data
    let cart = [];

    // Toggle customer fields
    function toggleCustomerFields() {
        const option = document.querySelector('input[name="customer_option"]:checked').value;
        
        document.getElementById('existingCustomerDiv').classList.add('hidden');
        document.getElementById('newCustomerDiv').classList.add('hidden');
        
        if (option === 'existing') {
            document.getElementById('existingCustomerDiv').classList.remove('hidden');
        } else if (option === 'new') {
            document.getElementById('newCustomerDiv').classList.remove('hidden');
        }
    }

    // Add product to cart
    function addToCart(id, name, price, unit, maxStock) {
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            if (existingItem.quantity >= maxStock) {
                alert('Cannot add more! Maximum stock available: ' + maxStock);
                return;
            }
            existingItem.quantity++;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1,
                unit: unit,
                maxStock: maxStock
            });
        }

        renderCart();
        updateTotals();
    }

    // Remove from cart
    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
        updateTotals();
    }

    // Update quantity
    function updateQuantity(id, newQuantity) {
        const item = cart.find(item => item.id === id);
        if (item) {
            if (newQuantity > item.maxStock) {
                alert('Cannot exceed available stock: ' + item.maxStock);
                return;
            }
            if (newQuantity <= 0) {
                removeFromCart(id);
            } else {
                item.quantity = parseFloat(newQuantity);
                renderCart();
                updateTotals();
            }
        }
    }

    // Clear cart
    function clearCart() {
        if (cart.length === 0) return;
        
        if (confirm('Clear all items from cart?')) {
            cart = [];
            renderCart();
            updateTotals();
        }
    }

    // Render cart items
    function renderCart() {
        const cartItemsDiv = document.getElementById('cartItems');
        
        if (cart.length === 0) {
            cartItemsDiv.innerHTML = `
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                    <p>Cart is empty</p>
                </div>
            `;
            document.getElementById('checkoutBtn').disabled = true;
            return;
        }

        document.getElementById('checkoutBtn').disabled = false;

        let html = '';
        cart.forEach(item => {
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900">${item.name}</p>
                        <p class="text-xs text-gray-500">UGX ${item.price.toLocaleString()} × ${item.quantity} ${item.unit}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" 
                                class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <input type="number" 
                               value="${item.quantity}" 
                               min="1" 
                               max="${item.maxStock}"
                               onchange="updateQuantity(${item.id}, this.value)"
                               class="w-12 px-2 py-1 border border-gray-300 rounded text-center text-sm">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" 
                                class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                        <button onclick="removeFromCart(${item.id})" 
                                class="w-6 h-6 bg-red-100 hover:bg-red-200 text-red-600 rounded flex items-center justify-center ml-2">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div class="ml-3 text-right">
                        <p class="font-bold text-green-600">UGX ${(item.price * item.quantity).toLocaleString()}</p>
                    </div>
                </div>
            `;
        });

        cartItemsDiv.innerHTML = html;
    }

    // Update totals
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
        
        // Tax is OPTIONAL
        let tax = 0;
        const addTax = document.getElementById('addTaxCheckbox').checked;
        if (addTax) {
            const taxableAmount = subtotal - discount;
            tax = taxableAmount * 0.18; // 18% tax
        }
        
        const total = subtotal - discount + tax;

        document.getElementById('subtotalAmount').textContent = subtotal.toLocaleString();
        document.getElementById('taxAmount').textContent = tax.toLocaleString();
        document.getElementById('totalAmount').textContent = total.toLocaleString();

        calculateChange();
    }

    // Calculate change
    function calculateChange() {
        const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/,/g, ''));
        const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
        const change = amountPaid - total;

        document.getElementById('changeAmount').textContent = Math.max(0, change).toLocaleString();
        
        const changeBox = document.getElementById('changeBox');
        const changeAmountSpan = document.getElementById('changeAmount');
        
        if (amountPaid < total && amountPaid > 0) {
            changeBox.classList.remove('bg-green-50');
            changeBox.classList.add('bg-red-50');
            changeAmountSpan.classList.remove('text-green-600');
            changeAmountSpan.classList.add('text-red-600');
        } else {
            changeBox.classList.add('bg-green-50');
            changeBox.classList.remove('bg-red-50');
            changeAmountSpan.classList.add('text-green-600');
            changeAmountSpan.classList.remove('text-red-600');
        }
    }

    // Exact amount button
    function exactAmount() {
        const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/,/g, ''));
        document.getElementById('amountPaid').value = total;
        calculateChange();
    }

    // Process sale
    // Process sale
async function processSale() {
    if (cart.length === 0) {
        alert('Cart is empty!');
        return;
    }

    const customerOption = document.querySelector('input[name="customer_option"]:checked').value;
    
    // Build sale data
    let saleData = {
        customer_option: customerOption,
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        discount: parseFloat(document.getElementById('discountAmount').value) || 0,
        add_tax: document.getElementById('addTaxCheckbox').checked,
        amount_paid: parseFloat(document.getElementById('amountPaid').value) || 0,
        notes: document.getElementById('saleNotes').value || null,
    };

    // Handle customer selection
    if (customerOption === 'existing') {
        const customerId = document.getElementById('existingCustomerId').value;
        if (!customerId) {
            alert('Please select a customer');
            return;
        }
        saleData.customer_id = customerId;
    } else if (customerOption === 'new') {
        const name = document.getElementById('newCustomerName').value.trim();
        const phone = document.getElementById('newCustomerPhone').value.trim();
        
        if (!name || !phone) {
            alert('Please enter customer name and phone number');
            return;
        }
        
        saleData.new_customer_name = name;
        saleData.new_customer_phone = phone;
        saleData.new_customer_email = document.getElementById('newCustomerEmail').value.trim();
        saleData.new_customer_address = document.getElementById('newCustomerAddress').value.trim();
    }

    // Validate payment
    const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/,/g, ''));
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;

    if (amountPaid < total) {
        alert('Amount paid is less than total amount!');
        return;
    }

    // Disable button and show loading
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.disabled = true;
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch('{{ route("pos.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(saleData)
        });

        const result = await response.json();

        if (result.success) {
            // Show success modal
            showReceipt(result);
            
            // Reset cart and form
            cart = [];
            renderCart();
            updateTotals();
            document.querySelector('input[name="customer_option"][value="walk_in"]').checked = true;
            toggleCustomerFields();
            document.getElementById('discountAmount').value = 0;
            document.getElementById('addTaxCheckbox').checked = false;
            document.getElementById('amountPaid').value = 0;
            document.getElementById('saleNotes').value = '';
            document.getElementById('existingCustomerId').value = '';
            document.getElementById('newCustomerName').value = '';
            document.getElementById('newCustomerPhone').value = '';
            document.getElementById('newCustomerEmail').value = '';
            document.getElementById('newCustomerAddress').value = '';
            calculateChange();
        } else {
            alert('Error: ' + (result.message || 'Failed to process sale'));
        }
    } catch (error) {
        console.error('Sale Error:', error);
        alert('Failed to process sale. Please try again.');
    } finally {
        checkoutBtn.disabled = false;
        checkoutBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Complete Sale';
    }
}

// ✅ FIXED: Show receipt modal
function showReceipt(data) {
    const modal = document.getElementById('receiptModal');
    const content = document.getElementById('receiptContent');

    content.innerHTML = `
        <div class="p-6">
            <div class="text-center mb-6">
                <i class="fas fa-check-circle text-6xl text-green-600 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900">Sale Completed!</h2>
                <p class="text-gray-600">Sale #${data.sale_number}</p>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between text-lg">
                    <span class="text-gray-700">Total Amount:</span>
                    <span class="font-bold">UGX ${data.total.toLocaleString()}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Amount Paid:</span>
                    <span class="font-semibold">UGX ${data.amount_paid.toLocaleString()}</span>
                </div>
                <div class="flex justify-between text-xl border-t pt-3">
                    <span class="text-gray-700 font-bold">Change:</span>
                    <span class="font-bold text-green-600">UGX ${data.change.toLocaleString()}</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="/pos/receipt/${data.sale_id}" 
                   class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center">
                    <i class="fas fa-receipt mr-2"></i>View Receipt
                </a>
                <button onclick="printReceiptDirect(${data.sale_id})" 
                        class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
            
            <button onclick="closeReceipt()" 
                    class="w-full mt-3 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-times mr-2"></i>Close & New Sale
            </button>
        </div>
    `;

    modal.classList.remove('hidden');
}

// ✅ NEW: Print receipt directly
function printReceiptDirect(saleId) {
    window.open('/pos/receipt/' + saleId, '_blank');
}

// Close receipt modal
function closeReceipt() {
    document.getElementById('receiptModal').classList.add('hidden');
}

    // Product search
    document.getElementById('productSearch').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        filterProducts(search, document.getElementById('categoryFilter').value);
    });

    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const category = this.value;
        filterProducts(document.getElementById('productSearch').value.toLowerCase(), category);
    });

    // Filter products
    function filterProducts(search, category) {
        const products = document.querySelectorAll('.product-card');
        
        products.forEach(product => {
            const name = product.dataset.name.toLowerCase();
            const productCategory = product.dataset.category;
            
            const matchesSearch = name.includes(search) || search === '';
            const matchesCategory = category === '' || productCategory === category;
            
            product.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('productSearch').focus();
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            if (!document.getElementById('checkoutBtn').disabled) {
                processSale();
            }
        }
        
        if (e.key === 'Escape') {
            document.getElementById('productSearch').value = '';
            filterProducts('', '');
        }
    });
</script>
@endpush