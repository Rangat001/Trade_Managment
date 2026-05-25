<?php
session_start();

require_once '../includes/auth_check.php';



 // adjust to your session key
$owner_name = trim($_POST['owner_name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$gst        = trim($_POST['GST_NO'] ?? '');
$address    = trim($_POST['Address'] ?? '');

header('Content-Type: application/json');

if (empty($owner_name) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Owner name and mobile are required']);
    exit;
}

$stmt = $conn->prepare("UPDATE dealer SET owner_name=?, phone=?, GST_NO=?, Address=? WHERE id=?");
$stmt->bind_param("ssssi", $owner_name, $phone, $gst, $address, $dealer_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'DB error']);
}
$stmt->close();