<?php

include '../../init.php';
$db = new user_admin;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == 'edit_user_admin') {
	$edit = $db->edit_user_admin();
	$_SESSION['RAPORT']['pesan_edit_user_admin'] = $edit;
	header("Location: ".config::base_url('admin/index.php?ref=user_admin'));
	die;
}