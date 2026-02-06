<?php
session_start();
// Database connection
require 'connection.php';

//                                                   Dealer Registration Script

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //                                           Dealer Registration
  $business_name = $_POST["business_name"];
  $owner_name = $_POST["owner_name"];
  $email = $_POST["user_email"];
  $phone = $_POST["user_phone"];
  $plan = "Free";

  $password = $_POST["user_password"];
  $hashedpassword = password_hash($_POST["user_password"], PASSWORD_DEFAULT); // Hash the password
  $confirmPassword = $_POST["user_confirm_password"];

  $created_at = date("d-m-Y");
  $email = mysqli_real_escape_string($conn, $email);

  //for count of enrollment
  $sql = "SELECT COUNT(*) FROM `dealer` WHERE `email` = '$email'";
  $res = mysqli_query($conn, $sql);
  
  if($res) {
    $row = mysqli_fetch_array($res);
    $count = $row[0]; // Number of rows with the specified email
    
    if($count > 0) {
        $_SESSION['rgt_error_message'] = "email is already exists.";
        header("Location: ../../auth/sign-up.php");
        exit(); // Always exit after a header redirect
    }
  }

  // to check all fields are empty or not
  if (empty($business_name) || empty($owner_name) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
    $_SESSION['rgt_error_message'] = "All fields are required.";
    header("Location: ../../auth/sign-up.php");
    exit();
  }
  

  // to check email_id valid or not
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['rgt_error_message'] = "Invalid email format.";
    header("Location: ../../auth/sign-up.php");
    exit();
  }

  // to check password and re-confirm password matched or not
  if ($password !== $confirmPassword) {
    $_SESSION['rgt_error_message'] = "Passwords do not match.";
    header("Location: ../../auth/sign-up.php");
    exit();
  }
  
  //                                                                  Dealer Reg.
   $stmt_insert = $conn->prepare("INSERT INTO `dealer` ( `business_name`, `owner_name`,`email`, `phone`,`plan`,`created_at`) VALUES (?,?,?,?,?,current_timestamp())");   // 5 pram

   // check insert query ir not
    if ($stmt_insert){
 
     $stmt_insert->bind_param("sssss",$business_name,$owner_name,$email,$phone,$plan);
     if ($stmt_insert->execute()) {
       // dealer Registration successful
      $dealer_id = $stmt_insert->insert_id;

      // Now insert into user_master table
      $insertQuery = $conn->prepare("INSERT INTO `users` (`dealer_id`, `name`,`email` ,`password`, `role`,`is_verified`) VALUES (?,?,?,?, 'ADMIN',0)");
      
      if($insertQuery){
        $insertQuery->bind_param("isss",$dealer_id,$owner_name,$email,$hashedpassword);
        
        if($insertQuery->execute()) {
          $_SESSION['rgt_success_message'] = "Account created, Please login with correct credentials.";
          header("Location: ../../auth/sign-in.php");
          exit();
        } 
      }

     } else {
     // Handle database error
     $_SESSION['rgt_error_message'] = "Error: " . $insertQuery . "<br>" . $conn->error;
     header("Location: ../../auth/sign-up.php");
     exit();
     }
   }else {
     // Handle preparation error
     $_SESSION['rgt_error_message'] = "Error: " . $conn->error;
     header("Location: ../../auth/sign-up.php");
     exit();
   }
   $conn->close();
   $stmt->close();
   }
?>