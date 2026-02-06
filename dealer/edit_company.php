<?php
    require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['rgt_logedin_user_id']) && (trim ($_SESSION['rgt_logedin_user_id']) !== '')){
        $user_id = $_SESSION['rgt_logedin_user_id'];
        $user_role = $_SESSION['rgt_logedin_user_role'];
        if($user_role !== "ADMIN"){
            header("Location: ../404.php");
        }
    }else{
        header("Location: ../sign-in.php");
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