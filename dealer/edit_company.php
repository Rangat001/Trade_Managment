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
   
        
        $comp_id = $_POST['comp_id'];
        $contact_person_name = $_POST['owner_name'];
        $phone_no = $_POST['owner_contact'];
        $email = $_POST['edit_email'];
        $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
  
        if(empty($contact_person_name) || empty($phone_no) || empty($email)){
            die("All fields are required.");
        }
        // Handle Update
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $sql = "UPDATE `companies` SET `contact_person`='$contact_person_name',`phone`='$phone_no',`email`='$email' WHERE `dealer_id` = $dealer_id and `id` = $comp_id";
            if ($conn->query($sql) === TRUE) {
                header("Location: companies.php");
            } else {
                die("Error: " . $sql . "<br>" . $conn->error);
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

?>