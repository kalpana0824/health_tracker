<?php
session_start();
require_once 'auth.php';

logoutUser();
header("Location: index.html");
exit;
?>

