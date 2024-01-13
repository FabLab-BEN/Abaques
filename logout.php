<!-- logout -->
<?php
// require_once('log.php');
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
// logRegistry("Logged out");
header("location: /");
exit;
?>