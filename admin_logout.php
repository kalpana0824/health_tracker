<?php
session_start();
require_once 'admin_auth.php';

logoutAdmin();
header("Location: admin_login.php");
exit;
?>

