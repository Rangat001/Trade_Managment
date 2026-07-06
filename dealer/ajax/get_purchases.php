<?php
/**
 * AJAX endpoint: get_purchases.php
 * Returns all purchases for the logged-in dealer as JSON for DataTables.
 */
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$dealer_id = (int)$_SESSION['rgt_logedin_user_dealer_id'];

$sql = "
    SELECT
        po.id        AS order_id,
        po.order_date,
        po.status,
        pr.product_name,
        poi.quantity,
        poi.base_price,
        poi.total_price,
        COALESCE(
            SUM(CASE WHEN ct.type = 'DEBIT' THEN ct.amount ELSE 0 END), 0
        ) AS paid_amount
    FROM purchase_order_items poi
    JOIN  purchase_orders po ON po.id = poi.order_id
    JOIN  products pr         ON pr.id = poi.product_id
    LEFT JOIN company_transactions ct ON ct.order_id = po.id
    WHERE po.dealer_id = ?
    GROUP BY poi.id
    ORDER BY po.order_date DESC, po.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = [
        'order_date'   => date('d/m/Y', strtotime($row['order_date'])),
        'product_name' => htmlspecialchars($row['product_name']),
        'quantity'     => (int)$row['quantity'],
        'base_price'   => number_format($row['base_price'], 2),
        'total_price'  => number_format($row['total_price'], 2),
        'paid_amount'  => number_format($row['paid_amount'], 2),
        'status'       => $row['status'],
        'order_id'     => (int)$row['order_id'],
    ];
}

$stmt->close();
echo json_encode(['data' => $rows]);
