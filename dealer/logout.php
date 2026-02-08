<?php
session_start();
if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    header("Location: ../login.php");
    exit;
}

/*
 |----------------------------------------
 | Destroy all session data
 |----------------------------------------
*/

$_SESSION = [];

// Destroy session cookie (important)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

/*
 |----------------------------------------
 | Redirect to login page
 |----------------------------------------
*/
header("Location: ../auth/sign-in.php");
exit;
