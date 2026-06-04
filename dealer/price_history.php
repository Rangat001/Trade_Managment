<?php
require_once 'includes/auth_check.php';

$product_id = intval($_GET['id'] ?? 0);

$pageTitle  = 'Price History';
$activePage = 'products';

// Get product details (using prepared statement for safety)
$prod_stmt = $conn->prepare("
    SELECT p.*, c.company_name
    FROM products p
    LEFT JOIN companies c ON p.company_id = c.id
    WHERE p.id = ? AND p.dealer_id = ?
");
$prod_stmt->bind_param("ii", $product_id, $dealer_id);
$prod_stmt->execute();
$product = $prod_stmt->get_result()->fetch_assoc();

if (!$product) { header("Location: products.php"); exit; }

// Get price history
$hist_stmt = $conn->prepare("
    SELECT * FROM product_price_history
    WHERE product_id = ? AND dealer_id = ?
    ORDER BY effective_from ASC
");
$hist_stmt->bind_param("ii", $product_id, $dealer_id);
$hist_stmt->execute();
$history_result = $hist_stmt->get_result();

// Build arrays for chart + table
$dates          = [];
$base_prices    = [];
$selling_prices = [];
$history_rows   = [];

while ($row = $history_result->fetch_assoc()) {
    $dates[]          = date('d M Y', strtotime($row['effective_from']));
    $base_prices[]    = (float)$row['base_price'];
    $selling_prices[] = (float)$row['selling_price'];
    $history_rows[]   = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header.php'; ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-[var(--bg)]">
<?php require_once 'includes/sidebar.php'; ?>

<div class="md:ml-64 pb-16 md:pb-0">
    <main class="p-4 md:p-8">

        <!-- Back button -->
        <div class="mb-6">
            <a href="products.php"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[var(--subtext)] bg-white border border-[var(--border)] rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Products
            </a>
        </div>

        <!-- Product Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-5 md:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-box text-[var(--primary)]"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-[var(--text)]"><?= htmlspecialchars($product['product_name']) ?></h2>
                            <p class="text-sm text-[var(--subtext)]">
                                Company: <span class="font-medium text-[var(--text)]"><?= htmlspecialchars($product['company_name']) ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 sm:text-right">
                    <div class="bg-indigo-50 rounded-xl px-4 py-3 border border-indigo-100">
                        <p class="text-xs text-[var(--subtext)] mb-0.5">Purchase Price</p>
                        <p class="text-base font-bold text-[var(--primary)]">₹<?= number_format($product['base_price'], 2) ?></p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                        <p class="text-xs text-[var(--subtext)] mb-0.5">Selling Price</p>
                        <p class="text-base font-bold text-emerald-600">₹<?= number_format($product['selling_price'], 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (count($history_rows) > 0): ?>

        <!-- Price Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-5 md:p-6 mb-6">
            <h3 class="text-base font-semibold text-[var(--text)] mb-5 flex items-center gap-2">
                <i class="fas fa-chart-line text-[var(--primary)]"></i>
                Price Trend
            </h3>
            <div class="h-72 md:h-96">
                <canvas id="priceChart"></canvas>
            </div>
        </div>

        <!-- Price History Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden">
            <div class="px-5 md:px-6 py-4 border-b border-[var(--border)]">
                <h3 class="text-base font-semibold text-[var(--text)]">Price History Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[520px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-[var(--border)]">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Date</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Base Price</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Selling Price</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Margin</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--subtext)] uppercase tracking-wider">Change</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $prev_base    = 0;
                        $prev_selling = 0;
                        foreach ($history_rows as $i => $row):
                            $margin     = $row['selling_price'] - $row['base_price'];
                            $margin_pct = $row['base_price'] > 0 ? ($margin / $row['base_price']) * 100 : 0;
                            $base_chg   = $i > 0 ? $row['base_price']    - $prev_base    : 0;
                            $sell_chg   = $i > 0 ? $row['selling_price'] - $prev_selling : 0;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-[var(--text)] whitespace-nowrap">
                                <?= date('d M Y', strtotime($row['effective_from'])) ?>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-[var(--text)] text-right">
                                ₹<?= number_format($row['base_price'], 2) ?>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-[var(--text)] text-right">
                                ₹<?= number_format($row['selling_price'], 2) ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                    +₹<?= number_format($margin, 2) ?> (<?= number_format($margin_pct, 1) ?>%)
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <?php if ($i === 0): ?>
                                    <span class="text-[var(--subtext)] text-xs">Initial price</span>
                                <?php elseif ($base_chg == 0 && $sell_chg == 0): ?>
                                    <span class="text-[var(--subtext)] text-xs">No change</span>
                                <?php else:
                                    $up = ($base_chg > 0 || $sell_chg > 0);
                                ?>
                                    <span class="inline-flex items-center gap-1 text-xs font-medium <?= $up ? 'text-red-600' : 'text-green-600' ?>">
                                        <i class="fas fa-arrow-<?= $up ? 'up' : 'down' ?> text-[10px]"></i>
                                        Base: ₹<?= number_format(abs($base_chg), 2) ?> &nbsp;|&nbsp; Sell: ₹<?= number_format(abs($sell_chg), 2) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                            $prev_base    = $row['base_price'];
                            $prev_selling = $row['selling_price'];
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php else: ?>
        <!-- Empty state -->
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-line text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-base font-semibold text-[var(--text)] mb-1">No price history yet</h3>
            <p class="text-sm text-[var(--subtext)]">Price changes will appear here when you edit this product's prices.</p>
        </div>
        <?php endif; ?>

    </main>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
<?php if (count($history_rows) > 0): ?>
var dates         = <?= json_encode($dates) ?>;
var basePrices    = <?= json_encode($base_prices) ?>;
var sellingPrices = <?= json_encode($selling_prices) ?>;

var ctx = document.getElementById('priceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [
            {
                label: 'Base Price (₹)',
                data: basePrices,
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79,70,229,0.08)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#4F46E5'
            },
            {
                label: 'Selling Price (₹)',
                data: sellingPrices,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#10B981'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: { size: 13, family: 'Inter, sans-serif' },
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17,24,39,0.9)',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(ctx) {
                        return ctx.dataset.label + ': ₹' + ctx.parsed.y.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                ticks: {
                    callback: function(v) { return '₹' + v.toFixed(2); },
                    font: { size: 11 }
                },
                grid: { color: 'rgba(0,0,0,0.04)' }
            },
            x: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>
