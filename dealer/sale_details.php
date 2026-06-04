<?php
require_once 'includes/auth_check.php';

$sale_id = intval($_GET['id'] ?? 0);
if ($sale_id <= 0) { header("Location: sales.php"); exit; }

$pageTitle  = 'Sale Details';
$activePage = 'sales';

// Get sale
$stmt = $conn->prepare("SELECT * FROM sales WHERE id = ? AND dealer_id = ? AND is_deleted = 0");
$stmt->bind_param("ii", $sale_id, $dealer_id);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();
if (!$sale) { header("Location: sales.php"); exit; }

// Get sale items
$items_stmt = $conn->prepare("
    SELECT si.*, p.product_name
    FROM sale_items si
    JOIN products p ON p.id = si.product_id
    WHERE si.sale_id = ?
    ORDER BY si.id
");
$items_stmt->bind_param("i", $sale_id);
$items_stmt->execute();
$items = $items_stmt->get_result();

$bill_no = 'BL' . str_pad($sale_id, 6, '0', STR_PAD_LEFT);
$detail_mobile = preg_replace('/\D+/', '', (string)($sale['mobile_no'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">
    <main class="p-4 md:p-8">

        <!-- Top action bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <a href="sales.php"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[var(--subtext)] bg-white border border-[var(--border)] rounded-xl hover:bg-gray-50 transition-colors self-start">
                <i class="fas fa-arrow-left text-xs"></i> Back to Sales
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="shareCurrentBillToWhatsapp(<?= (int)$sale_id ?>, '<?= htmlspecialchars($detail_mobile, ENT_QUOTES) ?>')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-xl transition-colors">
                    <i class="fab fa-whatsapp"></i>
                    <span class="hidden sm:inline">WhatsApp</span>
                </button>
                <a href="print_bill.php?sale_id=<?= $sale_id ?>" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                    <i class="fas fa-print"></i>
                    <span class="hidden sm:inline">Print Bill</span>
                </a>
                <a href="print_bill_thermal.php?sale_id=<?= $sale_id ?>" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-xl transition-colors">
                    <i class="fas fa-receipt"></i>
                    <span class="hidden sm:inline">Thermal</span>
                </a>
            </div>
        </div>

        <!-- Sale Summary Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-5 md:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-[var(--text)]">Bill #<?= $bill_no ?></h2>
                    <p class="text-sm text-[var(--subtext)] mt-1">
                        <?= date("d F Y", strtotime($sale['sale_date'])) ?>
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-[var(--primary)] text-sm font-semibold rounded-xl self-start">
                    <?= htmlspecialchars($sale['billing_type']) ?>
                </span>
            </div>

            <!-- Meta grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-5 border-t border-[var(--border)]">
                <div>
                    <p class="text-xs text-[var(--subtext)] mb-1 uppercase tracking-wide font-medium">Payment</p>
                    <p class="text-sm font-semibold text-[var(--text)]"><?= htmlspecialchars($sale['payment_mode']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--subtext)] mb-1 uppercase tracking-wide font-medium">Delivery</p>
                    <p class="text-sm font-semibold text-[var(--text)]"><?= htmlspecialchars($sale['delivery']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--subtext)] mb-1 uppercase tracking-wide font-medium">Discount</p>
                    <p class="text-sm font-semibold text-red-600">
                        ₹<?= number_format(max(0, ($sale['bill_amount'] - $sale['total_amount'])), 2) ?>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-[var(--subtext)] mb-1 uppercase tracking-wide font-medium">Profit</p>
                    <p class="text-sm font-semibold text-green-600">₹<?= number_format($sale['profit'], 2) ?></p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="px-5 md:px-6 py-4 border-b border-[var(--border)]">
                <h3 class="text-base font-semibold text-[var(--text)]">Sale Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-[var(--border)]">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Product</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Base Price</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Sell Price</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Qty</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Line Total</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $total_qty = 0;
                        $total_amount = 0;
                        while ($item = $items->fetch_assoc()):
                            $line_total   = $item['selling_price'] * $item['quantity'];
                            $line_profit  = ($item['selling_price'] - $item['base_price']) * $item['quantity'];
                            $total_qty   += $item['quantity'];
                            $total_amount += $line_total;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-[var(--text)]"><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="py-3 px-4 text-sm text-[var(--subtext)] text-right">₹<?= number_format($item['base_price'], 2) ?></td>
                            <td class="py-3 px-4 text-sm text-[var(--text)] text-right">₹<?= number_format($item['selling_price'], 2) ?></td>
                            <td class="py-3 px-4 text-sm text-[var(--text)] text-right font-medium"><?= $item['quantity'] ?></td>
                            <td class="py-3 px-4 text-sm font-semibold text-[var(--text)] text-right">₹<?= number_format($line_total, 2) ?></td>
                            <td class="py-3 px-4 text-sm font-semibold text-green-600 text-right">₹<?= number_format($line_profit, 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="border-t-2 border-[var(--border)] bg-gray-50">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-sm font-bold text-[var(--text)]">TOTAL</td>
                            <td class="py-3 px-4 text-sm font-bold text-[var(--text)] text-right"><?= $total_qty ?></td>
                            <td class="py-3 px-4 text-sm font-bold text-[var(--text)] text-right">₹<?= number_format($total_amount, 2) ?></td>
                            <td class="py-3 px-4 text-sm font-bold text-green-600 text-right">₹<?= number_format($sale['profit'], 2) ?></td>
                        </tr>
                        <?php if (($sale['bill_amount'] - $sale['total_amount']) > 0): ?>
                        <tr>
                            <td colspan="4" class="py-2 px-4 text-sm text-right text-[var(--subtext)]">Discount:</td>
                            <td colspan="2" class="py-2 px-4 text-sm font-semibold text-red-600 text-right">
                                -₹<?= number_format($sale['bill_amount'] - $sale['total_amount'], 2) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr class="bg-[var(--primary)]">
                            <td colspan="4" class="py-4 px-4 text-sm font-bold text-white text-right">FINAL AMOUNT</td>
                            <td colspan="2" class="py-4 px-4 text-lg font-extrabold text-white text-right">
                                ₹<?= number_format($sale['total_amount'], 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </main>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
function shareCurrentBillToWhatsapp(saleId, mobileNo) {
    var cleanPhone = String(mobileNo).replace(/\D/g, '');
    if (!cleanPhone) {
        showToast('Mobile number is not available for this sale.', 'error');
        return;
    }
    var billUrl = window.location.origin + window.location.pathname.replace('sale_details.php', 'print_bill.php') + '?sale_id=' + saleId;
    var message = 'Hello, here is your bill: ' + billUrl;
    window.open('https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(message), '_blank');
}
</script>
</body>
</html>
