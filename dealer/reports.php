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

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button id="exportDropdown" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-full shadow hover:shadow-md transition-all">
                            <i class="fas fa-file-export text-sm"></i>
                            <span class="font-medium hidden sm:block">Export</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="exportMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <button class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Sales (Excel)</button>
                            <button class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Purchases (Excel)</button>
                            <button class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Profit Summary (Excel)</button>
                            <button class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Stock Levels (Excel)</button>
                        </div>
                    </div>

                    <div class="relative">
                        <button id="profileDropdown" class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-full cursor-pointer hover:bg-gray-100 transition-colors">
                            <img src="https://ui-avatars.com/api/?name=Dealer+Admin&background=4F46E5&color=fff" 
                                 alt="Profile" class="w-9 h-9 rounded-full">
                            <span class="font-medium text-gray-700 hidden sm:block">Dealer <?php echo($_SESSION['rgt_logedin_user_role']); ?></span>
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

                            <!-- sale count -->
                            <?php
                                $sale_stmt = $conn->prepare(
                                    "SELECT COUNT(*) AS total_sales
                                     FROM sales s
                                     INNER JOIN sale_items si ON si.sale_id = s.id
                                     WHERE s.dealer_id = ?"
                                );                                
                                $sale_stmt->bind_param("i", $dealer_id);
                                $sale_stmt->execute();
                                $sale_result = $sale_stmt->get_result();
                                $sale_data = $sale_result->fetch_assoc();
                            ?>
                            
                            <!-- Revenue -->
                            <?php
                                $sale_stmt = $conn->prepare("SELECT SUM(total_amount) AS revenue FROM sales WHERE dealer_id = ? ");
                                $sale_stmt->bind_param("i", $dealer_id);
                                $sale_stmt->execute();
                                $sale_result = $sale_stmt->get_result();
                                $sale = $sale_result->fetch_assoc();
                            ?>
                            <strong class="text-lg text-gray-900">₹<?php echo number_format($sale['revenue'], 2); ?></strong>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Transactions</span>
                            <strong class="text-lg text-gray-900"><?php echo $sale_data['total_sales']; ?></strong>
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
                        <?php
                            $sale_stmt = $conn->prepare("SELECT SUM(amount) AS expense FROM company_transactions WHERE dealer_id = ? AND type = 'DEBIT'");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $expense = $sale_result->fetch_assoc();
                        ?>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Purchases</span>
                            <strong class="text-lg text-gray-900">₹<?php echo number_format($expense['expense'], 2); ?></strong>
                        </div>

                        <div class="flex justify-between items-center">
                        <?php
                            $sale_stmt = $conn->prepare("SELECT COUNT(*) AS purchase_count FROM purchase_orders WHERE dealer_id = ?");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $purchase = $sale_result->fetch_assoc();
                        ?>
                            <span class="text-sm text-gray-600">Total Entries</span>
                            <strong class="text-lg text-gray-900"><?php echo $purchase['purchase_count']; ?></strong>
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
                            <?php
                                $sale_stmt = $conn->prepare("SELECT SUM(profit) AS total_profit FROM sales WHERE dealer_id = ? ");
                                $sale_stmt->bind_param("i", $dealer_id);
                                $sale_stmt->execute();
                                $sale_result = $sale_stmt->get_result();
                                $profit = $sale_result->fetch_assoc();
                            ?>
                            <span class="text-sm text-gray-600">Total Profit</span>
                            <strong class="text-lg text-green-600">₹<?php echo number_format($profit['total_profit'], 2); ?></strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <?php
                                $revenue = isset($sale['revenue']) ? (float)$sale['revenue'] : 0;
                                $totalProfit = isset($profit['total_profit']) ? (float)$profit['total_profit'] : 0;
                                $profitMargin = $revenue > 0 ? ($totalProfit / $revenue) * 100 : 0;
                            ?>
                            <span class="text-sm text-gray-600">Profit Margin</span>
                            <strong class="text-lg text-gray-900"><?php echo number_format($profitMargin, 2); ?>%</strong>
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
                            <?php
                            $sale_stmt = $conn->prepare("SELECT COUNT(*) AS products FROM products WHERE dealer_id = ?");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $products = $sale_result->fetch_assoc();
                            ?>
                            <span class="text-sm text-gray-600">Total Products</span>
                            <strong class="text-lg text-gray-900"><?php echo $products['products']; ?></strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Low Stock Items</span>
                            <?php
                            $sale_stmt = $conn->prepare("SELECT COUNT(*) AS low_stock_products FROM products WHERE dealer_id = ? AND current_stock < 10");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $products = $sale_result->fetch_assoc();
                            ?>
                            <strong class="text-lg text-amber-600"><?php echo $products['low_stock_products']; ?></strong>
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
                            <?php
                                $monthly_stmt = $conn->prepare(
                                    "SELECT DATE_FORMAT(s.sale_date, '%b %Y') AS month_label,
                                            SUM(s.total_amount) AS revenue,
                                            SUM(s.profit) AS profit
                                     FROM sales s
                                     WHERE s.dealer_id = ?
                                     GROUP BY YEAR(s.sale_date), MONTH(s.sale_date)
                                     ORDER BY YEAR(s.sale_date) DESC, MONTH(s.sale_date) DESC
                                     LIMIT 5"
                                );
                                $monthly_stmt->bind_param("i", $dealer_id);
                                $monthly_stmt->execute();
                                $monthly_result = $monthly_stmt->get_result();
                                while ($row = $monthly_result->fetch_assoc()) {
                                    $rev = isset($row['revenue']) ? (float)$row['revenue'] : 0;
                                    $prof = isset($row['profit']) ? (float)$row['profit'] : 0;
                            ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600"><?php echo $row['month_label']; ?></p>
                                    <p class="text-xl font-bold text-gray-900 mt-1">₹<?php echo number_format($rev, 2); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Profit</p>
                                    <p class="text-lg font-semibold text-green-600 mt-1">₹<?php echo number_format($prof, 2); ?></p>
                                </div>
                            </div>
                            <?php }
                                $monthly_stmt->close();
                            ?>
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
                            <?php
                                $low_stmt = $conn->prepare(
                                    "SELECT product_name, current_stock
                                     FROM products
                                     WHERE dealer_id = ?
                                     ORDER BY current_stock DESC
                                     LIMIT 3"
                                );
                                $low_stmt->bind_param("i", $dealer_id);
                                $low_stmt->execute();
                                $low_result = $low_stmt->get_result();
                                while ($row = $low_result->fetch_assoc()) {
                                    $stock = isset($row['current_stock']) ? (int)$row['current_stock'] : 0;
                                    $badge = $stock <= 5 ? 'Critical' : 'Low';
                                    $badge_class = $stock <= 5
                                        ? 'px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700'
                                        : 'px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700';
                            ?>
                            <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($row['product_name']); ?></p>
                                    <p class="text-sm text-gray-600 mt-1">Stock: <?php echo $stock; ?> units</p>
                                </div>
                                <span class="<?php echo $badge_class; ?>"><?php echo $badge; ?></span>
                            </div>
                            <?php }
                                $low_stmt->close();
                            ?>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>

        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');
        const exportDropdown = document.getElementById('exportDropdown');
        const exportMenu = document.getElementById('exportMenu');

        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });

        exportDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            exportMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
            if (!exportDropdown.contains(e.target) && !exportMenu.contains(e.target)) {
                exportMenu.classList.add('hidden');
            }
        });
        
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
