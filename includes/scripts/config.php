<?php
$timeout = 259200; // 3 days in seconds

session_set_cookie_params($timeout); 

ini_set('session.gc_maxlifetime', $timeout); // Session will expire in 3 days; 

session_start();

?>
