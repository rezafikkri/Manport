<?php

include '../../init.php';
$db = new identitas_sekolah;
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == "add_identitas_sekolah") {
	if($db->add_identitas_sekolah()) {
		header("Location: ".config::base_url('admin/index.php?ref=identitas_sekolah'));
		die;
	} else {
		$_SESSION['RAPORT']['pesan_add_identitas_sekolah'] = 'gagal';
		header("Location: ".config::base_url('admin/index.php?ref=add_identitas_sekolah'));
		die;
	}

} elseif($action == "edit_identitas_sekolah") {
	$edit = $db->edit_identitas_sekolah();
	$_SESSION['RAPORT']['pesan_edit_identitas_sekolah'] = $edit;
	header("Location: ".config::base_url('admin/index.php?ref=edit_identitas_sekolah'));
	die;
}