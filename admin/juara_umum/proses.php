<?php  

include "../../init.php";
$db = new juara_umum;
if(!isset($_SESSION['RAPORT']['tahun_ajaran_id']) || !isset($_SESSION['RAPORT']['semester_id'])) die;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == 'tentukan_juara_umum') {

	$db->form_validation([
		'tipeJuara[Tipe juara]'=>'required|must[all,perjenjang]'
	], false);
	// form errors
	$errors = $db->get_form_errors();
	if($errors) {
		echo json_encode(['errors'=>$errors]);
		die;
	}

	$tipeJuara = filter_input(INPUT_POST, 'tipeJuara', FILTER_SANITIZE_STRING);
	if($tipeJuara == "perjenjang") {
		$db->form_validation([
		'kelas[Kelas]'=>'required'
		], false);
		// form errors
		$errors = $db->get_form_errors();
		if($errors) {
			echo json_encode(['errors'=>$errors]);
			die;
		}
		$kelas = filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING);
		if(!preg_match("/^[IVX]+\z/i", $kelas)) {
			echo json_encode(['kelas_salah'=>'yes']);
			die;
		}
		$where = $db->generate_where($kelas);
	} else {
		$where = null;
	}

	$tipeJuaraDB = $db->get_tipe_juara_umum();
	if($tipeJuara != $tipeJuaraDB) {
		// reset juara umum
		if($db->cek_has_juara_umum($where) > 0) {
			$db->reset_juara_umum();
		}
		echo $db->tentukan_juara_umum(false, $where, $tipeJuara);
	} else {
		if($db->cek_has_juara_umum($where) > 0) {
			echo json_encode(['success'=>$db->tampil_juara_umum(null, $where)]);
		} else {
			echo $db->tentukan_juara_umum(false, $where, $tipeJuara);
		}
	}

} elseif($action == 'reload_juara_umum') {
	echo $db->tentukan_juara_umum(true);
}