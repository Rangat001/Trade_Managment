<?php
session_start();
// include 'includes/scripts/config.php';

// Database connection
require 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_user_name = $_POST["email"];
    $loginPassword = $_POST["rgt_login_password"];

    // Validate input (you might want to add more validation checks)
    if (empty($login_user_name) || empty($loginPassword)) {
        $_SESSION['rgt_error_message'] = "username and password are required.";
        header("Location: sign-in.php");
    }

     // Retrieve hashed password from the database based on the provided email
     $selectQuery = "SELECT * FROM users WHERE email = '$login_user_name'";
     $result = $conn->query($selectQuery);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $hashedPasswordFromDB = $row["password"];
      $isVerified = $row["is_verified"];
      $userId = $row["id"];
     
      // Verify the provided password against the stored hashed password
      if (password_verify($loginPassword, $hashedPasswordFromDB)) {
        // Password is correct, set session variables or perform other actions as needed
        $isVerified = 1;
        $sql = "UPDATE `users` SET `is_verified`='$isVerified' WHERE `id` = $userId";
        $result = mysqli_query($conn,$sql);
          if ($isVerified == 1) {
            // Password is correct and the user is verified
            $_SESSION['rgt_logedin_user_id'] = $row["id"];
            header("Location: ../../index.php");
      }   
    }else{
        // Password is incorrect
        $_SESSION['rgt_error_message'] = "Incorrect password.";
        header("Location: ../../sign-in.php");
        echo "eror";
        

      }

    }else{
      // username not found in the database
      $_SESSION['rgt_error_message'] = "username is  not found.";
      header("Location: ../../sign-in.php");
  }
  $conn->close();
}
?>