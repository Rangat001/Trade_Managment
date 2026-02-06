<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases - Dealer Panel</title>
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
            <a href="purchases.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
                <i class="fas fa-shopping-cart w-5 mr-3"></i>
                <span class="font-medium">Purchases</span>
            </a>
            <a href="sales.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Purchases</h1>
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
        <main class="p-8">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Purchase Entry</h2>
                <button onclick="openAddPurchaseModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Add Purchase</span>
                </button>
            </div>

            <!-- Purchases Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Purchase Price</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Added By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm text-gray-500">Feb 5, 2026 10:30 AM</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">Rice (1kg)</td>
                                <td class="py-4 px-6 text-sm text-gray-900">100</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹50.00</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">₹5,000.00</td>
                                <td class="py-4 px-6 text-sm text-gray-500">Dealer Admin</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm text-gray-500">Feb 4, 2026 2:15 PM</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">Wheat Flour (1kg)</td>
                                <td class="py-4 px-6 text-sm text-gray-900">80</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹45.00</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">₹3,600.00</td>
                                <td class="py-4 px-6 text-sm text-gray-500">Rajesh Kumar</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm text-gray-500">Feb 3, 2026 11:00 AM</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">Sugar (1kg)</td>
                                <td class="py-4 px-6 text-sm text-gray-900">60</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹42.00</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">₹2,520.00</td>
                                <td class="py-4 px-6 text-sm text-gray-500">Dealer Admin</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm text-gray-500">Feb 2, 2026 4:20 PM</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">Cooking Oil (1L)</td>
                                <td class="py-4 px-6 text-sm text-gray-900">50</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹120.00</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">₹6,000.00</td>
                                <td class="py-4 px-6 text-sm text-gray-500">Dealer Admin</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm text-gray-500">Feb 1, 2026 9:45 AM</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">Tea Powder (250g)</td>
                                <td class="py-4 px-6 text-sm text-gray-900">40</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹85.00</td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">₹3,400.00</td>
                                <td class="py-4 px-6 text-sm text-gray-500">Rajesh Kumar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Add Purchase Modal -->
    <div id="addPurchaseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Add Purchase Entry</h3>
                <button onclick="closeAddPurchaseModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <form action="add_purchase.php" method="POST" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Product *</label>
                        <select name="product_id" id="productSelect" required onchange="updatePurchasePrice()"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            <option value="">-- Select Product --</option>
                            <option value="1" data-price="50.00">Rice (1kg)</option>
                            <option value="2" data-price="45.00">Wheat Flour (1kg)</option>
                            <option value="3" data-price="42.00">Sugar (1kg)</option>
                            <option value="4" data-price="120.00">Cooking Oil (1L)</option>
                            <option value="5" data-price="85.00">Tea Powder (250g)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (₹) *</label>
                        <input type="number" name="purchase_price" id="purchasePrice" step="0.01" min="0" required onchange="checkPriceChange()"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-500">Current purchase price will be auto-filled</p>
                    </div>
                    
                    <div id="priceWarning" class="hidden">
                        <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                            <p class="text-sm text-amber-800">Price has changed from the original purchase price</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="quantity" min="1" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeAddPurchaseModal()" 
                            class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                        Add Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Functions
        function openAddPurchaseModal() {
            document.getElementById('addPurchaseModal').classList.remove('hidden');
        }
        
        function closeAddPurchaseModal() {
            document.getElementById('addPurchaseModal').classList.add('hidden');
            document.getElementById('priceWarning').classList.add('hidden');
        }
        
        // Close modal on outside click
        document.getElementById('addPurchaseModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddPurchaseModal();
        });
        
        // Update purchase price when product is selected
        function updatePurchasePrice() {
            const select = document.getElementById('productSelect');
            const priceInput = document.getElementById('purchasePrice');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            
            if (price) {
                priceInput.value = price;
                priceInput.setAttribute('data-original-price', price);
                document.getElementById('priceWarning').classList.add('hidden');
            }
        }
        
        // Check if price changed
        function checkPriceChange() {
            const priceInput = document.getElementById('purchasePrice');
            const originalPrice = parseFloat(priceInput.getAttribute('data-original-price'));
            const currentPrice = parseFloat(priceInput.value);
            
            if (originalPrice && currentPrice !== originalPrice) {
                document.getElementById('priceWarning').classList.remove('hidden');
            } else {
                document.getElementById('priceWarning').classList.add('hidden');
            }
        }
        
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
