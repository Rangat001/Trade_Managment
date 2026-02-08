<?php
session_start();
require '../includes/scripts/connection.php';

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
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
$order_id  = intval($_POST['order_id'] ?? 0);

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE purchase_orders
    SET status = 'RECEIVED'
    WHERE id = ?
    AND dealer_id = ?
    AND status = 'REQUESTED'
");

$stmt->bind_param("ii", $order_id, $dealer_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    // die(mysqli_error($conn));
    echo json_encode(['success' => false, 'message' => 'Order not updated']);
}

$stmt->close();
