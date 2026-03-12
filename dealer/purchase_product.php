<?php
require_once 'includes/auth_check.php';
    
if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    die("Unauthorized");
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];

/* Fetch companies */
$companies = [];

$sql = "
    SELECT 
        c.id,
        c.company_name,
        COALESCE(
            SUM(
                CASE 
                    WHEN ct.type = 'CREDIT' THEN ct.amount
                    ELSE 0
                END
            ), 0
        ) AS balance
    FROM companies c
    LEFT JOIN company_transactions ct
        ON ct.company_id = c.id
        AND ct.dealer_id = ?
    WHERE c.dealer_id = ?
    GROUP BY c.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $dealer_id, $dealer_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $companies[] = $row;
}

$stmt->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Goods - Dealer Panel</title>
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
                    <h1 class="text-2xl font-semibold text-gray-900">Purchase Goods</h1>
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
        <main class="p-8 max-w-7xl">
            
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Record Goods Received</h2>
                <p class="text-gray-500 mt-1">Record goods received from company and payment details</p>
            </div>

            <!-- Purchase Form -->
            <form action="process_purchase.php" method="POST" id="purchaseForm">
                
                <!-- Section 1: Company Selection -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-building text-primary"></i>
                        Select Company
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>

                            <label class="text-sm font-medium mb-2 block">Company Name *</label>
                            <select name="company_id" id="companySelect" required
                                    onchange="loadCompanyProducts();loadCompanyBalance()"
                                    class="w-full px-4 py-3 border rounded-lg">
                                <option value="">-- Select Company --</option>
                                <?php foreach ($companies as $c) { ?>
                                    <option value="<?= $c['id'] ?>"
                                            data-balance="<?= $c['balance'] ?>">
                                        <?= htmlspecialchars($c['company_name']) ?>
                                    </option>
                                <?php } ?>
                            </select>

                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Current Balance with Company
                            </label>
                            <div id="companyBalancePreview" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-base font-medium text-gray-500">
                                Select a company to see balance
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Purchase Order Details -->
                <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-file-invoice text-indigo-600"></i> Purchase Details
                    </h3>
                
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date *</label>
                            <input type="date" name="purchase_date" required
                                   value="<?= date('Y-m-d') ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bill / Reference</label>
                            <input type="text" name="bill_number"
                                   placeholder="Bill Number"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Status *</label>
                            <select name="purchase_status" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="REQUESTED">Requested</option>
                                <option value="RECEIVED" selected>Received</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ================= PRODUCTS ================= -->
                <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-boxes text-indigo-600"></i> Goods Received
                    </h3>
                    <button type="button" onclick="addProductRow()"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                        + Add Product
                    </button>
                </div>

                <table class="w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="productsTableBody">
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-400">
                        Add product to begin
                    </td>
                </tr>
                </tbody>
                </table>
                </div>

                <!-- ================= PAYMENT ================= -->
                <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" id="paymentToggle" onchange="togglePayment()">
                        Making payment now?
                    </label>

                    <div id="paymentFields" class="hidden mt-4 grid md:grid-cols-3 gap-4">
                        <input type="number" name="amount_paid" id="amountPaid" value="0"
                               class="border px-4 py-3 rounded-lg">
                        <select name="payment_mode" class="border px-4 py-3 rounded-lg">
                            <option>CASH</option>
                            <option>UPI</option>
                            <option>BANK</option>
                            <option>CHEQUE</option>
                        </select>
                        <input type="date" name="payment_date"
                               value="<?= date('Y-m-d') ?>"
                               class="border px-4 py-3 rounded-lg">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 text-white py-3 rounded-xl">
                    Save Purchase
                </button>

            </form>

        </main>
    </div>

    <script>
        let rowIndex = 0;
        let products = [];

        /* Fetch products by company */
        function loadCompanyProducts() {
            const companyId = companySelect.value;
            fetch(`get_products.php?company_id=${companyId}`)
                .then(r => r.json())
                .then(d => products = d);
        }

                                     /* Company balance */

        // function loadCompanyBalance() {
        //     const opt = companySelect.selectedOptions[0];
        //     const bal = parseFloat(opt.dataset.balance || 0);
        //     companyBalancePreview.innerText =
        //         bal < 0 ? `Debit -₹${Math.abs(bal)}` :
        //         bal > 0 ? `Credit +₹${bal}` :
        //         `Account settled ₹${bal}`;
        // }

        function loadCompanyBalance() {
            const opt = companySelect.selectedOptions[0];
            const bal = parseFloat(opt.dataset.balance || 0);
            const preview = document.getElementById('companyBalancePreview');

            if (bal < 0) {
                preview.innerHTML = `<span class="text-red-600">Debit -₹${Math.abs(bal)}</span>`;
            } else if (bal > 0) {
                preview.innerHTML = `<span class="text-green-600">Credit +₹${bal}</span>`;
            } else {
                preview.innerHTML = `<span class="text-gray-700">₹${bal}</span>`;
            }
        }

        /* Add row */
        function addProductRow() {
            const tbody = document.getElementById('productsTableBody');
            if (tbody.children.length === 1 && tbody.innerText.includes('Add product'))
                tbody.innerHTML = '';
        
            rowIndex++;
            const tr = document.createElement('tr');
            tr.innerHTML = `
        <td>
        <select name="products[${rowIndex}][product_id]"
                onchange="setPrice(this)"
                class="border px-3 py-2 rounded w-full">
        <option value="">Select</option>
        ${products.map(p => `<option value="${p.id}" data-price="${p.base_price}">${p.product_name}</option>`).join('')}
        </select>
        </td>
        <td><input name="products[${rowIndex}][base_price]" readonly class="price border px-3 py-2 rounded"></td>
        <td><input name="products[${rowIndex}][quantity]" oninput="calcRow(this)" class="qty border px-3 py-2 rounded"></td>
        <td><input class="total border px-3 py-2 rounded" readonly></td>
        <td><button type="button" onclick="this.closest('tr').remove()">❌</button></td>`;
            tbody.appendChild(tr);
        }

        function setPrice(sel){
            const price = sel.selectedOptions[0].dataset.price;
            sel.closest('tr').querySelector('.price').value = price;
        }
        function calcRow(inp){
            const tr = inp.closest('tr');
            const p = tr.querySelector('.price').value || 0;
            tr.querySelector('.total').value = (p * inp.value).toFixed(2);
        }
        function togglePayment(){
            paymentFields.classList.toggle('hidden');
        }
    </script>

</body>
</html>