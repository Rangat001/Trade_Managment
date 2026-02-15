<?php
 require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['rgt_logedin_user_id']) && (trim ($_SESSION['rgt_logedin_user_id']) !== '')){
        $user_id = $_SESSION['rgt_logedin_user_id'];
        $user_role = $_SESSION['rgt_logedin_user_role'];
        if($user_role != "ADMIN"){
            header("Location: ../404.php");
        }
    }else{
        header("Location: ../auth/sign-in.php");
    }

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    die('Unauthorized');
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}

/* =========================
   1. BASIC INPUTS
========================= */

$sale_date       = $_POST['sale_date'] ?? null;
$billing_type    = $_POST['billing_type'] ?? null;
$payment_mode    = $_POST['payment_mode'] ?? null;
$delivery_status = $_POST['delivery_status'] ?? null;
$products        = $_POST['products'] ?? [];
$final_amount    = (float) ($_POST['final_amount'] ?? 0);
$discount_amount = (float) ($_POST['discount_amount'] ?? 0);

if (
    !$sale_date || !$billing_type || !$payment_mode || !$delivery_status ||
    empty($products)
) {
    die('Missing required fields');
}

/* =========================
   2. START DB TRANSACTION
========================= */

$conn->begin_transaction();

try {

    /* =========================
       3. FETCH BASE PRICES
    ========================= */

    

    $productIds = array_column($products, 'product_id');
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $types = str_repeat('i', count($productIds));

    $stmt = $conn->prepare("
        SELECT id, base_price
        FROM products
        WHERE dealer_id = ?
        AND id IN ($placeholders)
    ");

    $stmt->bind_param("i$types", $dealer_id, ...$productIds);
    $stmt->execute();
    $res = $stmt->get_result();

    $basePrices = [];
    while ($row = $res->fetch_assoc()) {
        $basePrices[$row['id']] = $row['base_price'];
    }
    $stmt->close();

    /* =========================
       4. CALCULATE TOTALS
    ========================= */

    $total_amount = 0;
    $total_profit = 0;

    $stockCheckStmt = $conn->prepare("
        SELECT current_stock
        FROM products 
        WHERE id = ? AND dealer_id = ?
        FOR UPDATE
    ");

    $stockUpdateStmt = $conn->prepare("
        UPDATE products 
        SET current_stock = current_stock - ?
        WHERE id = ? AND dealer_id = ?
    ");

    foreach ($products as $item) {

        $product_id    = (int) $item['product_id'];
        $quantity      = (int) $item['quantity'];
        $selling_price = (float) $item['selling_price'];

        $stockCheckStmt->bind_param("ii", $product_id, $dealer_id);
        $stockCheckStmt->execute();
        $stockRes = $stockCheckStmt->get_result();
        $product = $stockRes->fetch_assoc();

        if (!$product) {
            throw new Exception("Product not found or unauthorized");
        }

        if ($product['current_stock'] < $quantity) {
            throw new Exception("Insufficient stock for product ID: $product_id");
        }

        // Reduce stock
        $stockUpdateStmt->bind_param("iii", $quantity, $product_id, $dealer_id);
        $stockUpdateStmt->execute();


        if ($quantity <= 0 || $selling_price <= 0) {
            throw new Exception('Invalid product values');
        }

        if (!isset($basePrices[$product_id])) {
            throw new Exception('Invalid product selected');
        }

        $base_price = $basePrices[$product_id];

        $line_total = $selling_price * $quantity;
        $profit     = ($selling_price - $base_price) * $quantity;

        $total_amount += $line_total;
        $total_profit += $profit;

        
    }

    $stockCheckStmt->close();
    $stockUpdateStmt->close();

    // Discount reduces revenue, NOT base cost
    $bill = $total_amount;

    if ($discount_amount > 0) {
        $total_amount -= $discount_amount;
        if ($total_amount < 0) $total_amount = 0;
    }

    $total_profit = max(0, $total_profit - $discount_amount);



    /* =========================
       5. INSERT INTO SALES
    ========================= */

    $stmt = $conn->prepare("
        INSERT INTO sales
        (dealer_id, sale_date, billing_type, bill_amount, total_amount, payment_mode, profit, delivery)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issidsss",
        $dealer_id,
        $sale_date,
        $billing_type,
        $bill,
        $total_amount,
        $payment_mode,
        $total_profit,
        $delivery_status
    );

    $stmt->execute();
    $sale_id = $stmt->insert_id;
    $stmt->close();

    /* =========================
       6. INSERT SALE ITEMS
    ========================= */

    $stmt = $conn->prepare("
        INSERT INTO sale_items
        (sale_id, product_id, quantity, selling_price, base_price)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($products as $item) {

        $product_id    = (int) $item['product_id'];
        $quantity      = (int) $item['quantity'];
        $selling_price = (float) $item['selling_price'];
        $base_price    = $basePrices[$product_id];

        $stmt->bind_param(
            "iiidd",
            $sale_id,
            $product_id,
            $quantity,
            $selling_price,
            $base_price
        );
        $stmt->execute();
    }
    $stmt->close();

    /* =========================
       7. COMMIT TRANSACTION
    ========================= */

    $conn->commit();

    header("Location: sales.php?success=1");
    exit;

} catch (Exception $e) {

    $conn->rollback();

    echo "<pre>";
    echo "SALE FAILED\n";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "</pre>";
    exit;
}
