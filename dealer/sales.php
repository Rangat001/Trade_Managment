<?php
require_once 'includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - Dealer Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <!-- <a href="reports.php" class="flex items-center px-4 py-3 mb-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-chart-line w-5 mr-3"></i>
                <span class="font-medium">Reports</span>
            </a> -->
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
                    <h1 class="text-2xl font-semibold text-gray-900">Sales</h1>
                </div>

                <div class="relative">
                    <button id="profileDropdown" class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-full cursor-pointer hover:bg-gray-100 transition-colors">
                        <img src="https://ui-avatars.com/api/?name=Dealer+Admin&background=4F46E5&color=fff" 
                             alt="Profile" class="w-9 h-9 rounded-full">
                        <span class="font-medium text-gray-700 hidden sm:block">Dealer <?php  echo($_SESSION['rgt_logedin_user_role']); ?></span>
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
                <h2 class="text-2xl font-semibold text-gray-900">Sales Management</h2>
                <button onclick="window.location.href='sales_entry.php'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    <span>New Sale</span>
                </button>
            </div>

            <!-- Sales Table -->
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
                                <th>No.</th>
                                <th>Bill No.</th>
                                <th>Date</th>
                                <th>Billing Type</th>
                                <th>Items Count</th>
                                <th>Total Qty</th>
                                <th>Bill Amount</th>
                                <th>Payable Amount</th>
                                <th>Profit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                                $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];

                                $sql = "
                                    SELECT 
                                        s.id AS sale_id,
                                        s.sale_date,
                                        s.billing_type,
                                        s.bill_amount,
                                        s.profit,
                                        s.total_amount,
                                        s.mobile_no,
                                        COUNT(si.id) AS item_count,
                                        SUM(si.quantity) AS total_quantity
                                    FROM sales s
                                    LEFT JOIN sale_items si ON si.sale_id = s.id
                                    WHERE s.dealer_id = ? AND s.is_deleted = 0
                                    GROUP BY s.id
                                    ORDER BY s.sale_date DESC, s.id DESC
                                ";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $dealer_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $sr = 1;
                                while ($row = $result->fetch_assoc()): 
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- No. -->
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    <?= $sr++ ?>
                                </td>
                                
                                <!-- Bill No. -->
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <?= 'BL' . str_pad($row['sale_id'], 6, '0', STR_PAD_LEFT) ?>
                                </td>
                                
                                <!-- Date -->
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <?= date("d/m/Y", strtotime($row['sale_date'])) ?>
                                </td>

                                <!-- Billing Type -->
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <?= htmlspecialchars($row['billing_type']) ?>
                                </td>
                                
                                <!-- Items Count -->
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <?= $row['item_count'] ?> item(s)
                                </td>

                                <!-- Total Quantity -->
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <?= $row['total_quantity'] ?>
                                </td>

                                <!-- Bill Amount -->
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    ₹<?= number_format($row['bill_amount'], 2) ?>
                                </td>

                                <!-- Payable Amount -->
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                    ₹<?= number_format($row['total_amount'], 2) ?>
                                </td>


                                <!-- Profit -->
                                <td class="py-4 px-6 text-sm font-medium <?= $row['profit'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    ₹<?= number_format($row['profit'], 2) ?>
                                </td>

                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <button onclick="window.location.href='sale_details.php?id=<?= $row['sale_id'] ?>'" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <?php
                                        $mobile_no = preg_replace('/\D+/', '', (string)($row['mobile_no'] ?? ''));
                                    ?>
                                    <button onclick="shareBillToWhatsapp(<?= (int)$row['sale_id'] ?>, '<?= htmlspecialchars($mobile_no, ENT_QUOTES) ?>')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors"
                                            title="Share bill on WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>

                                    <button onclick="deleteSale(<?= $row['sale_id'] ?>)" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i> 

                                    </button>

                                    
                                </td>
                            </tr>
                        <?php endwhile; ?>
                            
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Add Sale Modal -->
    <div id="addSaleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">New Sale</h3>
                <button onclick="closeAddSaleModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <form action="add_sale.php" method="POST" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Product *</label>
                        <select name="product_id" id="productSelect" required onchange="updateProductDetails()"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            <option value="">-- Select Product --</option>
                            <option value="1" data-stock="150" data-price="65.00">Rice (1kg) - Stock: 150</option>
                            <option value="2" data-stock="120" data-price="58.00">Wheat Flour (1kg) - Stock: 120</option>
                            <option value="3" data-stock="80" data-price="55.00">Sugar (1kg) - Stock: 80</option>
                            <option value="4" data-stock="65" data-price="145.00">Cooking Oil (1L) - Stock: 65</option>
                            <option value="5" data-stock="45" data-price="105.00">Tea Powder (250g) - Stock: 45</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Stock</label>
                        <input type="text" id="availableStock" disabled 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹)</label>
                        <input type="text" id="sellingPrice" disabled 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" min="1" required oninput="calculateTotal()"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                        <input type="text" id="totalAmount" disabled 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed font-semibold">
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeAddSaleModal()" 
                            class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg shadow-lg shadow-green-500/30 hover:shadow-xl transition-all">
                        Complete Sale
                    </button>
                </div>
            </form>
        </div>
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

        // Delete sale function
        function deleteSale(saleId) {
            if (!confirm("Are you sure you want to delete this sale? This action cannot be undone.")) {
                return;
            }

            fetch("delete_sale.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "sale_id=" + saleId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Sale deleted successfully!");
                    location.reload();
                } else {
                    alert(data.message || "Failed to delete sale");
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Something went wrong");
            });
        }

        function openAddSaleModal() {
            document.getElementById('addSaleModal').classList.remove('hidden');
        }
        
        function closeAddSaleModal() {
            document.getElementById('addSaleModal').classList.add('hidden');
        }
        
        document.getElementById('addSaleModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddSaleModal();
        });
        
        function updateProductDetails() {
            const select = document.getElementById('productSelect');
            const selectedOption = select.options[select.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            const price = selectedOption.getAttribute('data-price');
            
            document.getElementById('availableStock').value = stock || '';
            document.getElementById('sellingPrice').value = price ? `₹${price}` : '';
            document.getElementById('quantity').max = stock || 1;
            calculateTotal();
        }
        
        function calculateTotal() {
            const select = document.getElementById('productSelect');
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const quantity = parseInt(document.getElementById('quantity').value) || 0;
            document.getElementById('totalAmount').value = `₹${(price * quantity).toFixed(2)}`;
        }
        
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

        function shareBillToWhatsapp(saleId, mobileNo) {
            if (!mobileNo) {
                alert('Mobile number is not available for this sale.');
                return;
            }

            const cleanPhone = String(mobileNo).replace(/\D/g, '');
            const billUrl = `${window.location.origin}${window.location.pathname.replace('sales.php', 'print_bill.php')}?sale_id=${saleId}`;
            const message = `Hello, here is your bill: ${billUrl}`;
            const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;

            window.open(whatsappUrl, '_blank');
        }

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
