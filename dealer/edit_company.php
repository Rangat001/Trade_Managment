<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can edit companies
if (!$is_admin) {
    $_SESSION['error'] = 'You do not have permission to edit companies.';
    header('Location: companies.php');
    exit;
}
?>
<?php
   
        
        // $comp_id = $_POST['comp_id'];
        // $contact_person_name = $_POST['owner_name'];
        // $phone_no = $_POST['owner_contact'];
        // $email = $_POST['edit_email'];
        // $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
  
        // if(empty($contact_person_name) || empty($phone_no) || empty($email)){
        //     die("All fields are required.");
        // }
        // // Handle Update
        
        // if($_SERVER["REQUEST_METHOD"] == "POST"){
        //     $sql = "UPDATE `companies` SET `contact_person`='$contact_person_name',`phone`='$phone_no',`email`='$email' WHERE `dealer_id` = $dealer_id and `id` = $comp_id";
        //     if ($conn->query($sql) === TRUE) {
        //         header("Location: companies.php");
        //     } else {
        //         die("Error: " . $sql . "<br>" . $conn->error);
        //         echo "Error: " . $sql . "<br>" . $conn->error;
        //     }
        // }

        $errors = [];

// Sanitize first
$comp_id             = trim($_POST['comp_id'] ?? '');
$contact_person_name = trim($_POST['owner_name'] ?? '');
$phone_no            = trim($_POST['owner_contact'] ?? '');
$email               = trim($_POST['edit_email'] ?? '');
$dealer_id           = $_SESSION['rgt_logedin_user_dealer_id'] ?? null;

// ── Required checks ──────────────────────────────────────
if (empty($comp_id))             $errors[] = "Company ID is missing.";
if (empty($contact_person_name)) $errors[] = "Contact person name is required.";
if (empty($phone_no))            $errors[] = "Phone number is required.";
if (empty($email))               $errors[] = "Email is required.";

// ── Format checks ────────────────────────────────────────
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!empty($phone_no) && !preg_match('/^[0-9+\-\s]{7,15}$/', $phone_no)) {
    $errors[] = "Invalid phone number.";
}

if (!empty($contact_person_name) && strlen($contact_person_name) > 100) {
    $errors[] = "Contact person name must be under 100 characters.";
}

if (!empty($comp_id) && !is_numeric($comp_id)) {
    $errors[] = "Invalid company ID.";
}

// ── Session check ────────────────────────────────────────
if (empty($dealer_id)) {
    $errors[] = "Session expired. Please login again.";
}

// ── Stop if any errors ───────────────────────────────────
if (!empty($errors)) {
    $_SESSION['rgt_error_message'] = implode(' ', $errors);
    header("Location: companies.php");
    exit;
}

// ── Update with prepared statement ───────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE `companies` SET `contact_person` = ?, `phone` = ?, `email` = ? WHERE `dealer_id` = ? AND `id` = ?");
    $stmt->bind_param("sssii", $contact_person_name, $phone_no, $email, $dealer_id, $comp_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: companies.php");
        exit;
    } else {
        $stmt->close();
        $_SESSION['rgt_error_message'] = "Update failed. Please try again.";
        header("Location: companies.php");
        exit;
    }
}

?>