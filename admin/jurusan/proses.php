<?php
include '../../init.php';

$db = new jurusan;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == "add_jurusan") {
	echo $db->add_jurusan();

} elseif ($action == 'delete_jurusan') {
	echo $db->delete_jurusan();

} elseif ($action == 'edit_jurusan') {
	$edit = $db->edit_jurusan();
	$_SESSION['RAPORT']['pesan_edit_jurusan'] = $edit;
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	header("Location: ".config::base_url('admin/index.php?ref=edit_jurusan&jurusan_id='.$jurusan_id));
	die;
}