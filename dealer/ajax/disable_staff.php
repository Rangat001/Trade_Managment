<?php
session_start();
require '../../includes/scripts/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
$staff_id  = (int)($_POST['staff_id'] ?? 0);

if ($staff_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid staff ID']);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET is_active=0 WHERE id=? AND dealer_id=?");

$stmt->bind_param("ii", $staff_id, $dealer_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Staff disabled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
}
$stmt->close();