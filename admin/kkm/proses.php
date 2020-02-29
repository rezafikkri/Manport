<?php 

include '../../init.php';

$db = new kkm;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == 'add_kkm'){
	echo $db->insert_kkm();

} elseif($action == 'edit_kkm') {
	$edit = $db->edit_kkm();
	$_SESSION['RAPORT']['pesan_edit_kkm'] = $edit;
	$kkm_id = filter_input(INPUT_POST, 'kkm_id', FILTER_SANITIZE_STRING);
	header("Location: ".config::base_url('admin/index.php?ref=edit_kkm&kkm_id='.$kkm_id));
	die;

} elseif($action == 'delete_kkm') {	
	echo $db->delete_kkm();

} elseif($action == 'tampil_kkm') {
	$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_SANITIZE_STRING);
	$tampil = $db->tampil_kkm($tahun_ajaran_id);
	echo json_encode($tampil);
}