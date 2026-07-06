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
        <div id="dashboardStats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Total Expense -->
            <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0">
                        <img src="../asset/img/icons/expense.png" alt="Total Expense" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <h3 id="dashExpense" class="text-2xl font-bold text-[var(--text)]">
                            <div class="h-7 w-28 bg-gray-200 rounded animate-pulse"></div>
                        </h3>
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
                        <h3 id="dashRevenue" class="text-2xl font-bold text-[var(--text)]">
                            <div class="h-7 w-28 bg-gray-200 rounded animate-pulse"></div>
                        </h3>
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
                        <h3 id="dashTotalSales" class="text-2xl font-bold text-[var(--text)]">
                            <div class="h-7 w-16 bg-gray-200 rounded animate-pulse"></div>
                        </h3>
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
                        <h3 id="dashTotalProfit" class="text-2xl font-bold text-[var(--text)]">
                            <div class="h-7 w-28 bg-gray-200 rounded animate-pulse"></div>
                        </h3>
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
                        <tbody id="dashLeastStockBody">
                            <tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]"><i class="fas fa-spinner fa-spin mr-2"></i>Loading…</td></tr>
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
                        <tbody id="dashRecentPurchasesBody">
                            <tr><td colspan="5" class="py-8 text-center text-sm text-[var(--subtext)]"><i class="fas fa-spinner fa-spin mr-2"></i>Loading…</td></tr>
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
                        <tbody id="dashBestSellersBody">
                            <tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]"><i class="fas fa-spinner fa-spin mr-2"></i>Loading…</td></tr>
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
                        <tbody id="dashRecentSalesBody">
                            <tr><td colspan="4" class="py-8 text-center text-sm text-[var(--subtext)]"><i class="fas fa-spinner fa-spin mr-2"></i>Loading…</td></tr>
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
