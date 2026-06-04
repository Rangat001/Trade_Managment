<?php
require_once 'includes/auth_check.php';
$pageTitle  = 'Sales';
$activePage = 'sales';
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
            <h2 class="text-2xl font-semibold text-[var(--text)]">Sales Management</h2>
            <button onclick="window.location.href='sales_entry.php'"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#4F46E5] to-indigo-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i>
                <span>New Sale</span>
            </button>
        </div>

        <!-- Sales Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="p-6">

               <div class="overflow-x-auto">
                    <table class="table datanew w-full min-w-[900px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-[var(--border)]">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">No.</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Bill No.</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Date</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Billing Type</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Items Count</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Total Qty</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Bill Amount</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Payable Amount</th>
                                <!-- <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Profit</th> -->
                                <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
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
                                <td class="py-4 px-4 text-sm font-medium text-[var(--text)]"><?= $sr++ ?></td>
                                <td class="py-4 px-4 text-sm text-[var(--subtext)]"><?= 'BL' . str_pad($row['sale_id'], 6, '0', STR_PAD_LEFT) ?></td>
                                <td class="py-4 px-4 text-sm text-[var(--subtext)]"><?= date("d/m/Y", strtotime($row['sale_date'])) ?></td>
                                <td class="py-4 px-4 text-sm text-[var(--text)]"><?= htmlspecialchars($row['billing_type']) ?></td>
                                <td class="py-4 px-4 text-sm text-[var(--text)]"><?= $row['item_count'] ?> item(s)</td>
                                <td class="py-4 px-4 text-sm text-[var(--text)]"><?= $row['total_quantity'] ?></td>
                                <td class="py-4 px-4 text-sm font-medium text-[var(--text)]">₹<?= number_format($row['bill_amount'], 2) ?></td>
                                <td class="py-4 px-4 text-sm font-medium text-[var(--text)]">₹<?= number_format($row['total_amount'], 2) ?></td>
                                <!-- <td class="py-4 px-4 text-sm font-medium <?= $row['profit'] >= 0 ? 'text-green-600' : 'text-red-600' ?>"> -->
                                    <!-- ₹<?= number_format($row['profit'], 2) ?>
                                </td> -->
                                <td class="py-4 px-4 text-sm">
                                    <div class="flex items-center gap-1">
                                        <button onclick="window.location.href='sale_details.php?id=<?= $row['sale_id'] ?>'"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php $mobile_no = preg_replace('/\D+/', '', (string)($row['mobile_no'] ?? '')); ?>
                                        <button onclick="shareBillToWhatsapp(<?= (int)$row['sale_id'] ?>, '<?= htmlspecialchars($mobile_no, ENT_QUOTES) ?>')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors"
                                                title="Share bill on WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </button>
                                        <button onclick="deleteSale(<?= $row['sale_id'] ?>)"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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

<?php require_once 'includes/footer.php'; ?>
<script>
    function deleteSale(saleId) {
        showConfirm('Delete Sale', 'Are you sure you want to delete this sale? This cannot be undone.')
            .then(function(confirmed) {
                if (!confirmed) return;
                fetch('delete_sale.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'sale_id=' + saleId
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        showToast('Sale deleted successfully', 'success');
                        setTimeout(function() { location.reload(); }, 800);
                    } else {
                        showToast(data.message || 'Failed to delete sale', 'error');
                    }
                })
                .catch(function() { showToast('Network error. Please try again.', 'error'); });
            });
    }

    function shareBillToWhatsapp(saleId, mobileNo) {
        var cleanPhone = String(mobileNo).replace(/\D/g, '');
        if (!cleanPhone) {
            showToast('Mobile number is not available for this sale.', 'error');
            return;
        }
        var billUrl = window.location.origin + window.location.pathname.replace('sales.php', 'print_bill.php') + '?sale_id=' + saleId;
        var message = 'Hello, here is your bill: ' + billUrl;
        window.open('https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(message), '_blank');
    }
</script>
</body>
</html>
