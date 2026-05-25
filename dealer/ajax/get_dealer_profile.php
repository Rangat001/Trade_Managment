<?php
session_start();
require_once '../includes/auth_check.php';





$stmt = $conn->prepare("SELECT owner_name, phone, GST_NO, Address FROM dealer WHERE id = ?");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

header('Content-Type: application/json');
echo json_encode($data);