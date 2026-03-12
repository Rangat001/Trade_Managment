<?php
require_once 'includes/auth_check.php';

// Get Products 

$products = [];
$stmt = $conn->prepare("
    SELECT 
        id,
        product_name,
        selling_price
    FROM products
    WHERE dealer_id = ?
    ORDER BY product_name
");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Entry - Dealer Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#6366F1',
                    }
                }
            }
        }
    </script>
    <?php includePermissionJS(); ?>
</head>
<body class="bg-gray-50">
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 w-64 h-screen bg-white border-r border-gray-200 transition-transform duration-300 z-50">
        <div class="px-6 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                DealerPro
            </h2>
        </div>
        
        <nav class="p-3 mt-4">
           <a href="dashboard.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-home w-5 mr-3"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="products.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-box w-5 mr-3"></i>
                <span class="font-medium">Products</span>
            </a>
            <a href="companies.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-box w-5 mr-3"></i>
                <span class="font-medium">Companies</span>
            </a>
            <a href="purchases.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-shopping-cart w-5 mr-3"></i>
                <span class="font-medium">Purchases</span>
            </a>
            <a href="sales.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
                <i class="fas fa-cash-register w-5 mr-3"></i>
                <span class="font-medium">Sales</span>
            </a>
            <a href="staff.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-users w-5 mr-3"></i>
                <span class="font-medium">Staff Management</span>
            </a>
            <a href="reports.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-chart-line w-5 mr-3"></i>
                <span class="font-medium">Reports</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Navigation -->
        <header class="sticky top-0 bg-white border-b border-gray-200 px-8 py-4 shadow-sm z-40">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="menuToggle" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-semibold text-gray-900">Sales Entry</h1>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-full cursor-pointer hover:bg-gray-100 transition-colors">
                    <img src="https://ui-avatars.com/api/?name=Dealer+Admin&background=4F46E5&color=fff" 
                         alt="Profile" class="w-9 h-9 rounded-full">
                    <span class="font-medium text-gray-700 hidden sm:block">Dealer Admin</span>
                    <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-8 max-w-7xl">
            
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Create New Sale</h2>
                <p class="text-gray-500 mt-1">Record a product sale and manage delivery</p>
            </div>

            <!-- Sales Form -->
            <form action="process_sale.php" method="POST" id="salesForm">

                <input type="hidden" name="final_amount" id="finalAmountInput" value="0">

                <!-- Section 1: Sale Details -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        Sale Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sale Date *
                            </label>
                            <input type="date" name="sale_date" id="saleDate" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-base">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Type *
                            </label>
                            <select name="billing_type" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-base">
                                <option value="">-- Select Billing Type --</option>
                                <option value="GST">GST Billing</option>
                                <option value="NON-GST">Non-GST Billing</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Mode *
                            </label>
                            <select name="payment_mode" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-base">
                                <option value="">-- Select Payment Mode --</option>
                                <option value="CASH">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="CARD">Card</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Delivery Status *
                            </label>
                            <select name="delivery_status" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-base">
                                <option value="">-- Select Delivery Status --</option>
                                <option value="ON-HAND">On-Hand (Delivered)</option>
                                <option value="PENDING">Pending Delivery</option>
                                <option value="DELIVERED">Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Products Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shopping-basket text-primary"></i>
                            Products
                        </h3>
                        <button type="button" onclick="addProductRow()" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all text-sm">
                            <i class="fas fa-plus"></i>
                            Add Product
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full" id="productsTable">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-2/5">Product</th>
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/6">Quantity</th>
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/5">Selling Price (₹)</th>
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/5">Line Total (₹)</th>
                                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Action</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <!-- Product rows will be added here dynamically -->
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        <i class="fas fa-shopping-basket text-5xl mb-3 text-gray-300"></i>
                                        <p class="text-base">Click "Add Product" to start adding items to this sale</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section 3: Summary Panel -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="lg:col-span-2"></div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-calculator text-blue-600"></i>
                            Sale Summary
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-blue-200">
                                <span class="text-sm text-gray-600">Total Items</span>
                                <span class="text-lg font-bold text-gray-900" id="totalItems">0</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-blue-200">
                                <span class="text-sm text-gray-600">Total Quantity</span>
                                <span class="text-lg font-bold text-gray-900" id="totalQuantity">0</span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-base font-medium text-gray-700">Total Sale Amount</span>
                                <span class="text-2xl font-bold text-blue-600" id="totalSaleAmount">₹0.00</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-blue-200">
                                <span class="text-sm text-gray-600">Discount (₹)</span>
                                <input type="number" step="0.01" min="0"
                                       id="discountAmount"
                                       name="discount_amount"
                                       value="0"
                                       oninput="calculateSummary()"
                                       class="w-32 px-2 py-1 text-right border border-gray-300 rounded-md text-sm">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="sales.html"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all text-base">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition-all text-base">
                        <i class="fas fa-check-circle"></i>
                        Save Sale
                    </button>
                </div>

            </form>

        </main>
    </div>

    <script>
        // Product data (In production, load from backend)
        const productsList = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        // Initialize
        let productRowCounter = 0;

        // Set today's date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('saleDate').value = today;
        });

        // Add product row
        function addProductRow() {
            const tbody = document.getElementById('productsTableBody');
            const emptyRow = tbody.querySelector('tr td[colspan="5"]');
            if (emptyRow) {
                emptyRow.parentElement.remove();
            }
            
            productRowCounter++;
            const rowId = `row_${productRowCounter}`;
            
            const row = document.createElement('tr');
            row.id = rowId;
            row.className = 'border-b border-gray-200 hover:bg-gray-50 transition-colors';
            row.innerHTML = `
                <td class="py-3 px-4">
                    <select name="products[${productRowCounter}][product_id]" 
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm product-select"
                            onchange="updateSellingPrice('${rowId}')" required>
                        <option value="">-- Select Product --</option>
                       ${productsList.map(p => `
                            <option value="${p.id}" data-price="${p.selling_price}">
                                ${p.product_name}
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td class="py-3 px-4">
                    <input type="number" name="products[${productRowCounter}][quantity]" 
                           min="1" placeholder="0"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm quantity-input"
                           oninput="calculateLineTotal('${rowId}')" required>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-1">
                        <span class="text-gray-500 text-sm">₹</span>
                        <input type="number" name="products[${productRowCounter}][selling_price]" 
                               step="0.01" min="0" placeholder="0.00"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm price-input"
                               oninput="calculateLineTotal('${rowId}')" required>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-1">
                        <span class="text-gray-500 text-sm">₹</span>
                        <input type="text" class="line-total w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm font-semibold text-gray-900" 
                               readonly value="0.00">
                    </div>
                </td>
                <td class="py-3 px-4 text-center">
                    <button type="button" onclick="removeProductRow('${rowId}')"
                            class="w-9 h-9 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(row);
            calculateSummary();
        }

        // Update selling price when product selected
        function updateSellingPrice(rowId) {
            const row = document.getElementById(rowId);
            const productSelect = row.querySelector('.product-select');
            const priceInput = row.querySelector('.price-input');

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');

            if (price !== null) {
                priceInput.value = parseFloat(price).toFixed(2);
                calculateLineTotal(rowId);
            }
        }

        // Calculate line total for a row
        function calculateLineTotal(rowId) {
            const row = document.getElementById(rowId);
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const lineTotal = quantity * price;
            
            row.querySelector('.line-total').value = lineTotal.toFixed(2);
            calculateSummary();
        }

        // Remove product row
        function removeProductRow(rowId) {
            const row = document.getElementById(rowId);
            row.remove();
            
            const tbody = document.getElementById('productsTableBody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500">
                            <i class="fas fa-shopping-basket text-5xl mb-3 text-gray-300"></i>
                            <p class="text-base">Click "Add Product" to start adding items to this sale</p>
                        </td>
                    </tr>
                `;
            }
            
            calculateSummary();
        }

        // Calculate summary totals
function calculateSummary() {
    const tbody = document.getElementById('productsTableBody');
    const rows = tbody.querySelectorAll('tr[id^="row_"]');

    let totalItems = 0;
    let totalQuantity = 0;
    let totalAmount = 0;

    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const lineTotal = parseFloat(row.querySelector('.line-total').value) || 0;

        if (quantity > 0) {
            totalItems++;
            totalQuantity += quantity;
            totalAmount += lineTotal;
        }
    });

    const discount = parseFloat(document.getElementById('discountAmount')?.value) || 0;
    const finalAmount = Math.max(totalAmount - discount, 0);

    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQuantity').textContent = totalQuantity;
    document.getElementById('totalSaleAmount').textContent = `₹${finalAmount.toFixed(2)}`;

    // Hidden field for backend
    document.getElementById('finalAmountInput').value = finalAmount.toFixed(2);
}


        // Form validation before submit
        document.getElementById('salesForm').addEventListener('submit', function(e) {
            const tbody = document.getElementById('productsTableBody');
            const hasProducts = tbody.querySelector('.line-total') !== null;
            
            if (!hasProducts) {
                e.preventDefault();
                alert('Please add at least one product to the sale');
                return false;
            }
            
            // Validate that all products have quantity and price
            const rows = tbody.querySelectorAll('tr[id^="row_"]');
            let hasInvalidRow = false;
            
            rows.forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                
                if (!productSelect.value || quantity <= 0 || price <= 0) {
                    hasInvalidRow = true;
                }
            });
            
            if (hasInvalidRow) {
                e.preventDefault();
                alert('Please fill in all product details with valid values');
                return false;
            }
            
            return true;
        });

        // Mobile menu toggle
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });

        function handleResize() {
            if (window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
            } else {
                sidebar.classList.remove('-translate-x-full');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize();
    </script>

</body>
</html>
