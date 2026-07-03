<?php
require_once 'includes/auth_check.php';
$pageTitle  = 'Products';
$activePage = 'products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">

    <!-- Page Content -->
    <main class="p-4 md:p-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-[var(--text)]">Products Management</h2>
            <button onclick="openAddProductModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i>
                <span>Add Product</span>
            </button>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="p-6">
        
                <div class="overflow-x-auto">
                    <table class="table datanew w-full min-w-[900px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">No.</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Product Name</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Category</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Company Name</th>
                                
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Assurance</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Validity</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Base Price</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">selling Price</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">current stock</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">HSN Code</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">GST (%)</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Barcode</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
                            <?php
                                $products_query = "SELECT p.*, c.company_name 
                                                  FROM products p 
                                                  LEFT JOIN companies c ON p.company_id = c.id 
                                                  WHERE p.dealer_id = {$_SESSION['rgt_logedin_user_dealer_id']}
                                                  ORDER BY p.category, p.product_name";
                                $result = mysqli_query($conn, $products_query);

                                if(mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-6 text-sm font-medium text-gray-900"><?php echo $row['id']; ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900">
                                                <?php if (!empty($row['category'])): ?>
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                                    <?php echo htmlspecialchars($row['category']); ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="text-gray-400 text-xs">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['company_name']; ?></td>
                                            
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['assurance'] ; ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['VALIDITY'] ; ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900">₹<?php echo number_format($row['base_price'], 2); ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900">₹<?php echo number_format($row['selling_price'], 2); ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['current_stock']; ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['HSN'] ; ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['GST'] ; ?></td>
                                            <td class="py-4 px-6 text-sm text-gray-900"><?php echo $row['Barcode'] ; ?></td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="openEditProductModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>', <?php echo $row['base_price']; ?>, <?php echo $row['selling_price']; ?>, <?php echo $row['current_stock']; ?>, '<?php echo htmlspecialchars($row['category'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['HSN'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['Barcode'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['GST'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['assurance'] ?? 'None', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['VALIDITY'] ?? '', ENT_QUOTES); ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <input required  type="text" name="category" placeholder="e.g. Electronics, Grocery, Clothing…"
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">HSN CODE</label>
                    <input type="text" name="hsn_code" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode No</label>
                    <input type="number" name="barcode_no" min="0" steps="1" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">GST (%)</label>
                    <input type="number" name="gst" min="0" steps="1" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (₹)</label>
                    <input type="number" name="purchase_price" step="10" min="0" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹)</label>
                    <input type="number" name="selling_price" step="10" min="0" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ASSURANCE</label>
                    <select name="assurance" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <option value="">Select Type</option>
                        <option value="None" selected>None</option>
                        <option value="Guarantee">Guarantee</option>
                        <option value="Warranty">Warranty</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">MONTHS</label>
                    <input type="number" name="validity" step="1" min="0" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Initial Quantity</label>
                    <input type="number" name="initial_quantity" min="0" value="0" disabled
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <input  required type="text" name="category" id="edit_category" value=""
                           placeholder="e.g. Electronics, Grocery, Clothing…"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                //hsn

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">HSN Code</label>
                    <input  required type="text" name="hsn" id="edit_hsn" value=""
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                // barcode

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                    <input  required type="number" name="barcode" id="edit_barcode" value=""
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                //gst

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">gst</label>
                    <input  required type="number" name="gst" id="edit_gst" value=""
                           placeholder="e.g. 18"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                //assurance

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ASSURANCE</label>
                    <select name="assurance" required id="edit_assurance"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <option value="">Select Type</option>
                        <option value="None" selected>None</option>
                        <option value="Guarantee">Guarantee</option>
                        <option value="Warranty">Warranty</option>
                    </select>

                </div>

                // validity

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Validity(Months)</label>
                    <input  required type="number" name="validity" id="edit_validity" value=""
                           placeholder="e.g. 6"
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

<?php require_once 'includes/footer.php'; ?>
<script>
    // Modal Functions
    function openAddProductModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }

    function closeAddProductModal() {
        document.getElementById('addProductModal').classList.add('hidden');
    }

    function openEditProductModal(productId, productName, purchasePrice, sellingPrice, currentStock, category, hsn, barcode, gst, assurance, validity) {
        document.getElementById('edit_product_id').value = productId;
        document.getElementById('edit_product_name').value = productName;
        document.getElementById('edit_purchase_price').value = purchasePrice;
        document.getElementById('edit_selling_price').value = sellingPrice;
        document.getElementById('edit_current_stock').value = currentStock;
        document.getElementById('edit_category').value = category || '';
        document.getElementById('edit_hsn').value = hsn || '';
        document.getElementById('edit_barcode').value = barcode || '';
        document.getElementById('edit_gst').value = gst || '';
        document.getElementById('edit_assurance').value = assurance || 'None';
        document.getElementById('edit_validity').value = validity || '';
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
</script>
</body>
</html>
