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
    header('Location: staff.php');
    exit;
}

if (!$is_admin) {
    respond(false, 'You do not have permission to add staff.', $isAjax);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff.php'); exit;
}

$name     = trim($_POST['staff_name']     ?? '');
$email    = trim($_POST['staff_email']    ?? '');
$role     = trim($_POST['staff_role']     ?? '');
$password = trim($_POST['staff_password'] ?? '');

if (empty($name) || empty($email) || empty($role) || empty($password)) {
    respond(false, 'All fields are required.', $isAjax);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Invalid email format.', $isAjax);
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->get_result()->num_rows > 0
    ? respond(false, 'Email already exists.', $isAjax)
    : null;
$check->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users
    (dealer_id, name, email, password, role, is_verified)
    VALUES (?, ?, ?, ?, ?, 1)");
$stmt->bind_param("issss", $dealer_id, $name, $email, $hashed, $role);

if ($stmt->execute()) {
    $stmt->close();
    respond(true, 'Staff member added successfully.', $isAjax);
} else {
    $err = $stmt->error; $stmt->close();
    respond(false, 'Failed to add staff: ' . $err, $isAjax);
}
