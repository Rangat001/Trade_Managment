<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can edit products
if (!$is_admin) {
    $_SESSION['error'] = 'You do not have permission to edit products.';
    header('Location: products.php');
    exit;
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dealer_id      = (int) $_SESSION['rgt_logedin_user_dealer_id'];
    $product_id     = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $product_name   = trim($_POST['product_name'] ?? '');
    $category       = trim($_POST['category'] ?? '');
    $hsn            = trim($_POST['hsn'] ?? '');
    $barcode        = trim($_POST['barcode'] ?? '');
    $gst            = trim($_POST['gst'] ?? '');
    $assurance      = trim($_POST['assurance'] ?? '');
    $validity       = trim($_POST['validity'] ?? '');
    $purchase_price = trim($_POST['purchase_price'] ?? '');
    $selling_price  = trim($_POST['selling_price'] ?? '');

    // Validate required input from edit form
    if (
        $product_id <= 0 ||
        $product_name === '' ||
        $category === '' ||
        $hsn === '' ||
        $barcode === '' ||
        $gst === '' ||
        $assurance === '' ||
        $validity === '' ||
        $purchase_price === '' ||
        $selling_price === ''
    ) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: products.php");
        exit();
    }

    if (!is_numeric($purchase_price) || !is_numeric($selling_price) || !is_numeric($gst) || !is_numeric($barcode) || !is_numeric($validity)) {
        $_SESSION['error_message'] = "Invalid numeric values provided.";
        header("Location: products.php");
        exit();
    }

    // Update all editable product fields
    $update_sql = "UPDATE products
                   SET product_name = ?,
                       category = ?,
                       HSN = ?,
                       Barcode = ?,
                       GST = ?,
                       assurance = ?,
                       VALIDITY = ?,
                       base_price = ?,
                       selling_price = ?
                   WHERE id = ? AND dealer_id = ?";

    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        $_SESSION['error_message'] = "Error preparing update: " . $conn->error;
        header("Location: products.php");
        exit();
    }

    $stmt->bind_param(
        "ssssssiddii",
        $product_name,
        $category,
        $hsn,
        $barcode,
        $gst,
        $assurance,
        $validity,
        $purchase_price,
        $selling_price,
        $product_id,
        $dealer_id
    );

    if ($stmt->execute()) {
        $stmt->close();

        // Record latest price in history table
        $price_history_sql = "INSERT INTO product_price_history
                              (dealer_id, product_id, base_price, selling_price, effective_from)
                              VALUES (?, ?, ?, ?, NOW())";
        $history_stmt = $conn->prepare($price_history_sql);

        if ($history_stmt) {
            $history_stmt->bind_param("iidd", $dealer_id, $product_id, $purchase_price, $selling_price);
            if ($history_stmt->execute()) {
                $_SESSION['success_message'] = "Product updated successfully and price history recorded.";
            } else {
                $_SESSION['error_message'] = "Product updated but failed to record price history: " . $history_stmt->error;
            }
            $history_stmt->close();
        } else {
            $_SESSION['error_message'] = "Product updated but failed to prepare price history insert.";
        }

        header("Location: products.php");
        exit();
    }

    $_SESSION['error_message'] = "Error updating product: " . $stmt->error;
    $stmt->close();
    header("Location: products.php");
    exit();
}

header("Location: products.php");
exit();

$conn->close();
?>