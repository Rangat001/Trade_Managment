<?php
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Dealer Panel</title>
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
            <a href="sales.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-cash-register w-5 mr-3"></i>
                <span class="font-medium">Sales</span>
            </a>
            <a href="staff.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-users w-5 mr-3"></i>
                <span class="font-medium">Staff Management</span>
            </a>
            <a href="reports.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Reports</h1>
                </div>
                
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-full cursor-pointer hover:bg-gray-100 transition-colors">
                        <img src="https://ui-avatars.com/api/?name=Dealer+Admin&background=4F46E5&color=fff" 
                             alt="Profile" class="w-9 h-9 rounded-full">
                        <span class="font-medium text-gray-700 hidden sm:block">Dealer Admin</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        
                        <a href="logout.php" class="flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>

            </div>
        </header>

        <!-- Page Content -->
        <main class="p-8">
            
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Reports & Analytics</h2>
                <p class="text-gray-500 mt-1">View comprehensive business reports and insights</p>
            </div>

            <!-- Report Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Sales Summary -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                        <h3 class="text-lg font-semibold text-blue-900 flex items-center gap-2">
                            <i class="fas fa-shopping-bag"></i>
                            Sales Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Sales</span>
                            <strong class="text-lg text-gray-900">₹126,140</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Transactions</span>
                            <strong class="text-lg text-gray-900">1,245</strong>
                        </div>
                    </div>
                </div>

                <!-- Purchase Summary -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100">
                        <h3 class="text-lg font-semibold text-purple-900 flex items-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            Purchase Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Purchases</span>
                            <strong class="text-lg text-gray-900">₹80,860</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Entries</span>
                            <strong class="text-lg text-gray-900">156</strong>
                        </div>
                    </div>
                </div>

                <!-- Profit Summary -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                        <h3 class="text-lg font-semibold text-green-900 flex items-center gap-2">
                            <i class="fas fa-chart-line"></i>
                            Profit Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Profit</span>
                            <strong class="text-lg text-green-600">₹45,280</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Profit Margin</span>
                            <strong class="text-lg text-gray-900">35.89%</strong>
                        </div>
                    </div>
                </div>

                <!-- Stock Summary -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-amber-100">
                        <h3 class="text-lg font-semibold text-amber-900 flex items-center gap-2">
                            <i class="fas fa-boxes"></i>
                            Stock Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Products</span>
                            <strong class="text-lg text-gray-900">124</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Low Stock Items</span>
                            <strong class="text-lg text-amber-600">8</strong>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Detailed Reports Section -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Monthly Performance -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Monthly Performance</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">January 2026</p>
                                    <p class="text-xl font-bold text-gray-900 mt-1">₹28,450</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Profit</p>
                                    <p class="text-lg font-semibold text-green-600 mt-1">₹9,240</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">December 2025</p>
                                    <p class="text-xl font-bold text-gray-900 mt-1">₹32,680</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Profit</p>
                                    <p class="text-lg font-semibold text-green-600 mt-1">₹11,520</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">November 2025</p>
                                    <p class="text-xl font-bold text-gray-900 mt-1">₹29,340</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Profit</p>
                                    <p class="text-lg font-semibold text-green-600 mt-1">₹10,180</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Low Stock Alert</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Tea Powder (250g)</p>
                                    <p class="text-sm text-gray-600 mt-1">Stock: 8 units</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded-full">Low</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Cooking Oil (1L)</p>
                                    <p class="text-sm text-gray-600 mt-1">Stock: 5 units</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Critical</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Sugar (1kg)</p>
                                    <p class="text-sm text-gray-600 mt-1">Stock: 9 units</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded-full">Low</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>
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
