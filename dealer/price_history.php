<?php
require_once 'includes/auth_check.php';
    
    // Get product_id from URL
    $product_id = isset($_GET['id']) ? $_GET['id'] : 0;
    
    // Get product details
    $product_query = "SELECT p.*, c.company_name 
                     FROM products p 
                     LEFT JOIN companies c ON p.company_id = c.id 
                     WHERE p.id = '$product_id' AND p.dealer_id = {$_SESSION['rgt_logedin_user_dealer_id']}";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);
    
    if(!$product){
        header("Location: products.php");
        exit();
    }
    
    // Get price history
    $history_query = "SELECT * FROM product_price_history 
                     WHERE product_id = '$product_id' AND dealer_id = {$_SESSION['rgt_logedin_user_dealer_id']} 
                     ORDER BY effective_from ASC";
    $history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price History - <?php echo $product['product_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="products.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Price History</h1>
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
            
            <!-- Back Button -->
            <div class="mb-6">
                <a href="products.php" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Products</span>
                </a>
            </div>

            <!-- Product Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2"><?php echo $product['product_name']; ?></h2>
                        <p class="text-gray-600">Company: <span class="font-medium"><?php echo $product['company_name']; ?></span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 mb-1">Current Prices</p>
                        <p class="text-lg font-semibold text-primary">Purchase: ₹<?php echo number_format($product['base_price'], 2); ?></p>
                        <p class="text-lg font-semibold text-secondary">Selling: ₹<?php echo number_format($product['selling_price'], 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Price Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Price Trend</h3>
                <div class="h-96">
                    <canvas id="priceChart"></canvas>
                </div>
            </div>

            <!-- Price History Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Price History Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Base Price (₹)</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Selling Price (₹)</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Profit Margin</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Price Change</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            $prev_base_price = 0;
                            $prev_selling_price = 0;
                            $count = 0;
                            
                            // Reset pointer to read data again for table
                            mysqli_data_seek($history_result, 0);
                            
                            if(mysqli_num_rows($history_result) > 0){
                                while($row = mysqli_fetch_assoc($history_result)){
                                    $profit_margin = $row['selling_price'] - $row['base_price'];
                                    $profit_percentage = ($row['base_price'] > 0) ? (($profit_margin / $row['base_price']) * 100) : 0;
                                    
                                    // Calculate price change
                                    $base_change = 0;
                                    $selling_change = 0;
                                    if($count > 0){
                                        $base_change = $row['base_price'] - $prev_base_price;
                                        $selling_change = $row['selling_price'] - $prev_selling_price;
                                    }
                                    
                                    echo '<tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-6 text-sm text-gray-900">'.date('d M Y', strtotime($row['effective_from'])).'</td>
                                        <td class="py-4 px-6 text-sm font-medium text-gray-900">₹'.number_format($row['base_price'], 2).'</td>
                                        <td class="py-4 px-6 text-sm font-medium text-gray-900">₹'.number_format($row['selling_price'], 2).'</td>
                                        <td class="py-4 px-6 text-sm text-gray-900">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium">
                                                +₹'.number_format($profit_margin, 2).' ('.number_format($profit_percentage, 1).'%)
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-sm">';
                                    
                                    if($count == 0){
                                        echo '<span class="text-gray-500">Initial Price</span>';
                                    } else {
                                        if($base_change > 0 || $selling_change > 0){
                                            echo '<span class="inline-flex items-center gap-1 text-red-600">
                                                <i class="fas fa-arrow-up"></i>
                                                Base: ₹'.number_format(abs($base_change), 2).' | Selling: ₹'.number_format(abs($selling_change), 2).'
                                            </span>';
                                        } elseif($base_change < 0 || $selling_change < 0){
                                            echo '<span class="inline-flex items-center gap-1 text-green-600">
                                                <i class="fas fa-arrow-down"></i>
                                                Base: ₹'.number_format(abs($base_change), 2).' | Selling: ₹'.number_format(abs($selling_change), 2).'
                                            </span>';
                                        } else {
                                            echo '<span class="text-gray-500">No Change</span>';
                                        }
                                    }
                                    
                                    echo '</td></tr>';
                                    
                                    $prev_base_price = $row['base_price'];
                                    $prev_selling_price = $row['selling_price'];
                                    $count++;
                                }
                            } else {
                                echo '<tr><td colspan="5" class="py-8 px-6 text-center text-gray-500">No price history available.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        // Prepare data for chart
        <?php
        // Reset pointer to read data again for chart
        mysqli_data_seek($history_result, 0);
        
        $dates = [];
        $base_prices = [];
        $selling_prices = [];
        
        while($row = mysqli_fetch_assoc($history_result)){
            $dates[] = date('d M Y', strtotime($row['effective_from']));
            $base_prices[] = floatval($row['base_price']);
            $selling_prices[] = floatval($row['selling_price']);
        }
        ?>
        
        const dates = <?php echo json_encode($dates); ?>;
        const basePrices = <?php echo json_encode($base_prices); ?>;
        const sellingPrices = <?php echo json_encode($selling_prices); ?>;
        
        // Create chart
        const ctx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Base Price (₹)',
                        data: basePrices,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Selling Price (₹)',
                        data: sellingPrices,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                family: 'Inter, system-ui, sans-serif'
                            },
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₹' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toFixed(2);
                            },
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
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
    </script>

</body>
</html>