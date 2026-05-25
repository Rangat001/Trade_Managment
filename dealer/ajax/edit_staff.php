<?php
session_start();
require '../../includes/scripts/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$dealer_id  = $_SESSION['rgt_logedin_user_dealer_id'];
$staff_id   = (int)($_POST['staff_id'] ?? 0);
$staff_name = trim($_POST['staff_name'] ?? '');
$staff_email= trim($_POST['staff_email'] ?? '');
$staff_role = trim($_POST['staff_role'] ?? '');

if ($staff_id <= 0 || empty($staff_name) || empty($staff_email) || empty($staff_role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Make sure staff belongs to this dealer
$stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=? AND dealer_id=?");
$stmt->bind_param("sssii", $staff_name, $staff_email, $staff_role, $staff_id, $dealer_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Staff updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
}
$stmt->close();