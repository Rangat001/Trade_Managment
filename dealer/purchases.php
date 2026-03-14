<?php
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases - Dealer Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../asset/css/bootstrap.min.css">

    <link rel="stylesheet" href="../asset/plugins/icons/ionic/ionicons.css">

    <link rel="stylesheet" href="../asset/plugins/icons/feather/feather.css">

    <link rel="stylesheet" href="../asset/css/animate.css">

    <link rel="stylesheet" href="../asset/plugins/select2/css/select2.min.css">

    <link rel="stylesheet" href="../asset/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="../asset/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../asset/plugins/fontawesome/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

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
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Purchase Entry</h2>
                <button onclick="window.location.href='purchase_product.php'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Add Purchase</span>
                </button>
            </div>

            <!-- Purchases Table -->
            <div class="card">
            <div class="card-body">
                <div class="table-top">
                            <div class="search-set">
                                <div class="search-input">
                                    <a class="btn btn-searchset"><img src="../asset/img/icons/search-white.svg"
                                            alt="img"></a>
                                </div>
                            </div>
                </div>
            
                <div class="table-responsive">
                    <table class="table  datanew">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th>Date</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Product Amount</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>   
                        </thead>
                    <div class="table-responsive">
                        <tbody class="divide-y divide-gray-200">
                            <?php 
                                    $psql = "SELECT 
                                                po.id AS order_id,
                                                po.order_date,
                                                pr.product_name,
                                                poi.quantity,
                                                poi.base_price,
                                                poi.total_price,
                                                po.status,

                                                COALESCE(
                                                    SUM(
                                                        CASE 
                                                            WHEN ct.type = 'DEBIT' THEN ct.amount
                                                            ELSE 0
                                                        END 
                                                    ), 0
                                                ) AS paid_amount

                                            FROM purchase_order_items poi

                                            JOIN purchase_orders po 
                                                ON po.id = poi.order_id

                                            JOIN products pr 
                                                ON pr.id = poi.product_id

                                            LEFT JOIN company_transactions ct 
                                                ON ct.order_id = po.id

                                            WHERE po.dealer_id = ?
                                            GROUP BY poi.id
                                            ORDER BY po.order_date DESC ";
                                    $stmt = $conn->prepare($psql);
                                    $stmt->bind_param("i", $_SESSION['rgt_logedin_user_dealer_id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) { 
                                       ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                

                                <td class="py-4 px-6 text-sm text-gray-500">
                                   <?= date("d/m/Y", strtotime($row['order_date'])) ?>  
                                </td>

                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </td>

                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <?= $row['quantity'] ?>
                                </td>

                                <td class="py-4 px-6 text-sm text-gray-900">
                                    ₹<?= number_format($row['base_price'], 2) ?>
                                </td>

                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    ₹<?= number_format($row['total_price'], 2) ?>
                                </td>

                                <td class="py-4 px-6 text-sm font-medium text-green-600">
                                    ₹<?= number_format($row['paid_amount'], 2) ?>
                                </td>

                                <!-- ✅ STATUS COLUMN -->
                                <td class="py-4 px-6 text-sm font-medium">
                                    <?php
                                        $status = $row['status'];

                                        $statusClass = match ($status) {
                                            'RECEIVED'  => 'text-green-600',
                                            'REQUESTED' => 'text-yellow-600',
                                            'CANCELLED' => 'text-red-600',
                                            default     => 'text-gray-500',
                                        };
                                    ?>

                                    <span class="<?= $statusClass ?>">
                                        <?= $status ?>
                                    </span>

                                    <?php if ($status === 'REQUESTED'): ?>
                                        <button 
                                            onclick="markAsReceived(<?= $row['order_id'] ?>)" 
                                            class="ml-2 px-2 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                                            Mark as Received
                                        </button>
                                       
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <?php } ?>
                        </tbody>
                    </div>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="../asset/js/jquery-3.6.0.min.js"></script>

    <script src="../asset/js/feather.min.js"></script>

    <script src="../asset/js/jquery.slimscroll.min.js"></script>

    <script src="../asset/js/jquery.dataTables.min.js"></script>
    <script src="../asset/js/dataTables.bootstrap4.min.js"></script>

    <script src="../asset/js/bootstrap.bundle.min.js"></script>

    <script src="../asset/plugins/select2/js/select2.min.js"></script>

    <script src="../asset/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="../asset/plugins/sweetalert/sweetalerts.min.js"></script>

    <script src="../asset/js/script.js"></script>

    <script>

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

                                                // Mark as Received Function
        // Mark as Received Function
function markAsReceived(orderId) {
    if (!confirm("Mark this order as RECEIVED?")) return;
                                        
    fetch("mark_order_received.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "order_id=" + orderId
    })
    .then(res => {
        console.log("Response status:", res.status);
        console.log("Response ok:", res.ok);
        return res.text(); // Get raw text first
    })
    .then(text => {
        console.log("Raw response:", text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || "Failed to update order");
            }
        } catch(e) {
            console.error("JSON parse error:", e);
            alert("Server returned invalid response: " + text);
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
        alert("Network error: " + err.message);
    });
}

    

    </script>

</body>
</html>
