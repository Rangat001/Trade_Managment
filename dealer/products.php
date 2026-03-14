<?php
require_once 'includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Dealer Panel</title>
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
            <a href="products.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
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
                <h2 class="text-2xl font-semibold text-gray-900">Products Management</h2>
                <button onclick="openAddProductModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </button>
            </div>

            <!-- Products Table -->
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
                                <th>dealer_id</th>
                                <th>Company Name</th>
                                <th>Product Name</th>
                                <th>Base Price</th>
                                <th>selling Price</th>
                                <th>current stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                    $products_query = "SELECT p.*, c.company_name 
                                                      FROM products p 
                                                      LEFT JOIN companies c ON p.company_id = c.id 
                                                      WHERE p.dealer_id = {$_SESSION['rgt_logedin_user_dealer_id']}";
                                    $result = mysqli_query($conn, $products_query);

                                    if(mysqli_num_rows($result) > 0){
                                        while($row = mysqli_fetch_assoc($result)){
                                            ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="py-4 px-6 text-sm font-medium text-gray-900"><?php echo $row['id']; ?></td>
                                                <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['dealer_id']; ?></td>
                                                <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['company_name']; ?></td>
                                                <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['product_name']; ?></td>
                                                <td class="py-4 px-6 text-sm text-gray-900">₹<?php echo number_format($row['base_price'], 2); ?></td>
                                                <td class="py-4 px-6 text-sm text-gray-900">₹<?php echo number_format($row['selling_price'], 2); ?></td>
                                                <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['current_stock']; ?></td>
                                                <td class="py-4 px-6">
                                                    <div class="flex items-center gap-2">
                                                        <button onclick="openEditProductModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>', <?php echo $row['base_price']; ?>, <?php echo $row['selling_price']; ?>, <?php echo $row['current_stock']; ?>)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button onclick="window.location.href='price_history.php?id=<?php echo $row['id']; ?>'" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors">
                                                            <i class="fas fa-chart-line"></i> Price History
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td class="py-8 px-6 text-center text-gray-500">
                                                No products found. Click "Add Product" to get started.
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </tbody>
                    </table>
                </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Add New Product</h3>
                <button onclick="closeAddProductModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <form action="add_product.php" method="POST" class="p-6">
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="product_name" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                        <select name="company_id" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            <option value="">Select a company</option>
                            <?php
                                $company_query = mysqli_query($conn, "SELECT id, company_name FROM companies WHERE dealer_id = {$_SESSION['rgt_logedin_user_dealer_id']}");
                                while($company = mysqli_fetch_assoc($company_query)){
                                    echo '<option value="'.$company['id'].'">'.$company['company_name'].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (₹)</label>
                        <input type="number" name="purchase_price" step="0.01" min="0" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹)</label>
                        <input type="number" name="selling_price" step="0.01" min="0" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Quantity</label>
                        <input type="number" name="initial_quantity" min="0"  value="0" disabled 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeAddProductModal()" 
                            class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

                                                                <!-- Edit Product Modal -->
    <div id="editProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Edit Product</h3>
                <button onclick="closeEditProductModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
                <form action="edit_product.php" method="POST" class="p-6">
                    <input type="hidden" name="product_id" id="edit_product_id" value="">

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                            <input type="text" name="product_name" id="edit_product_name" value="" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (₹) *</label>
                            <input type="number" name="purchase_price" id="edit_purchase_price" value="" step="0.01" min="0" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹) *</label>
                            <input type="number" name="selling_price" id="edit_selling_price" value="" step="0.01" min="0" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                            <input type="number" id="edit_current_stock" value="" disabled 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">Stock can only be updated via Purchases and Sales</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeEditProductModal()" 
                                class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                            Update Product
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
        function openAddProductModal() {
            document.getElementById('addProductModal').classList.remove('hidden');
        }
        
        function closeAddProductModal() {
            document.getElementById('addProductModal').classList.add('hidden');
        }
        
        function openEditProductModal(productId, productName, purchasePrice, sellingPrice, currentStock) {
            // Populate form fields with current product data
            document.getElementById('edit_product_id').value = productId;
            document.getElementById('edit_product_name').value = productName;
            document.getElementById('edit_purchase_price').value = purchasePrice;
            document.getElementById('edit_selling_price').value = sellingPrice;
            document.getElementById('edit_current_stock').value = currentStock;

            // Show the modal
            document.getElementById('editProductModal').classList.remove('hidden');
        }
        
        function closeEditProductModal() {
            document.getElementById('editProductModal').classList.add('hidden');
        }
        
        // Close modal on outside click
        document.getElementById('addProductModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddProductModal();
        });
        
        document.getElementById('editProductModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditProductModal();
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
