<?php  

include '../../init.php';
$db = new siswa;
$dbIS = new identitas_sekolah;
$dbK = new kelas;
$dbR = new raport;
$dbJK = new juara_kelas;
$dbJU = new juara_umum;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "tampil_kelas") {
	// generate where
	$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
	if($lama_belajar == 3) {
		$where = "and kelas >= 'XII'";
	} else {
		$where = "and kelas >= 'XIII'";
	}
	$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
	if($jurusan_id) {
		echo json_encode($dbK->tampil_kelas($jurusan_id, $where, 'kelas_id, kelas'));
	} else {
		echo json_encode(null);
	}

} elseif($action == 'tampil_siswa_lulus') {
	$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
	echo json_encode($db->tampil_siswa_detail($kelas_id,'lulus','s.siswa_detail_id, s.nama_siswa, s.nisn, s.status'));

} elseif($action == 'edit_siswa_lulus') {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$db->form_validation([
		'status[Status]' => 'required|must[lulus,tidak_lulus,masih_sekolah]',
		'nama_siswa[Nama siswa]' => 'required|maxLength[30]',
		'nisn[NISN]' => 'required|maxLength[10]|minLength[10]|integer|unique[siswa_detail.nisn][siswa_detail_id.'.$siswa_detail_id.']',
		'no_un[Nomor UN]'=>'required|maxLength[40]'
	], false);
	$db->set_delimiter('<p class="pesan warning">','</p>');

	$jurusan = filter_input(INPUT_GET, 'jurusan', FILTER_SANITIZE_STRING);
	if($db->has_formErrors()) {
		$edit = false;
	} else {
		$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
		$nama_siswa = filter_input(INPUT_POST, 'nama_siswa', FILTER_SANITIZE_STRING);
		$nisn = filter_input(INPUT_POST, 'nisn', FILTER_SANITIZE_STRING);
		$no_un = filter_input(INPUT_POST, 'no_un', FILTER_SANITIZE_STRING);

		$updateSet = 'status=:status, nama_siswa=:nama_siswa, nisn=:nisn, no_un=:no_un';
		$execute = [':status'=>$status, ':nama_siswa'=>$nama_siswa, ':nisn'=>$nisn, ':no_un'=>$no_un];
		$edit = $db->update_kelas_or_status_siswa($updateSet, $execute, $siswa_detail_id);
	}

	if($edit == true && $status == "masih_sekolah") {
		$_SESSION['RAPORT']['pesan_edit_siswa_detail'] = 'masih_sekolah';
		header("Location: ".config::base_url('admin/index.php?ref=edit_siswa_lulus'));
		die;
	} else {
		$_SESSION['RAPORT']['pesan_edit_siswa_detail'] = $edit;
		header("Location: ".config::base_url('admin/index.php?ref=edit_siswa_lulus&siswa_detail_id='.$siswa_detail_id));
		die;
	}

} elseif($action == "delete_siswa_lulus") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	// sikap siswa
	$dbR->delete_sikap(null, $siswa_detail_id, "deleteSiswaLulus");
	// nilai
	$dbR->delete_nilai_deskripsi(null, $siswa_detail_id, "deleteSiswaLulus");
	// praktik kerja industri
	$dbR->delete_praktik_kerja_industri(null, true, "deleteSiswaLulus");
	// ekstrakurikuler
	$dbR->delete_ekstrakurikuler(null, true, "deleteSiswaLulus");
	// prestasi
	$dbR->delete_prestasi(null, true, "deleteSiswaLulus");
	// delete ketidakhadiran
	$dbR->delete_ketidakhadiran(null, $siswa_detail_id, "deleteSiswaLulus");
	// catatan wali kelas
	$dbR->delete_catatan_wali_kelas(null, $siswa_detail_id, "deleteSiswaLulus");
	// juara kelas
	$dbJK->delete_juara_kelas(null, null, $siswa_detail_id, "deleteSiswaLulus");
	// status akhir semester
	$dbR->delete_status_semester(null, $siswa_detail_id, "deleteSiswaLulus");
	// juara umum
	$dbJU->delete_juara_umum(null, null, $siswa_detail_id, "deleteSiswaLulus");
	
	// delete siswa
	$db->delete_siswa_detail($siswa_detail_id, 'and (status="lulus" or status="tidak_lulus")');
	echo json_encode(['success'=>'yes']);
}