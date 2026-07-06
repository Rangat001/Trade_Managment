<?php
require_once 'includes/auth_check.php';
$pageTitle  = 'Purchases';
$activePage = 'purchases';
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
    <main class="p-6 md:p-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-[var(--text)]">Purchase Entry</h2>
            <button onclick="window.location.href='purchase_product.php'"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[var(--primary)] to-indigo-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i>
                <span>Add Purchase</span>
            </button>
        </div>

        <!-- Purchases Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="p-6">
                <div class="table-responsive">
                    <table id="purchasesTable" class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Date</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Product</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Quantity</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Purchase Price (₹)</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Product Amount (₹)</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Amount Paid (₹)</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
function markAsReceived(orderId) {
    showConfirm('Mark as Received', 'Mark this order as RECEIVED? This will update stock levels.')
        .then(function(confirmed) {
            if (!confirmed) return;
            fetch('mark_order_received.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_id=' + orderId
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('Order marked as received', 'success');
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    showToast(data.message || 'Failed to update order', 'error');
                }
            })
            .catch(function() { showToast('Network error. Please try again.', 'error'); });
        });
}
</script>
</body>
</html>
