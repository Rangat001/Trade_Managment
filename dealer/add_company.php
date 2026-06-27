<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can add companies
if (!$is_admin) {
    $_SESSION['error'] = 'You do not have permission to add companies.';
    header('Location: companies.php');
    exit;
}
?>
<?php
   
        
        $errors = [];

// Sanitize first
        $comany_name         = trim($_POST['company_name'] ?? '');
        $contact_person_name = trim($_POST['contact_person_name'] ?? '');
        $phone_no            = trim($_POST['phone_no'] ?? '');
        $email               = trim($_POST['email'] ?? '');
        $dealer_id           = $_SESSION['rgt_logedin_user_dealer_id'] ?? null;

        // ── Required checks ──────────────────────────────────────
        if (empty($comany_name))         $errors[] = "Company name is required.";
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

        if (!empty($comany_name) && strlen($comany_name) > 100) {
            $errors[] = "Company name must be under 100 characters.";
        }

        if (!empty($contact_person_name) && strlen($contact_person_name) > 100) {
            $errors[] = "Contact person name must be under 100 characters.";
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




        // Handle Form Submission
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){

            // $sql = "INSERT INTO `companies`(`dealer_id`, `company_name`, `contact_person`, `phone`, `email`) VALUES ('$dealer_id','$comany_name','$contact_person_name','$phone_no','$email')";
            // if ($conn->query($sql) === TRUE) {
            //     header("Location: companies.php");
            // } else {
            //     die("Error: " . $sql . "<br>" . $conn->error);
            //     echo "Error: " . $sql . "<br>" . $conn->error;
            // }

            $stmt = $conn->prepare("INSERT INTO `companies`(`dealer_id`, `company_name`, `contact_person`, `phone`, `email`) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $dealer_id, $comany_name, $contact_person_name, $phone_no, $email);

            if ($stmt->execute()) {
                 header("Location: companies.php");
            } else {
                // error: $stmt->error
                die("Error: " . $sql . "<br>" . $stmt->error);
                echo "Error: " . $sql . "<br>" . $stmt->error;
            }

            $stmt->close();
        }

?>