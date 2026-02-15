<?php
require '../includes/scripts/connection.php';  
session_start();
if(isset($_SESSION['rgt_logedin_user_id']) && (trim($_SESSION['rgt_logedin_user_id']) !== '')){
    $user_id = $_SESSION['rgt_logedin_user_id'];
    $user_role = $_SESSION['rgt_logedin_user_role'];
    if($user_role != "ADMIN"){
        header("Location: ../404.php");
        exit;
    }
}else{
    header("Location: ../auth/sign-in.php");
    exit;
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
$sale_id = intval($_GET['id'] ?? 0);

if($sale_id <= 0){
    header("Location: sales.php");
    exit;
}

// Get sale details
$sql = "SELECT * FROM sales WHERE id = ? AND dealer_id = ? AND is_deleted = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $sale_id, $dealer_id);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();

if(!$sale){
    header("Location: sales.php");
    exit;
}

// Get sale items
$items_sql = "
    SELECT 
        si.*,
        p.product_name
    FROM sale_items si
    JOIN products p ON p.id = si.product_id
    WHERE si.sale_id = ?
    ORDER BY si.id
";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $sale_id);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Details - <?= 'BL' . str_pad($sale_id, 6, '0', STR_PAD_LEFT) ?></title>
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
                <i class="fas fa-building w-5 mr-3"></i>
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
                    <h1 class="text-2xl font-semibold text-gray-900">Sale Details</h1>
                </div>
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-full cursor-pointer hover:bg-gray-100 transition-colors">
                        <img src="https://ui-avatars.com/api/?name=Dealer+Admin&background=4F46E5&color=fff" 
                             alt="Profile" class="w-9 h-9 rounded-full">
                        <span class="font-medium text-gray-700 hidden sm:block">Dealer Admin</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
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
            
            <!-- Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <a href="sales.php" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Sales</span>
                </a>

                <a href="print_bill.php?sale_id=<?= $sale_id ?>" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <i class="fas fa-print"></i>
                    <span>Print Bill</span>
                </a>
               
            </div>

            <!-- Sale Summary Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Bill #<?= 'BL' . str_pad($sale_id, 6, '0', STR_PAD_LEFT) ?></h2>
                        <p class="text-gray-500 mt-1"><?= date("d F Y, h:i A", strtotime($sale['sale_date'])) ?></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-medium">
                            <?= htmlspecialchars($sale['billing_type']) ?>
                        </span>
                    </div>


                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6 pt-6 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Payment Mode</p>
                        <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($sale['payment_mode']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Delivery Status</p>
                        <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($sale['delivery']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Discount</p>
                        <p class="text-lg font-semibold text-red-600">₹<?= number_format($sale['bill_amount'] - $sale['total_amount']?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Profit</p>
                        <p class="text-lg font-semibold text-green-600">₹<?= number_format($sale['profit'], 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Sale Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Base Price</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Selling Price</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Line Total</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php 
                            $total_qty = 0;
                            $total_amount = 0;
                            while($item = $items->fetch_assoc()): 
                                $line_total = $item['selling_price'] * $item['quantity'];
                                $line_profit = ($item['selling_price'] - $item['base_price']) * $item['quantity'];
                                $total_qty += $item['quantity'];
                                $total_amount += $line_total;
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    ₹<?= number_format($item['base_price'], 2) ?>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    ₹<?= number_format($item['selling_price'], 2) ?>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <?= $item['quantity'] ?>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    ₹<?= number_format($line_total, 2) ?>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium text-green-600">
                                    ₹<?= number_format($line_profit, 2) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="3" class="py-4 px-6 text-sm font-bold text-gray-900">TOTAL</td>
                                <td class="py-4 px-6 text-sm font-bold text-gray-900"><?= $total_qty ?></td>
                                <td class="py-4 px-6 text-sm font-bold text-gray-900">₹<?= number_format($total_amount, 2) ?></td>
                                <td class="py-4 px-6 text-sm font-bold text-green-600">₹<?= number_format($sale['profit'], 2) ?></td>
                            </tr>
                            <?php if(($sale['bill_amount'] - $sale['total_amount']) > 0): ?>
                            <tr>
                                <td colspan="4" class="py-2 px-6 text-sm text-right text-gray-700">Discount:</td>
                                <td colspan="2" class="py-2 px-6 text-sm font-semibold text-red-600">-₹<?= number_format($sale['bill_amount'] - $sale['total_amount'], 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="bg-primary">
                                <td colspan="4" class="py-4 px-6 text-sm text-right font-bold text-white">FINAL AMOUNT:</td>
                                <td colspan="2" class="py-4 px-6 text-lg font-bold text-white">₹<?= number_format($sale['total_amount'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        // Profile dropdown
        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');

        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            profileMenu.classList.add('hidden');
        });

        // Mobile menu
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
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