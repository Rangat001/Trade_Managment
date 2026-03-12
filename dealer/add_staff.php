<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can add staff
if (!$is_admin) {
    $_SESSION['error'] = 'You do not have permission to add staff.';
    header('Location: staff.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['staff_name']);
    $email = trim($_POST['staff_email']);
    $role = $_POST['staff_role'];
    $password = password_hash($_POST['staff_password'], PASSWORD_DEFAULT);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($role) || empty($_POST['staff_password'])) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: staff.php');
        exit;
    }
    
    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Email already exists.';
        header('Location: staff.php');
        exit;
    }
    $check_stmt->close();
    
    // Insert new staff member
    $stmt = $conn->prepare("INSERT INTO users (dealer_id, name, email, password, role, is_verified) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("issss", $dealer_id, $name, $email, $password, $role);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Staff member added successfully.';
    } else {
        $_SESSION['error'] = 'Failed to add staff member. Please try again.';
    }
    
    $stmt->close();
    header('Location: staff.php');
    exit;
} else {
    header('Location: staff.php');
    exit;
}
?>