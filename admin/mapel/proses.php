<?php  

include '../../init.php';

$db = new mapel;
$dbK = new kelas;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "add_mapel") {
	echo $db->add_mapel();

} elseif($action == "delete_mapel") {
	echo $db->delete_mapel();

} elseif($action == "edit_mapel") {
	$edit = $db->edit_mapel();
	$_SESSION['RAPORT']['pesan_edit_mapel'] = $edit;
	$mapel_id = filter_input(INPUT_POST, 'mapel_id', FILTER_SANITIZE_STRING);
	header("Location: ".config::base_url('admin/index.php?ref=edit_mapel&mapel_id='.$mapel_id));
	die;

} elseif($action == "tampil_kelas") {
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	echo json_encode($dbK->tampil_kelas($jurusan_id));

} elseif($action == "tampil_mapel") {
	$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
	echo json_encode($db->tampil_mapel($kelas_id));
}