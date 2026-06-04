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
                    <table class="table datanew w-full">
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
                        <tbody class="divide-y divide-[var(--border)]">
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

                                <td class="py-4 px-4 text-sm text-[var(--subtext)]">
                                    <?= date("d/m/Y", strtotime($row['order_date'])) ?>
                                </td>

                                <td class="py-4 px-4 text-sm font-medium text-[var(--text)]">
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </td>

                                <td class="py-4 px-4 text-sm text-[var(--text)]">
                                    <?= $row['quantity'] ?>
                                </td>

                                <td class="py-4 px-4 text-sm text-[var(--text)]">
                                    ₹<?= number_format($row['base_price'], 2) ?>
                                </td>

                                <td class="py-4 px-4 text-sm font-medium text-[var(--text)]">
                                    ₹<?= number_format($row['total_price'], 2) ?>
                                </td>

                                <td class="py-4 px-4 text-sm font-medium text-green-600">
                                    ₹<?= number_format($row['paid_amount'], 2) ?>
                                </td>

                                <!-- STATUS COLUMN -->
                                <td class="py-4 px-4 text-sm font-medium">
                                    <?php $status = $row['status']; ?>

                                    <?php if ($status === 'RECEIVED'): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">RECEIVED</span>
                                    <?php elseif ($status === 'REQUESTED'): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">REQUESTED</span>
                                    <?php elseif ($status === 'CANCELLED'): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">CANCELLED</span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600"><?= htmlspecialchars($status) ?></span>
                                    <?php endif; ?>

                                    <?php if ($status === 'REQUESTED'): ?>
                                        <button
                                            onclick="markAsReceived(<?= $row['order_id'] ?>)"
                                            class="ml-2 px-2 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-colors">
                                            Mark as Received
                                        </button>
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <?php } ?>
                        </tbody>
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
