<?php
session_start(); // Start the session

// Destroy session and redirect to login page
session_unset();
session_destroy();
header("Location:../index.php");
exit();
?>