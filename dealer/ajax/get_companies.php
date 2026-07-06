<?php
/**
 * AJAX endpoint: get_companies.php
 * Returns all companies (with balance) for the logged-in dealer as JSON.
 */
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$dealer_id = (int)$_SESSION['rgt_logedin_user_dealer_id'];

$sql = "
    SELECT
        c.*,
        (
            COALESCE((
                SELECT SUM(ct.amount)
                FROM company_transactions ct
                WHERE ct.company_id = c.id
                  AND ct.dealer_id  = ?
                  AND ct.type = 'DEBIT'
            ), 0)
            -
            COALESCE((
                SELECT SUM(poi.total_price)
                FROM purchase_order_items poi
                JOIN purchase_orders po ON po.id = poi.order_id
                WHERE po.company_id = c.id
                  AND po.dealer_id  = ?
            ), 0)
        ) AS balance
    FROM companies c
    WHERE c.dealer_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $dealer_id, $dealer_id, $dealer_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $balance      = (float)$row['balance'];
    $balance_sign = $balance > 0 ? '+' : ($balance < 0 ? '-' : '');
    $balance_cls  = $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-900');

    $rows[] = [
        'id'             => (int)$row['id'],
        'dealer_id'      => (int)$row['dealer_id'],
        'company_name'   => htmlspecialchars($row['company_name']),
        'contact_person' => htmlspecialchars($row['contact_person']),
        'phone'          => htmlspecialchars($row['phone']),
        'email'          => htmlspecialchars($row['email']),
        'balance_html'   => '<span class="' . $balance_cls . '">' . $balance_sign . '₹' . number_format(abs($balance), 2) . '</span>',
        // raw values for the edit modal
        '_raw' => [
            'id'             => (int)$row['id'],
            'company_name'   => $row['company_name'],
            'contact_person' => $row['contact_person'],
            'phone'          => $row['phone'],
            'email'          => $row['email'],
        ]
    ];
}

$stmt->close();
echo json_encode(['data' => $rows]);
