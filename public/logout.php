<?php
require_once '../includes/auth.php';

// Logout user
$auth->logout();

// Redirect to homepage with success message
header("Location: index.php?message=logged_out");
exit();
?>