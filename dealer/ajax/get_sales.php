<?php
/**
 * AJAX endpoint: get_sales.php
 * Returns all sales for the logged-in dealer as JSON for DataTables.
 */
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$dealer_id = (int)$_SESSION['rgt_logedin_user_dealer_id'];

$sql = "
    SELECT
        s.id         AS sale_id,
        s.sale_date,
        s.billing_type,
        s.bill_amount,
        s.total_amount,
        s.mobile_no,
        COUNT(si.id)    AS item_count,
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

$rows = [];
$sr   = 1;
while ($row = $result->fetch_assoc()) {
    $mobile = preg_replace('/\D+/', '', (string)($row['mobile_no'] ?? ''));
    $rows[] = [
        'sr'           => $sr++,
        'bill_no'      => 'BL' . str_pad($row['sale_id'], 6, '0', STR_PAD_LEFT),
        'sale_date'    => date('d/m/Y', strtotime($row['sale_date'])),
        'billing_type' => htmlspecialchars($row['billing_type']),
        'item_count'   => (int)$row['item_count'],
        'total_qty'    => (int)$row['total_quantity'],
        'bill_amount'  => number_format($row['bill_amount'], 2),
        'total_amount' => number_format($row['total_amount'], 2),
        'sale_id'      => (int)$row['sale_id'],
        'mobile_no'    => $mobile,
    ];
}

$stmt->close();
echo json_encode(['data' => $rows]);
