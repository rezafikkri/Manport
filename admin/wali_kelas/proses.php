<?php

include "../../init.php";
$db = new wali_kelas;
$dbK = new kelas;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "tampil_kelas") {
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	echo json_encode($dbK->tampil_kelas($jurusan_id));

} elseif($action == "add_wali_kelas") {
	echo $db->add_wali_kelas();

} elseif($action == "edit_wali_kelas") {
	$edit = $db->edit_wali_kelas();
	$_SESSION['RAPORT']['pesan_edit_wali_kelas'] = $edit;
	$wali_kelas_id = filter_input(INPUT_POST, 'wali_kelas_id', FILTER_SANITIZE_STRING);
	header("Location: ".config::base_url('admin/index.php?ref=edit_wali_kelas&wali_kelas_id='.$wali_kelas_id));
	die;

} elseif($action == "delete_wali_kelas") {
	$wali_kelas_id = filter_input(INPUT_POST, 'wali_kelas_id', FILTER_SANITIZE_STRING);
	echo $db->delete_wali_kelas($wali_kelas_id);

} elseif($action == "generate_password_wali_kelas") {
	echo bin2hex(random_bytes(8));
}