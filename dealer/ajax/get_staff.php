<?php
/**
 * AJAX endpoint: get_staff.php
 * Returns active staff members for the logged-in dealer as JSON.
 */
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$dealer_id = (int)$_SESSION['rgt_logedin_user_dealer_id'];

$stmt = $conn->prepare("
    SELECT id, name, email, role
    FROM users
    WHERE dealer_id = ? AND is_active = 1
    ORDER BY name ASC
");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $role_cls = $row['role'] === 'ADMIN' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';
    $rows[] = [
        'id'       => (int)$row['id'],
        'name'     => htmlspecialchars($row['name']),
        'email'    => htmlspecialchars($row['email']),
        'role'     => $row['role'],
        'role_cls' => $role_cls,
    ];
}

$stmt->close();
echo json_encode(['data' => $rows]);
