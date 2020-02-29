<?php  

session_start();
unset($_SESSION['RAPORT']);
header("Location: login.php");