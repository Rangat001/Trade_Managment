<?php
require_once 'includes/auth_check.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function respond($success, $message, $isAjax) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }
    $_SESSION[$success ? 'success' : 'error'] = $message;
    header('Location: products.php');
    exit;
}

if (!$is_admin) {
    respond(false, 'You do not have permission to add products.', $isAjax);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php'); exit;
}

$dealer_id      = (int)$_SESSION['rgt_logedin_user_dealer_id'];
$company_id     = trim($_POST['company_id']     ?? '');
$product_name   = trim($_POST['product_name']   ?? '');
$hsn_code       = trim($_POST['hsn_code']       ?? '');
$category       = trim($_POST['category']       ?? '');
$barcode_no     = trim($_POST['barcode_no']     ?? '');
$gst            = trim($_POST['gst']            ?? '');
$purchase_price = trim($_POST['purchase_price'] ?? '');
$selling_price  = trim($_POST['selling_price']  ?? '');
$assurance      = trim($_POST['assurance']      ?? '');
$validity       = $_POST['validity'] ?? '';

if (empty($company_id) || empty($product_name) || empty($hsn_code) || empty($barcode_no) ||
    empty($gst) || empty($purchase_price) || empty($selling_price) || empty($assurance) || $validity === '') {
    respond(false, 'All fields are required.', $isAjax);
}

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
    respond(false, 'Product image is required.', $isAjax);
}

$fileName   = basename($_FILES['productImage']['name']);
$image_path = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $image_path)) {
    respond(false, 'Image upload failed.', $isAjax);
}

$initial_quantity = 0;
$stmt = $conn->prepare("INSERT INTO `products`
    (`dealer_id`,`company_id`,`product_name`,`category`,`HSN`,`GST`,`Barcode`,
     `base_price`,`selling_price`,`assurance`,`validity`,`current_stock`,`image_path`)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param(
    "iisssssddssis",
    $dealer_id, $company_id, $product_name, $category,
    $hsn_code, $gst, $barcode_no,
    $purchase_price, $selling_price,
    $assurance, $validity, $initial_quantity, $image_path
);

if (!$stmt->execute()) {
    $err = $stmt->error; $stmt->close();
    respond(false, 'Failed to add product: ' . $err, $isAjax);
}

$product_id = $conn->insert_id;
$stmt->close();

$hist = $conn->prepare("INSERT INTO `product_price_history`
    (`dealer_id`,`product_id`,`base_price`,`selling_price`,`effective_from`)
    VALUES (?,?,?,?,CURDATE())");
$hist->bind_param("iidd", $dealer_id, $product_id, $purchase_price, $selling_price);
$hist->execute();
$hist->close();

respond(true, 'Product added successfully.', $isAjax);
