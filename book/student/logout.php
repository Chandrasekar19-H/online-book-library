<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: ../public/student_login.php");
exit();
?>