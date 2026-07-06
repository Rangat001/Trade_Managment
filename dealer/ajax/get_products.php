<?php
/**
 * AJAX endpoint: get_products.php
 * Returns all products for the logged-in dealer as JSON for DataTables.
 */
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$dealer_id = (int)$_SESSION['rgt_logedin_user_dealer_id'];

$sql = "
    SELECT p.*, c.company_name
    FROM products p
    LEFT JOIN companies c ON p.company_id = c.id
    WHERE p.dealer_id = ?
    ORDER BY p.category, p.product_name
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = [
        'id'            => (int)$row['id'],
        'product_name'  => htmlspecialchars($row['product_name']),
        'category'      => htmlspecialchars($row['category'] ?? ''),
        'company_name'  => htmlspecialchars($row['company_name']),
        'assurance'     => htmlspecialchars($row['assurance'] ?? 'None'),
        'validity'      => htmlspecialchars($row['VALIDITY'] ?? ''),
        'base_price'    => number_format($row['base_price'], 2),
        'selling_price' => number_format($row['selling_price'], 2),
        'current_stock' => (int)$row['current_stock'],
        'hsn'           => htmlspecialchars($row['HSN'] ?? ''),
        'gst'           => htmlspecialchars($row['GST'] ?? ''),
        'barcode'       => htmlspecialchars($row['Barcode'] ?? ''),
        'image_path'    => $row['image_path'] ?? '',
        // Pass raw values for modal editing — keys must match openEditProductModal() params
        '_raw' => [
            'productId'     => (int)$row['id'],
            'productName'   => $row['product_name'],
            'purchasePrice' => (float)$row['base_price'],
            'sellingPrice'  => (float)$row['selling_price'],
            'currentStock'  => (int)$row['current_stock'],
            'category'      => $row['category'] ?? '',
            'hsn'           => $row['HSN'] ?? '',
            'barcode'       => $row['Barcode'] ?? '',
            'gst'           => $row['GST'] ?? '',
            'assurance'     => $row['assurance'] ?? 'None',
            'validity'      => $row['VALIDITY'] ?? '',
            'imagePath'     => $row['image_path'] ?? '',
        ]
    ];
}

$stmt->close();
echo json_encode(['data' => $rows]);
