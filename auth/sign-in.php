<?php 
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>LOGIN</title>
    <link rel="shortcut icon" type="image/x-icon" href="../asset/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        background: linear-gradient(135deg, #121212, #1e1e2f);
        font-family: "Poppins", sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        color: white;
        overflow: hidden;
        background: url("../asset/login.png") no-repeat center center fixed;
        background-size: cover;
      }

      .form-container {
        position: relative;
        background: rgba(0, 0, 0, 0.353);
        padding: 30px;
        width: 450px;
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        animation: fadeIn 0.8s ease-in-out;
        margin-left: 800px;
      }

      .home-button {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #76abae;
        border: none;
        padding: 10px 15px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      .home-button i {
        font-size: 30px;
        width: 25px;
        height: 30px;
        display: inline-block;
        text-align: center;
        line-height: 30px;
      }

      .home-button:hover {
        background: #76abae3c;
      }

      @media screen and (max-width: 1024px) {
        .form-container {
          margin-top: 40px;
          height: auto;
          width: 450px;
          margin-right: 550px;
        }
        .header h3 {
          font-size: 20px;
        }

        .row {
          flex-direction: column;
        }

        .password-wrapper input {
          width: 380px;
        }

        button {
          font-size: 0.9rem;
        }
      }

      @media screen and (max-width: 767px) {
        .form-container {
          height: auto;
          justify-content: center;
          align-items: center;
          width: 100%;
          margin-right: 550px;
          margin-top: -50px;
        }
        .header h3 {
          font-size: 20px;
          font-weight: bolder;
        }

        .row {
          flex-direction: column;
        }

        .password-wrapper input {
          width: 300px;
        }

        button {
          font-size: 0.9rem;
        }
      }

      @media screen and (max-width: 480px) {
        .form-container {
          width: 95%;
        }
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .header {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: row;
        gap: 20px;
        margin-bottom: 20px;
      }

      .header img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
      }

      .header h3 {
        font-size: 1.6rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
      }

      input {
        width: 100%;
        padding: 15px;
        margin: 15px 0;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 5px;
        font-size: 1rem;
        color: white;
        transition: all 0.3s ease;
      }

      input:focus {
        background: rgba(255, 255, 255, 0.2);
        outline: none;
        border: 2px solid #76abae;
        box-shadow: 0 0 8px rgba(35, 162, 246, 0.6);
      }

      .password-wrapper {
        position: relative;
      }

      .password-wrapper input {
        padding-right: 40px;
      }

      .password-wrapper .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.7);
        transition: color 0.3s ease;
      }

      .password-wrapper .toggle-password:hover {
        color: white;
      }

      button {
        width: 100%;
        padding: 15px;
        margin-top: 20px;
        background: #76abae;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      button:hover {
        box-shadow: 0 0 15px black, gray;
        transform: scale(1.03);
      }

      .signup-link {
        text-align: center;
        margin-top: 20px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
      }

      .signup-link a {
        color: #76abae;
        text-decoration: none;
        font-weight: 500;
      }

      .signup-link a:hover {
        text-decoration: underline;
      }

      .forgot_pass {
        color: #76abae;
        text-decoration: none;
      }
      input::placeholder {
        color: #76abae;
        opacity: 1;
      }

      .psw{
        color:  #76abae;
      }
    </style>
  </head>
  <body>
    <div class="home-button">
      <a href="index.php"><i class="fa fa-home"></i></a>
    </div>
    <div class="form-container">
      <div class="header">
        <h3>
          LOGIN
        </h3>
      </div>
      <form action="../includes/scripts/signmein.php" method="post">
        <p class="psw">
          <?php
           if(isset( $_SESSION['rgt_error_message'])){
            echo  $_SESSION['rgt_error_message'];
          }
           if(isset( $_SESSION['rgt_success_message'])){
            echo  $_SESSION['rgt_success_message'];
          }
  
          unset($_SESSION['rgt_error_message']);
          unset($_SESSION['rgt_success_message']);
          
  
          ?>
          </p>
        <input type="text" name="email" placeholder="Email" required />

        <div class="password-wrapper">
          <input
            type="password"
            name="rgt_login_password"
            id="password"
            placeholder="Password"
            required
          />
          <i class="fas fa-eye toggle-password" id="togglePassword"></i>
        </div>
        <a href="forgot.php" class="forgot_pass">Forgot Password?</a>

        <button type="submit">LOGIN</button>

        <p class="signup-link">
          Don't have an account ? <a href="sign-up.php">Register</a>
        </p>
      </form>
    </div>

    <script>
      const passwordInput = document.getElementById("password");
      const togglePassword = document.getElementById("togglePassword");

      togglePassword.addEventListener("click", () => {
        const type = passwordInput.type === "password" ? "text" : "password";
        passwordInput.type = type;

        togglePassword.classList.toggle("fa-eye-slash");
      });
    </script>
    <script>
  // Disable Right-Click
  document.addEventListener("contextmenu", function (event) {
    event.preventDefault();
  });

  // Disable Keyboard Shortcuts for Developer Tools
  document.addEventListener("keydown", function (event) {
    if (
      event.ctrlKey && event.shiftKey && (event.key === "I" || event.key === "J" || event.key === "C" || event.key === "M") || // Ctrl + Shift + I / J / C
      event.ctrlKey && event.key === "U" || // Ctrl + U (View Source)
      event.key === "F12" // F12 (DevTools)
    ) {
      event.preventDefault();
    }
  });

  // Prevent Opening DevTools via Mouse Clicks
  document.addEventListener("mousedown", function (event) {
    if (event.button === 2 || event.button === 1) { // Right-click or Middle-click
      event.preventDefault();
    }
  });

  // Prevent DevTools Detection
  (function() {
    setInterval(function() {
      if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
        document.body.innerHTML = "DevTools Detected. Please close it!";
      }
    }, 1000);
  })();
</script>
  </body>
</html>
