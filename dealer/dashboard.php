<?php
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dealer Panel</title>
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
            <a href="dashboard.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
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
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Products -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">Total Product Count</h3>
                            <p class="text-sm font-medium text-gray-500">Total Products</p>
                        </div>
                    </div>
                </div>

                <!-- Total Staff -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">8</h3>
                            <p class="text-sm font-medium text-gray-500">Total Staff</p>
                        </div>
                    </div>
                </div>

                <!-- Total Sales -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">1,245</h3>
                            <p class="text-sm font-medium text-gray-500">Total Sales</p>
                        </div>
                    </div>
                </div>

                <!-- Total Profit -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">₹45,280</h3>
                            <p class="text-sm font-medium text-gray-500">Total Profit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                
                <!-- Top Selling Products -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Top 5 Selling Products</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product Name</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty Sold</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">Rice (1kg)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">450</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹29,250</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">Wheat Flour (1kg)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">380</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹22,040</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">Sugar (1kg)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">320</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹17,600</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">Cooking Oil (1L)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">250</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹36,250</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">Tea Powder (250g)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">200</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹21,000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Sales -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Sales</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">INV-1245</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">Rice (1kg)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹650</td>
                                        <td class="py-4 px-4 text-sm text-gray-500">Feb 5, 2:30 PM</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">INV-1244</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">Cooking Oil (1L)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹435</td>
                                        <td class="py-4 px-4 text-sm text-gray-500">Feb 5, 1:15 PM</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">INV-1243</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">Sugar (1kg)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹275</td>
                                        <td class="py-4 px-4 text-sm text-gray-500">Feb 5, 11:45 AM</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">INV-1242</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">Tea Powder (250g)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹315</td>
                                        <td class="py-4 px-4 text-sm text-gray-500">Feb 5, 10:20 AM</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-sm text-gray-900">INV-1241</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">Wheat Flour (1kg)</td>
                                        <td class="py-4 px-4 text-sm text-gray-900">₹580</td>
                                        <td class="py-4 px-4 text-sm text-gray-500">Feb 5, 9:00 AM</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>
        // Mobile menu toggle
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });

        // Responsive sidebar
        function handleResize() {
            if (window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
            } else {
                sidebar.classList.remove('-translate-x-full');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize();


        // Profile dropdown toggle

        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');

        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });

    </script>

</body>
</html>
