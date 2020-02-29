<?php

include '../../init.php';
$db = new kelas;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == "add_kelas") {
	echo $db->add_kelas();
	
} else if($action == "delete_kelas") {
	echo $db->delete_kelas();

} else if($action == "edit_kelas") {
	$edit = $db->edit_kelas();
	$_SESSION['RAPORT']['pesan_edit_kelas'] = $edit;
	$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
	header("Location: ".config::base_url('admin/index.php?ref=edit_kelas&kelas_id='.$kelas_id));
	die;

} else if($action == "tampil_kelas") {
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	echo json_encode($db->tampil_kelas($jurusan_id));
}