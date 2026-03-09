<?php
session_start();
session_unset();  // Clear all session variables
session_destroy(); // Destroy session
header("Location: login.php");
exit();
?>

