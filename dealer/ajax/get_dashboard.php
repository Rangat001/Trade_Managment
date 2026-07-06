<?php
/**
 * AJAX endpoint: get_dashboard.php
 * Returns all dashboard data (stat cards + 4 mini-tables) as JSON.
 * Called once on page load so the shell renders instantly.
 */
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$dealer_id = (int)$_SESSION['rgt_logedin_user_dealer_id'];

// ── Stat Cards ────────────────────────────────────────────────────────────────

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS expense FROM company_transactions WHERE dealer_id = ? AND type = 'DEBIT'");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$expense = (float)$stmt->get_result()->fetch_assoc()['expense'];
$stmt->close();

$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) AS revenue FROM sales WHERE dealer_id = ?");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$revenue = (float)$stmt->get_result()->fetch_assoc()['revenue'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total_sales FROM sales s INNER JOIN sale_items si ON si.sale_id = s.id WHERE s.dealer_id = ?");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$total_sales = (int)$stmt->get_result()->fetch_assoc()['total_sales'];
$stmt->close();

$stmt = $conn->prepare("SELECT COALESCE(SUM(profit), 0) AS total_profit FROM sales WHERE dealer_id = ?");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$total_profit = (float)$stmt->get_result()->fetch_assoc()['total_profit'];
$stmt->close();

// ── Least Stock ───────────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT p.product_name, p.current_stock, p.base_price, c.company_name
    FROM products p
    JOIN companies c ON p.company_id = c.id
    WHERE p.dealer_id = ?
    ORDER BY current_stock ASC
    LIMIT 5
");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$res       = $stmt->get_result();
$least_stock = [];
while ($r = $res->fetch_assoc()) {
    $least_stock[] = [
        'product_name'  => htmlspecialchars($r['product_name']),
        'company_name'  => htmlspecialchars($r['company_name']),
        'current_stock' => (int)$r['current_stock'],
        'base_price'    => number_format($r['base_price']),
    ];
}
$stmt->close();

// ── Recent Purchases ──────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT c.company_name, p.product_name, pu.quantity, pu.total_price, po.order_date
    FROM purchase_orders po
    JOIN purchase_order_items pu ON po.id = pu.order_id
    JOIN products p               ON pu.product_id = p.id
    JOIN companies c              ON p.company_id  = c.id
    WHERE po.dealer_id = ?
    ORDER BY po.order_date DESC
    LIMIT 5
");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$res       = $stmt->get_result();
$recent_purchases = [];
while ($r = $res->fetch_assoc()) {
    $recent_purchases[] = [
        'company_name'  => htmlspecialchars($r['company_name']),
        'product_name'  => htmlspecialchars($r['product_name']),
        'quantity'      => (int)$r['quantity'],
        'total_price'   => number_format($r['total_price']),
        'order_date'    => date('d M Y', strtotime($r['order_date'])),
    ];
}
$stmt->close();

// ── Top 5 Best Sellers ────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT p.product_name, c.company_name, SUM(si.quantity) AS total_qty_sold
    FROM sale_items si
    JOIN sales s     ON si.sale_id   = s.id
    JOIN products p  ON si.product_id = p.id
    JOIN companies c ON p.company_id  = c.id
    WHERE s.dealer_id = ?
    GROUP BY p.id, p.product_name, c.company_name
    ORDER BY total_qty_sold DESC
    LIMIT 5
");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$res       = $stmt->get_result();
$best_sellers = [];
$rank = 1;
while ($r = $res->fetch_assoc()) {
    $best_sellers[] = [
        'rank'          => $rank++,
        'company_name'  => htmlspecialchars($r['company_name']),
        'product_name'  => htmlspecialchars($r['product_name']),
        'total_qty_sold'=> number_format($r['total_qty_sold']),
    ];
}
$stmt->close();

// ── Recent Selling ────────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT s.id AS sale_id, p.product_name, si.quantity, s.sale_date
    FROM sales s
    JOIN sale_items si ON si.sale_id  = s.id
    JOIN products p    ON si.product_id = p.id
    WHERE s.dealer_id = ?
    ORDER BY s.sale_date DESC , s.id DESC
    LIMIT 5
");
$stmt->bind_param("i", $dealer_id); $stmt->execute();
$res       = $stmt->get_result();
$recent_sales = [];
while ($r = $res->fetch_assoc()) {
    $recent_sales[] = [
        'bill_no'      => 'BL' . str_pad($r['sale_id'], 6, '0', STR_PAD_LEFT),
        'product_name' => htmlspecialchars($r['product_name']),
        'quantity'     => (int)$r['quantity'],
        'sale_date'    => date('d M Y', strtotime($r['sale_date'])),
    ];
}
$stmt->close();

echo json_encode([
    'stats' => [
        'expense'      => number_format($expense),
        'revenue'      => number_format($revenue),
        'total_sales'  => $total_sales,
        'total_profit' => number_format($total_profit),
    ],
    'least_stock'     => $least_stock,
    'recent_purchases'=> $recent_purchases,
    'best_sellers'    => $best_sellers,
    'recent_sales'    => $recent_sales,
]);
