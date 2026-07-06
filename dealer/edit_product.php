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
    respond(false, 'You do not have permission to edit products.', $isAjax);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php'); exit;
}

$dealer_id      = (int)$_SESSION['rgt_logedin_user_dealer_id'];
$product_id     = (int)($_POST['product_id'] ?? 0);
$product_name   = trim($_POST['product_name']   ?? '');
$category       = trim($_POST['category']       ?? '');
$hsn            = trim($_POST['hsn']            ?? '');
$barcode        = trim($_POST['barcode']        ?? '');
$gst            = trim($_POST['gst']            ?? '');
$assurance      = trim($_POST['assurance']      ?? '');
$validity       = $_POST['validity'] ?? '';
$purchase_price = trim($_POST['purchase_price'] ?? '');
$selling_price  = trim($_POST['selling_price']  ?? '');

if ($product_id <= 0 || $product_name === '' || $category === '' || $hsn === '' ||
    $barcode === '' || $gst === '' || $assurance === '' || $validity === '' ||
    $purchase_price === '' || $selling_price === '') {
    respond(false, 'All fields are required.', $isAjax);
}

if (!is_numeric($purchase_price) || !is_numeric($selling_price) ||
    !is_numeric($gst) || !is_numeric($barcode) || !is_numeric($validity)) {
    respond(false, 'Invalid numeric values provided.', $isAjax);
}

// Image upload
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Fetch current image path as fallback
$cur = $conn->prepare("SELECT image_path FROM products WHERE id = ? AND dealer_id = ?");
$cur->bind_param("ii", $product_id, $dealer_id);
$cur->execute();
$cur_row = $cur->get_result()->fetch_assoc();
$cur->close();
$image_path = $cur_row['image_path'] ?? '';

if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($_FILES['productImage']['type'], $allowedTypes) || $_FILES['productImage']['size'] > 1 * 1024 * 1024) {
        respond(false, 'Invalid file type or file too large (max 1MB).', $isAjax);
    }
    $fileName   = basename($_FILES['productImage']['name']);
    $image_path = $uploadDir . $fileName;
    if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $image_path)) {
        respond(false, 'Image upload failed.', $isAjax);
    }
}

$stmt = $conn->prepare("UPDATE products
    SET product_name=?, category=?, HSN=?, Barcode=?, GST=?,
        assurance=?, VALIDITY=?, base_price=?, selling_price=?, image_path=?
    WHERE id=? AND dealer_id=?");
if (!$stmt) {
    respond(false, 'DB prepare error: ' . $conn->error, $isAjax);
}

$stmt->bind_param("sssssssddsii",
    $product_name, $category, $hsn, $barcode, $gst,
    $assurance, $validity, $purchase_price, $selling_price,
    $image_path, $product_id, $dealer_id
);

if (!$stmt->execute()) {
    $err = $stmt->error; $stmt->close();
    respond(false, 'Update failed: ' . $err, $isAjax);
}
$stmt->close();

// Record price history
$hist = $conn->prepare("INSERT INTO product_price_history
    (dealer_id, product_id, base_price, selling_price, effective_from)
    VALUES (?, ?, ?, ?, NOW())");
if ($hist) {
    $hist->bind_param("iidd", $dealer_id, $product_id, $purchase_price, $selling_price);
    $hist->execute();
    $hist->close();
}

respond(true, 'Product updated successfully.', $isAjax);
