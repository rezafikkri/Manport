<?php

include '../../init.php';

$db = new siswa;
$dbK = new kelas;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "tampil_kelas") {
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	echo json_encode($dbK->tampil_kelas($jurusan_id));

} elseif($action == "add_siswa_detail") {
	echo $db->add_siswa_detail();

} elseif($action == 'edit_siswa_detail') {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$edit = $db->edit_siswa_detail($siswa_detail_id);
	$_SESSION['RAPORT']['pesan_edit_siswa_detail'] = $edit;
	if($edit == "keluar") {
		header("Location: ".config::base_url("admin/index.php?ref=edit_siswa_detail"));
		die;
	} else {
		header("Location: ".config::base_url("admin/index.php?ref=edit_siswa_detail&siswa_detail_id=".$siswa_detail_id));
		die;
	}

} elseif($action == 'delete_siswa_detail') {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	echo $db->delete_siswa_detail($siswa_detail_id, 'and status="masih_sekolah"');

} elseif($action == 'tampil_siswa_detail') {
	$status = 'masih_sekolah';
	$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
	echo json_encode($db->tampil_siswa_detail($kelas_id,$status));
}