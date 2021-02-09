<?php  

include '../../init.php';
$db = new login;
$dbTA = new tahun_ajaran;
$dbSem = new semester;

$login = $db->proses_login_admin($dbTA, $dbSem);

if($login == "success") {
	header("Location: ".config::base_url('admin/index.php'));
	die;

} elseif($login == "passwordWorng") {
	$_SESSION['RAPORT']['passwordWorng'] = "yes";
	header("Location: ".config::base_url('admin/login/login.php'));
	die;

} elseif($login == "usernameNotFound") {
	$_SESSION['RAPORT']['usernameNotFound'] = "yes";
	header("Location: ".config::base_url('admin/login/login.php'));
	die;

} else {
	header("Location: ".config::base_url('admin/login/login.php'));
	die;	
}
