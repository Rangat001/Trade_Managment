<?php
require_once 'includes/auth_check.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function respond($success, $message, $isAjax) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }
    $_SESSION[$success ? 'success' : 'rgt_error_message'] = $message;
    header('Location: companies.php');
    exit;
}

if (!$is_admin) {
    respond(false, 'You do not have permission to edit companies.', $isAjax);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: companies.php'); exit;
}

$comp_id             = trim($_POST['comp_id']       ?? '');
$contact_person_name = trim($_POST['owner_name']    ?? '');
$phone_no            = trim($_POST['owner_contact'] ?? '');
$email               = trim($_POST['edit_email']    ?? '');
$dealer_id           = (int)$_SESSION['rgt_logedin_user_dealer_id'];

$errors = [];
if (empty($comp_id))             $errors[] = 'Company ID is missing.';
if (empty($contact_person_name)) $errors[] = 'Contact person name is required.';
if (empty($phone_no))            $errors[] = 'Phone number is required.';
if (empty($email))               $errors[] = 'Email is required.';
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))           $errors[] = 'Invalid email format.';
if (!empty($phone_no) && !preg_match('/^[0-9+\-\s]{7,15}$/', $phone_no))   $errors[] = 'Invalid phone number.';
if (!empty($contact_person_name) && strlen($contact_person_name) > 100)     $errors[] = 'Contact person name must be under 100 characters.';
if (!empty($comp_id) && !is_numeric($comp_id))                              $errors[] = 'Invalid company ID.';

if (!empty($errors)) {
    respond(false, implode(' ', $errors), $isAjax);
}

$stmt = $conn->prepare("UPDATE `companies`
    SET `contact_person`=?, `phone`=?, `email`=?
    WHERE `dealer_id`=? AND `id`=?");
$stmt->bind_param("sssii", $contact_person_name, $phone_no, $email, $dealer_id, $comp_id);

if ($stmt->execute()) {
    $stmt->close();
    respond(true, 'Company updated successfully.', $isAjax);
} else {
    $err = $stmt->error; $stmt->close();
    respond(false, 'Update failed: ' . $err, $isAjax);
}
