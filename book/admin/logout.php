<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: ../public/librarian_login.php");
exit();
?>