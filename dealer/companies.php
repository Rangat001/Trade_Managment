<?php
    require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['rgt_logedin_user_id']) && (trim ($_SESSION['rgt_logedin_user_id']) !== '')){
        $user_id = $_SESSION['rgt_logedin_user_id'];
        $user_role = $_SESSION['rgt_logedin_user_role'];
        if($user_role != "ADMIN"){
            header("Location: ../404.php");
        }
    }else{
        header("Location: ../sign-in.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies - Dealer Panel</title>
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
            <a href="companies.php" class="flex items-center px-4 py-3 mb-2 text-white bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg shadow-indigo-500/30">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Companies</h1>
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
                <h2 class="text-2xl font-semibold text-gray-900">Companies Management</h2>
                <button onclick="openAddProductModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Add Companies</span>
                </button>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">No.</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">dealer_id</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Company Name</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Owner Name</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                                
                                $result = mysqli_query($conn, "SELECT * FROM companies where dealer_id = {$_SESSION['rgt_logedin_user_dealer_id']}");
                                while($row = mysqli_fetch_assoc($result)){
                                    echo '<tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-6 text-sm font-medium text-gray-900">'.$row["id"].'</td>
                                            <td class="py-4 px-6 text-sm text-gray-900">'.$row["dealer_id"].'</td>
                                            <td class="py-4 px-6 text-sm text-gray-900">'.$row["company_name"].'</td>
                                            <td class="py-4 px-6 text-sm text-gray-900">'.$row["contact_person"].'</td>
                                            <td class="py-4 px-6 text-sm text-gray-900">'.$row["phone"].'</td>
                                            <td class="py-4 px-6 text-sm text-gray-900">'.$row["email"].'</td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="openEditProductModal(
                                                                        '.$row['id'].',
                                                                        \''.addslashes(htmlspecialchars($row['company_name'], ENT_QUOTES)).'\',
                                                                        \''.addslashes(htmlspecialchars($row['contact_person'], ENT_QUOTES)).'\',
                                                                        \''.addslashes(htmlspecialchars($row['phone'], ENT_QUOTES)).'\',
                                                                        \''.addslashes(htmlspecialchars($row['email'], ENT_QUOTES)).'\'
                                                                    )" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                </div>
                                            </td>
                                        </tr>';
                                }
                            ?>
                            <!-- <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">Rice (1kg)</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹50.00</td>
                                <td class="py-4 px-6 text-sm text-gray-900">₹65.00</td>
                                <td class="py-4 px-6 text-sm text-gray-900">150</td>
                                <td class="py-4 px-6 text-sm text-gray-900">150</td>

                                <td class="py-4 px-6">
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <button onclick="openEditProductModal()" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Add New Company</h3>
                <button onclick="closeAddProductModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <form action="add_company.php" method="POST" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                        <input type="text" name="company_name" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person Name</label>
                        <input type="text" name="contact_person_name" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone No.</label>
                        <input type="number" name="phone_no" pattern="[0-9]{10}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
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

                                                            <!-- Edit Company Modal -->

    <div id="editProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Edit Company</h3>
                <button onclick="closeEditProductModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            

            <form action="edit_company.php" method="POST" class="p-6">
                <input type="hidden" name="comp_id" value="">
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                        <input type="text" name="owner_name" value="Rice (1kg)" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Owner Contact</label>
                        <input type="text" name="owner_contact" value="1234567890" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="edit_email" value="" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    
                </div>
                
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditProductModal()" 
                            class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                        Update
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        // Modal Functions
        function openAddProductModal() {
            document.getElementById('addProductModal').classList.remove('hidden');
        }
        
        function closeAddProductModal() {
            document.getElementById('addProductModal').classList.add('hidden');
        }
        
        function openEditProductModal(id, companyName, contactPerson, phone, email) {
            document.getElementById('editProductModal').classList.remove('hidden');
            document.querySelector('input[name="comp_id"]').value = id;
            document.querySelector('input[name="owner_name"]').value = contactPerson;
            // document.querySelector('input[name="owner_contact"]').value = contactPerson;
            document.querySelector('input[name="owner_contact"]').value = phone;
            document.querySelector('input[name="edit_email"]').value = email;
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
