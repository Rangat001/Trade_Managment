<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can delete sales
if (!$is_admin) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete sales']);
    exit;
}

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
$sale_id = intval($_POST['sale_id'] ?? 0);

if ($sale_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid sale ID']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Soft delete: Set is_deleted = 1
    $stmt = $conn->prepare("
        UPDATE sales
        SET is_deleted = 1
        WHERE id = ? AND dealer_id = ?
    ");
    
    $stmt->bind_param("ii", $sale_id, $dealer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Sale not found or already deleted']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>