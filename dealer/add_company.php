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
   
        
        $comany_name = $_POST['company_name'];
        $contact_person_name = $_POST['contact_person_name'];
        $phone_no = $_POST['phone_no'];
        $email = $_POST['email'];
        $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
  
        if(empty($comany_name) || empty($contact_person_name) || empty($phone_no) || empty($email)){
            die("All fields are required.");
        }
        // Handle Form Submission
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $sql = "INSERT INTO `companies`(`dealer_id`, `company_name`, `contact_person`, `phone`, `email`) VALUES ('$dealer_id','$comany_name','$contact_person_name','$phone_no','$email')";
            if ($conn->query($sql) === TRUE) {
                header("Location: companies.php");
            } else {
                die("Error: " . $sql . "<br>" . $conn->error);
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

?>