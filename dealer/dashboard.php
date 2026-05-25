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
                        <span class="font-medium text-gray-700 hidden sm:block">Dealer <?php echo($_SESSION['rgt_logedin_user_role']); ?></span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        
                        <!-- Add this -->
                        <a href="#" onclick="openProfileModal()" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                    
                        <div class="border-t border-gray-100 my-1"></div>

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
                <!-- Total Expense -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-white-500 to-gray-600 flex items-center justify-center">
                            <img src="../asset/img/icons/expense.png" alt="Total Expense" class="w-12 h-12 object-contain">
                        </div>
                        <div>
                            <?php
                            $sale_stmt = $conn->prepare("SELECT SUM(amount) AS expense FROM company_transactions WHERE dealer_id = ? AND type = 'DEBIT'");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $sale_data = $sale_result->fetch_assoc();
                            ?>
                            <h3 class="text-3xl font-bold text-gray-900">₹ <?php echo number_format($sale_data['expense']); ?></h3>
                            <p class="text-sm font-medium text-gray-500">Total Expense</p>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-white-500 to-gray-600 flex items-center justify-center">
                            <img src="../asset/img/icons/revenue.png" alt="Total Revenue" class="w-12 h-12 object-contain">
                        </div>
                        <div>
                            <?php
                            $sale_stmt = $conn->prepare("SELECT SUM(total_amount) AS revenue FROM sales WHERE dealer_id = ? ");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $sale_data = $sale_result->fetch_assoc();
                            ?>

                            <h3 class="text-3xl font-bold text-gray-900">₹ <?php echo number_format($sale_data['revenue']); ?></h3>
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        </div>
                    </div>
                </div>

                <!-- Total Sales -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-white-500 to-gray-600 flex items-center justify-center">
                            <img src="../asset/img/icons/sales.png" alt="Total Sales" class="w-12 h-12 object-contain">
                        </div>
                        <div>
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
                            <h3 class="text-3xl font-bold text-gray-900"><?php echo $sale_data['total_sales']; ?></h3>
                            <p class="text-sm font-medium text-gray-500">Total Sales</p>
                        </div>
                    </div>
                </div>

                <!-- Total Profit -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-white-500 to-gray-600 flex items-center justify-center">
                            <img src="../asset/img/icons/profit.png" alt="Total Profit" class="w-13 h-13 object-contain">
                        </div>
                        <div>
                            <?php
                            $sale_stmt = $conn->prepare("SELECT SUM(profit) AS total_profit FROM sales WHERE dealer_id = ? ");
                            $sale_stmt->bind_param("i", $dealer_id);
                            $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            $sale_data = $sale_result->fetch_assoc();
                            ?>
                            <h3 class="text-3xl font-bold text-gray-900">₹ <?php echo number_format($sale_data['total_profit']); ?> </h3>
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
                        <h3 class="text-lg font-semibold text-gray-900">Top 5 Least(Qty) Products</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product Name</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php 
                                        $sale_stmt = $conn->prepare("SELECT p.product_name, p.current_stock, p.base_price , c.company_name FROM products p JOIN companies c ON p.company_id = c.id WHERE p.dealer_id = ? ORDER BY current_stock ASC LIMIT 5 ");
                                        $sale_stmt->bind_param("i", $dealer_id);
                                        $sale_stmt->execute();
                                        $sale_result = $sale_stmt->get_result();
                                        while($row = $sale_result->fetch_assoc()) {
                                            echo '<tr class="hover:bg-gray-50 transition-colors">
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['product_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['company_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['current_stock'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">₹ '.number_format($row['base_price']).'</td>
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                

                <!-- Recent Purchases -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">5 Recent Purchases</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php 
                                        $purchase_stmt = $conn->prepare("SELECT c.company_name, p.product_name, pu.quantity, pu.total_price, po.order_date FROM purchase_orders po JOIN purchase_order_items pu ON po.id = pu.order_id JOIN products p ON pu.product_id = p.id JOIN companies c ON p.company_id = c.id WHERE po.dealer_id = ? ORDER BY po.order_date DESC LIMIT 5");
                                        $purchase_stmt->bind_param("i", $dealer_id);
                                        $purchase_stmt->execute();
                                        $purchase_result = $purchase_stmt->get_result();
                                        while($row = $purchase_result->fetch_assoc()) {
                                            echo '<tr class="hover:bg-gray-50 transition-colors">
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['company_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['product_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['quantity'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">₹ '.number_format($row['total_price']).'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.date('d M Y', strtotime($row['order_date'])).'</td>
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Best Selling Products -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Top 5 Best Seller</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php 
                                        $purchase_stmt = $conn->prepare("SELECT
                                                            p.id AS product_id,
                                                            p.product_name,
                                                            c.company_name,
                                                            SUM(si.quantity) AS total_qty_sold,
                                                            SUM(si.quantity * si.selling_price) AS total_revenue
                                                        FROM sale_items si
                                                        JOIN sales s     ON si.sale_id = s.id
                                                        JOIN products p  ON si.product_id = p.id
                                                        JOIN companies c ON p.company_id = c.id
                                                        WHERE s.dealer_id = ?
                                                        GROUP BY p.id, p.product_name, c.company_name
                                                        ORDER BY total_qty_sold DESC
                                                        LIMIT 5;");
                                        $purchase_stmt->bind_param("i", $dealer_id);
                                        $purchase_stmt->execute();
                                        $purchase_result = $purchase_stmt->get_result();
                                        while($row = $purchase_result->fetch_assoc()) {
                                            echo '<tr class="hover:bg-gray-50 transition-colors">
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['company_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['product_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['total_qty_sold'].'</td>
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Selling -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Selling</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bill No.</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php 
                                        $purchase_stmt = $conn->prepare("SELECT
                                                        s.id  AS sale_id,
                                                        p.product_name,
                                                        si.quantity,
                                                        (si.quantity * si.selling_price) AS amount,
                                                        s.sale_date
                                                    FROM sales s
                                                    JOIN sale_items si ON si.sale_id = s.id
                                                    JOIN products p    ON si.product_id = p.id
                                                    WHERE s.dealer_id = ?
                                                    ORDER BY s.sale_date DESC
                                                    LIMIT 5;");
                                        $purchase_stmt->bind_param("i", $dealer_id);
                                        $purchase_stmt->execute();
                                        $purchase_result = $purchase_stmt->get_result();
                                        while($row = $purchase_result->fetch_assoc()) {
                                            echo '<tr class="hover:bg-gray-50 transition-colors">
                                                <td class="py-4 px-4 text-sm text-gray-900">'.'BL'.str_pad($row['sale_id'], 6, '0', STR_PAD_LEFT).'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['sale_date'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['product_name'].'</td>
                                                <td class="py-4 px-4 text-sm text-gray-900">'.$row['quantity'].'</td>
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



            </div>

        </main>

        
    </div>

    <!-- ░░ PROFILE MODAL ░░ -->
<div id="profileModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600">
            <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                <i class="fas fa-user-edit"></i> Edit Profile
            </h2>
            <button onclick="closeProfileModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="profileForm" class="px-6 py-5 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name</label>
                <input type="text" name="owner_name" id="f_owner_name"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 text-sm"
                       placeholder="Enter owner name">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mobile No</label>
                <input type="text" name="phone" id="f_phone" maxlength="10"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 text-sm"
                       placeholder="Enter mobile number">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">GST No</label>
                <input type="text" name="GST_NO" id="f_gst" maxlength="15"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 text-sm"
                       placeholder="Enter GST number">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="Address" id="f_address" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 text-sm resize-none"
                          placeholder="Enter address"></textarea>
            </div>

            <!-- Alert -->
            <div id="profileAlert" class="hidden text-sm px-4 py-2 rounded-lg"></div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeProfileModal()"
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all text-sm">
                    Cancel
                </button>
                <button type="button" onclick="saveProfile()"
                        class="flex-1 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all text-sm">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>

        </form>
    </div>
</div>

    <script>
        function openProfileModal() {
    // Close dropdown first
    document.getElementById('profileMenu').classList.add('hidden');

    // Fetch current dealer data
    fetch('ajax/get_dealer_profile.php')
        .then(r => r.json())
        .then(data => {
            document.getElementById('f_owner_name').value = data.owner_name ?? '';
            document.getElementById('f_phone').value      = data.phone ?? '';
            document.getElementById('f_gst').value        = data.GST_NO ?? '';
            document.getElementById('f_address').value    = data.Address ?? '';
        });

    const modal = document.getElementById('profileModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('profileAlert').classList.add('hidden');
}

function saveProfile() {
    const form = document.getElementById('profileForm');
    const data = new FormData(form);

    fetch('ajax/update_dealer_profile.php', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        const alert = document.getElementById('profileAlert');
        alert.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
        if (res.success) {
            alert.classList.add('bg-green-100', 'text-green-700');
            alert.textContent = '✓ Profile updated successfully!';
            setTimeout(() => closeProfileModal(), 1500);
        } else {
            alert.classList.add('bg-red-100', 'text-red-700');
            alert.textContent = '✗ ' + (res.message ?? 'Something went wrong');
        }
    });
}

// Close modal on backdrop click
document.getElementById('profileModal').addEventListener('click', function(e) {
    if (e.target === this) closeProfileModal();
});

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
