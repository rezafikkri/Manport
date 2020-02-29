<?php

include '../../init.php';
$db = new siswa;
$dbK = new kelas;
$dbR = new raport;
$dbJK = new juara_kelas;
$dbJU = new juara_umum;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "tampil_kelas") {
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	echo json_encode($dbK->tampil_kelas($jurusan_id, null, 'kelas_id, kelas'));

} elseif($action == "tampil_siswa_keluar") {
	$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
	echo json_encode($db->tampil_siswa_detail($kelas_id,'keluar','s.siswa_detail_id, s.nama_siswa, s.nisn, k.kelas, k.kelas_id, j.nama_jurusan','JOIN kelas as k ON k.kelas_id=s.kelas_id JOIN jurusan as j ON j.jurusan_id=k.jurusan_id'));

} elseif($action == 'edit_siswa_keluar') {
	$db->form_validation([
		'status[Status]' => 'required|must[keluar,masih_sekolah]',
		'nama_siswa[Nama siswa]' => 'required|maxLength[30]'
	], false);
	$db->set_delimiter('<p class="pesan warning">','</p>');
	
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
	$nama_siswa = filter_input(INPUT_POST, 'nama_siswa', FILTER_SANITIZE_STRING);

	if($db->has_formErrors()) {
		$edit = false;
	} else {
		$updateSet = 'status=:status, nama_siswa=:nama_siswa';
		$execute = [':status'=>$status, ':nama_siswa'=>$nama_siswa];
		$edit = $db->update_kelas_or_status_siswa($updateSet, $execute, $siswa_detail_id);
	}

	if($edit == true && $status == "masih_sekolah") {
		$_SESSION['RAPORT']['pesan_edit_siswa_detail'] = "masih_sekolah";
		header("Location: ".config::base_url('admin/index.php?ref=edit_siswa_keluar'));
		die;
	} else {
		$_SESSION['RAPORT']['pesan_edit_siswa_detail'] = $edit;
		header("Location: ".config::base_url('admin/index.php?ref=edit_siswa_keluar&siswa_detail_id='.$siswa_detail_id));
		die;
	}
	
} elseif($action == "deleteSiswaKeluar") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	// sikap siswa
	$dbR->delete_sikap(null, $siswa_detail_id, "deleteSiswaKeluar");
	// nilai
	$dbR->delete_nilai_deskripsi(null, $siswa_detail_id, "deleteSiswaKeluar");
	// praktik kerja industri
	$dbR->delete_praktik_kerja_industri(null, true, "deleteSiswaKeluar");
	// ekstrakurikuler
	$dbR->delete_ekstrakurikuler(null, true, "deleteSiswaKeluar");
	// prestasi
	$dbR->delete_prestasi(null, true, "deleteSiswaKeluar");
	// delete ketidakhadiran
	$dbR->delete_ketidakhadiran(null, $siswa_detail_id, "deleteSiswaKeluar");
	// catatan wali kelas
	$dbR->delete_catatan_wali_kelas(null, $siswa_detail_id, "deleteSiswaKeluar");
	// juara kelas
	$dbJK->delete_juara_kelas(null, null, $siswa_detail_id, "deleteSiswaKeluar");
	// status akhir semester
	$dbR->delete_status_semester(null, $siswa_detail_id, "deleteSiswaKeluar");
	// juara umum
	$dbJU->delete_juara_umum(null, null, $siswa_detail_id, "deleteSiswaKeluar");
	
	// delete siswa
	$db->delete_siswa_detail($siswa_detail_id, 'and status="keluar"');
	echo json_encode(['success'=>'yes']);
}