<?php
require_once 'includes/auth_check.php';
$pageTitle       = 'Dashboard';
$activePage      = 'dashboard';
$showEditProfile = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">
    <main class="p-6 md:p-8">

        <!-- ── Stat Cards ──────────────────────────────────────── -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Total Expense -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0">
                        <img src="../asset/img/icons/expense.png" alt="Total Expense" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <?php
                        $sale_stmt = $conn->prepare("SELECT SUM(amount) AS expense FROM company_transactions WHERE dealer_id = ? AND type = 'DEBIT'");
                        $sale_stmt->bind_param("i", $dealer_id); $sale_stmt->execute();
                        $sale_data = $sale_stmt->get_result()->fetch_assoc();
                        ?>
                        <h3 class="text-2xl font-bold text-[var(--text)]">₹ <?php echo number_format($sale_data['expense'] ?? 0); ?></h3>
                        <p class="text-sm font-medium text-[var(--subtext)] mt-0.5">Total Expense</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0">
                        <img src="../asset/img/icons/revenue.png" alt="Total Revenue" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <?php
                        $sale_stmt = $conn->prepare("SELECT SUM(total_amount) AS revenue FROM sales WHERE dealer_id = ?");
                        $sale_stmt->bind_param("i", $dealer_id); $sale_stmt->execute();
                        $sale_data = $sale_stmt->get_result()->fetch_assoc();
                        ?>
                        <h3 class="text-2xl font-bold text-[var(--text)]">₹ <?php echo number_format($sale_data['revenue'] ?? 0); ?></h3>
                        <p class="text-sm font-medium text-[var(--subtext)] mt-0.5">Total Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0">
                        <img src="../asset/img/icons/sales.png" alt="Total Sales" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <?php
                        $sale_stmt = $conn->prepare("SELECT COUNT(*) AS total_sales FROM sales s INNER JOIN sale_items si ON si.sale_id = s.id WHERE s.dealer_id = ?");
                        $sale_stmt->bind_param("i", $dealer_id); $sale_stmt->execute();
                        $sale_data = $sale_stmt->get_result()->fetch_assoc();
                        ?>
                        <h3 class="text-2xl font-bold text-[var(--text)]"><?php echo $sale_data['total_sales'] ?? 0; ?></h3>
                        <p class="text-sm font-medium text-[var(--subtext)] mt-0.5">Total Sales</p>
                    </div>
                </div>
            </div>

            <!-- Total Profit -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0">
                        <img src="../asset/img/icons/profit.png" alt="Total Profit" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <?php
                        $sale_stmt = $conn->prepare("SELECT SUM(profit) AS total_profit FROM sales WHERE dealer_id = ?");
                        $sale_stmt->bind_param("i", $dealer_id); $sale_stmt->execute();
                        $sale_data = $sale_stmt->get_result()->fetch_assoc();
                        ?>
                        <h3 class="text-2xl font-bold text-[var(--text)]">₹ <?php echo number_format($sale_data['total_profit'] ?? 0); ?></h3>
                        <p class="text-sm font-medium text-[var(--subtext)] mt-0.5">Total Profit</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- ── Dashboard Tables ────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Table 1: Top 5 Least Stock -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
                <div class="px-6 py-5 border-b border-[var(--border)] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-[var(--text)]">Top 5 Least(Qty) Products</h3>
                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Low Stock</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Product</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Company</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Qty</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php
                            $sale_stmt = $conn->prepare("SELECT p.product_name, p.current_stock, p.base_price, c.company_name FROM products p JOIN companies c ON p.company_id = c.id WHERE p.dealer_id = ? ORDER BY current_stock ASC LIMIT 5");
                            $sale_stmt->bind_param("i", $dealer_id); $sale_stmt->execute();
                            $sale_result = $sale_stmt->get_result();
                            if ($sale_result->num_rows === 0): ?>
                            <tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]">No products found</td></tr>
                            <?php else: while ($row = $sale_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-sm font-medium text-[var(--text)]"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--subtext)]"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?= $row['current_stock'] <= 5 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= $row['current_stock'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-[var(--text)]">₹ <?= number_format($row['base_price']) ?></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: 5 Recent Purchases -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
                <div class="px-6 py-5 border-b border-[var(--border)] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-[var(--text)]">5 Recent Purchases</h3>
                    <span class="text-xs font-medium text-[var(--subtext)] bg-gray-100 px-2.5 py-1 rounded-full">Latest</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Company</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Product</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Qty</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Amount</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php
                            $purchase_stmt = $conn->prepare("SELECT c.company_name, p.product_name, pu.quantity, pu.total_price, po.order_date FROM purchase_orders po JOIN purchase_order_items pu ON po.id = pu.order_id JOIN products p ON pu.product_id = p.id JOIN companies c ON p.company_id = c.id WHERE po.dealer_id = ? ORDER BY po.order_date DESC LIMIT 5");
                            $purchase_stmt->bind_param("i", $dealer_id); $purchase_stmt->execute();
                            $purchase_result = $purchase_stmt->get_result();
                            if ($purchase_result->num_rows === 0): ?>
                            <tr><td colspan="5" class="py-8 text-center text-sm text-[var(--subtext)]">No purchases found</td></tr>
                            <?php else: while ($row = $purchase_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-sm font-medium text-[var(--text)]"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--subtext)]"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--text)]"><?= $row['quantity'] ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--text)]">₹ <?= number_format($row['total_price']) ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--subtext)] whitespace-nowrap"><?= date('d M Y', strtotime($row['order_date'])) ?></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 3: Top 5 Best Seller -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
                <div class="px-6 py-5 border-b border-[var(--border)] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-[var(--text)]">Top 5 Best Seller</h3>
                    <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">Top Selling</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">#</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Company</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Product</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Qty Sold</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php
                            $purchase_stmt = $conn->prepare("SELECT p.product_name, c.company_name, SUM(si.quantity) AS total_qty_sold FROM sale_items si JOIN sales s ON si.sale_id = s.id JOIN products p ON si.product_id = p.id JOIN companies c ON p.company_id = c.id WHERE s.dealer_id = ? GROUP BY p.id, p.product_name, c.company_name ORDER BY total_qty_sold DESC LIMIT 5");
                            $purchase_stmt->bind_param("i", $dealer_id); $purchase_stmt->execute();
                            $purchase_result = $purchase_stmt->get_result();
                            if ($purchase_result->num_rows === 0): ?>
                            <tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]">No sales data found</td></tr>
                            <?php
                            else:
                                $rank = 1;
                                while ($row = $purchase_result->fetch_assoc()):
                                    $rankColors = ['bg-yellow-100 text-yellow-700','bg-gray-100 text-gray-600','bg-orange-100 text-orange-700'];
                                    $rankColor  = $rankColors[$rank-1] ?? 'bg-gray-50 text-gray-500';
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold <?= $rankColor ?>">
                                        <?= $rank ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm font-medium text-[var(--text)]"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--subtext)]"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="py-3 px-4 text-sm font-semibold text-[var(--primary)]"><?= number_format($row['total_qty_sold']) ?></td>
                            </tr>
                            <?php $rank++; endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 4: Recent Selling -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
                <div class="px-6 py-5 border-b border-[var(--border)] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-[var(--text)]">Recent Selling</h3>
                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Latest</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Bill No.</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Date</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Product</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wide">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php
                            $purchase_stmt = $conn->prepare("SELECT s.id AS sale_id, p.product_name, si.quantity, s.sale_date FROM sales s JOIN sale_items si ON si.sale_id = s.id JOIN products p ON si.product_id = p.id WHERE s.dealer_id = ? ORDER BY s.sale_date DESC LIMIT 5");
                            $purchase_stmt->bind_param("i", $dealer_id); $purchase_stmt->execute();
                            $purchase_result = $purchase_stmt->get_result();
                            if ($purchase_result->num_rows === 0): ?>
                            <tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]">No recent sales found</td></tr>
                            <?php else: while ($row = $purchase_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="text-xs font-mono font-semibold text-[var(--primary)]">
                                        BL<?= str_pad($row['sale_id'], 6, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-[var(--subtext)] whitespace-nowrap"><?= date('d M Y', strtotime($row['sale_date'])) ?></td>
                                <td class="py-3 px-4 text-sm text-[var(--text)]"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="py-3 px-4 text-sm font-medium text-[var(--text)]"><?= $row['quantity'] ?></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">
        <div class="bg-gradient-to-r from-[#4F46E5] to-indigo-500 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-edit text-white text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-white">Edit Profile</h2>
            </div>
            <button onclick="closeProfileModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-white text-sm"></i>
            </button>
        </div>
        <form id="profileForm" class="px-6 py-6 space-y-4">
            <div>
                <label for="f_owner_name" class="block text-sm font-medium text-[var(--text)] mb-1.5">Owner Name</label>
                <input type="text" id="f_owner_name" name="owner_name" placeholder="Enter owner name"
                       class="w-full px-4 py-2.5 rounded-xl border border-[var(--border)] text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
            </div>
            <div>
                <label for="f_phone" class="block text-sm font-medium text-[var(--text)] mb-1.5">Phone Number</label>
                <input type="tel" id="f_phone" name="phone" maxlength="10" placeholder="10-digit mobile number"
                       class="w-full px-4 py-2.5 rounded-xl border border-[var(--border)] text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all">
            </div>
            <div>
                <label for="f_gst" class="block text-sm font-medium text-[var(--text)] mb-1.5">GST Number</label>
                <input type="text" id="f_gst" name="GST_NO" maxlength="15" placeholder="15-character GST number"
                       class="w-full px-4 py-2.5 rounded-xl border border-[var(--border)] text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all uppercase">
            </div>
            <div>
                <label for="f_address" class="block text-sm font-medium text-[var(--text)] mb-1.5">Address</label>
                <textarea id="f_address" name="Address" rows="3" placeholder="Enter business address"
                          class="w-full px-4 py-2.5 rounded-xl border border-[var(--border)] text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all resize-none"></textarea>
            </div>
        </form>
        <div class="px-6 pb-6 flex gap-3">
            <button onclick="closeProfileModal()" class="flex-1 py-2.5 px-4 bg-gray-100 text-[var(--text)] font-medium rounded-xl hover:bg-gray-200 transition-colors text-sm">Cancel</button>
            <button onclick="saveProfile()" class="flex-1 py-2.5 px-4 bg-gradient-to-r from-[#4F46E5] to-indigo-500 text-white font-medium rounded-xl transition-all text-sm shadow-sm">Save Changes</button>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
function openProfileModal() {
    document.getElementById('profileMenu').classList.add('hidden');
    fetch('ajax/get_dealer_profile.php')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('f_owner_name').value = data.owner_name ?? '';
            document.getElementById('f_phone').value      = data.phone     ?? '';
            document.getElementById('f_gst').value        = data.GST_NO    ?? '';
            document.getElementById('f_address').value    = data.Address   ?? '';
        })
        .catch(function() { showToast('Failed to load profile', 'error'); });
    var modal = document.getElementById('profileModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeProfileModal() {
    var modal = document.getElementById('profileModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
function saveProfile() {
    var data = new FormData(document.getElementById('profileForm'));
    fetch('ajax/update_dealer_profile.php', { method: 'POST', body: data })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { showToast('Profile updated!', 'success'); setTimeout(closeProfileModal, 1500); }
            else { showToast(res.message || 'Update failed', 'error'); }
        })
        .catch(function() { showToast('Network error', 'error'); });
}
document.getElementById('profileModal').addEventListener('click', function(e) {
    if (e.target === this) closeProfileModal();
});
</script>
</body>
</html>
