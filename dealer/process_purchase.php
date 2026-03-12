<?php
require_once 'includes/auth_check.php';

$company_id = $_POST['company_id'] ?? null;
$purchase_date = $_POST['purchase_date'] ?? date('Y-m-d');
$products = $_POST['products'] ?? [];
$amount_paid = floatval($_POST['amount_paid'] ?? 0);
$payment_mode = $_POST['payment_mode'] ?? null;
$payment_date = $_POST['payment_date'] ?? date('Y-m-d');
$status = $_POST['purchase_status'] ?? null;
if (!$dealer_id || !$company_id || empty($products) || empty($payment_date) || empty($status)) {
    die("Invalid purchase data");
}

$conn->begin_transaction();

try {

    /* ===============================
       1️⃣ CALCULATE TOTAL GOODS VALUE
       =============================== */
    $total_goods_value = 0;

    foreach ($products as $p) {
        $qty = intval($p['quantity']);
        $price = floatval($p['base_price']);

        if ($qty <= 0 || $price < 0) {
            throw new Exception("Invalid product data");
        }

        $total_goods_value += ($qty * $price);
    }

    /* ===============================
       2️⃣ INSERT PURCHASE ORDER
       =============================== */
    $stmt = $conn->prepare("
        INSERT INTO purchase_orders 
        (dealer_id, company_id, order_date, total_amount, status)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisds", $dealer_id, $company_id, $purchase_date, $total_goods_value, $status);
    $stmt->execute();

    $purchase_order_id = $stmt->insert_id;
    $stmt->close();

    /* ===============================
       3️⃣ INSERT PURCHASE ITEMS + UPDATE STOCK
       =============================== */
    foreach ($products as $p) {
        $product_id = intval($p['product_id']);
        $qty = intval($p['quantity']);
        $price = floatval($p['base_price']);
        $line_total = $qty * $price;

        // purchase_order_items
        $stmt = $conn->prepare("
            INSERT INTO purchase_order_items
            (order_id, product_id, quantity,base_price,total_price)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiidi",
            $purchase_order_id,
            $product_id,
            $qty,
            $price,
            $line_total,
        );
        $stmt->execute();
        $stmt->close();

        // stock update
        $stmt = $conn->prepare("
            UPDATE products
            SET current_stock = current_stock + ?
            WHERE id = ? AND dealer_id = ?
        ");
        $stmt->bind_param("iii", $qty, $product_id, $dealer_id);
        $stmt->execute();
        $stmt->close();
    }

    /* ===============================
       4️⃣ HANDLE PAYMENT (OPTIONAL)
       =============================== */
    if ($amount_paid > 0) {

    // 1️⃣ Always record what dealer ACTUALLY PAID
    $stmt = $conn->prepare("
        INSERT INTO company_transactions
        (dealer_id, company_id, order_id, amount, type, payment_mode, transaction_date)
        VALUES (?, ?, ?, ?, 'DEBIT', ?, ?)
    ");
    $stmt->bind_param(
        "iiidss",
        $dealer_id,
        $company_id,
        $purchase_order_id,
        $amount_paid,
        $payment_mode,
        $payment_date
    );
    $stmt->execute();
    $stmt->close();

    // 2️⃣ If dealer paid EXTRA → record ADVANCE (CREDIT)
    if ($amount_paid > $total_goods_value) {

        $extra_amount = $amount_paid - $total_goods_value;

        $stmt = $conn->prepare("
            INSERT INTO company_transactions
            (dealer_id, company_id, order_id, amount, type, payment_mode, transaction_date)
            VALUES (?, ?, ?, ?, 'CREDIT', ?, ?)
        ");
        $stmt->bind_param(
            "iiidss",
            $dealer_id,
            $company_id,
            $purchase_order_id,
            $extra_amount,
            $payment_mode,
            $payment_date
        );
        $stmt->execute();
        $stmt->close();
    }
    }

    /* ===============================
       5️⃣ COMMIT
       =============================== */
    $conn->commit();

    header("Location: purchases.php?success=1");
    exit;

}catch (Exception $e) {
    $conn->rollback();

    echo "<pre style='color:red'>";
    echo "ERROR TYPE: " . get_class($e) . "\n\n";

    echo "MESSAGE:\n";
    echo $e->getMessage() . "\n\n";

    echo "FILE:\n";
    echo $e->getFile() . "\n\n";

    echo "LINE:\n";
    echo $e->getLine() . "\n\n";

    echo "TRACE:\n";
    print_r($e->getTrace());
    echo "</pre>";
    exit;
}
