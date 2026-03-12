<?php
require_once 'includes/auth_check.php';

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    echo json_encode([]);
    exit;
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
$company_id = intval($_GET['company_id'] ?? 0);

if ($company_id <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT id, product_name, base_price
    FROM products
    WHERE dealer_id = ?
      AND company_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $dealer_id, $company_id);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
