<?php

include '../init.php';
$db = new raport;
$dbS = new siswa;
$dbK = new kelas;
$dbL = new login;
$dbTA = new tahun_ajaran;
$dbSem = new semester;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "tampil_raport") {
	// form validation
	$db->form_validation([
		'nama_siswa[Nama siswa]' => 'required',
		'nisn[NISN]' => 'required',
		'tahun_ajaran_id[Tahun ajaran]' => 'required',
		'semester_id[Semester]' => 'required'
	], false);
	$errors = $db->get_form_errors();
	if($errors) {
		echo json_encode(['errors'=>$errors]);
		die;
	}
	
	$nama_siswa = filter_input(INPUT_POST, 'nama_siswa', FILTER_SANITIZE_STRING);
	$nisn = filter_input(INPUT_POST, 'nisn', FILTER_SANITIZE_STRING);
	$arrSemester = explode(".", filter_input(INPUT_POST, 'semester_id', FILTER_SANITIZE_STRING));
	$semester_id = $arrSemester[0];
	$semester = $arrSemester[1]??1;
	$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_SANITIZE_STRING);
	$siswa_detail_id = $dbS->get_one_siswa_detail_not_where_siswa_detail_id("siswa_detail_id","nama_siswa=:nama_siswa and nisn=:nisn",[':nama_siswa'=>$nama_siswa, ':nisn'=>$nisn])['siswa_detail_id']??null;
	// jika siswa ada
	if($siswa_detail_id) {
		// get sikap
		$sikap = $db->tampil_sikap($siswa_detail_id, $tahun_ajaran_id, $semester_id)['sikap']??'';
		$data = ['sikap'=>$sikap];
		// get nilai deskripsi
		$cek_has_nilai_deskripsi = $db->cek_has_nilai_deskripsi($siswa_detail_id, $tahun_ajaran_id, $semester_id, "siswa_lihat_raport");
		if($cek_has_nilai_deskripsi) {
			$nilai_deskripsi = $db->tampil_nilai($siswa_detail_id, $tahun_ajaran_id, $semester_id, null, "siswa_lihat_raport");
			if($nilai_deskripsi) {
				$i = 0;
				foreach($nilai_deskripsi as $nd) {
					$nilai_deskripsi[$i]['predikat_p'] = $db->generate_predikat($nd['nilai_p'], $nd['predikat_d'], $nd['predikat_c'], $nd['predikat_b'], $nd['predikat_a']);
					$nilai_deskripsi[$i]['predikat_k'] = $db->generate_predikat($nd['nilai_k'], $nd['predikat_d'], $nd['predikat_c'], $nd['predikat_b'], $nd['predikat_a']);
					$nilai_deskripsi[$i]['nilai_p'] = str_replace(".", ",", $nd['nilai_p']);
					$nilai_deskripsi[$i]['nilai_k'] = str_replace(".", ",", $nd['nilai_k']);
					unset($nilai_deskripsi[$i]['predikat_a']);
					unset($nilai_deskripsi[$i]['predikat_b']);
					unset($nilai_deskripsi[$i]['predikat_c']);
					unset($nilai_deskripsi[$i]['predikat_d']);
					$i++;
				}
				$data = array_merge($data, ['nilai_deskripsi'=>$nilai_deskripsi]);
			}
		}
		// praktik kerja industri
		$praktik_kerja_industri = $db->tampil_praktik_kerja_industri($siswa_detail_id, $tahun_ajaran_id, $semester_id);
		$data = array_merge($data, ['praktik_kerja_industri'=>$praktik_kerja_industri]);
		// ekstrakurikuler
		$ekstrakurikuler = $db->tampil_ekstrakurikuler($siswa_detail_id, $tahun_ajaran_id, $semester_id);
		$data = array_merge($data, ['ekstrakurikuler'=>$ekstrakurikuler]);
		// prestasi
		$prestasi = $db->tampil_prestasi($siswa_detail_id, $tahun_ajaran_id, $semester_id);
		$data = array_merge($data, ['prestasi'=>$prestasi]);
		// ketidakhadiran
		$ketidakhadiran = $db->tampil_ketidakhadiran($siswa_detail_id, $tahun_ajaran_id, $semester_id);
		if($ketidakhadiran) {
			$data = array_merge($data, ['ketidakhadiran'=>$ketidakhadiran]);
		}
		// catatan wali kelas
		$catatan_wali_kelas = $db->tampil_catatan_wali_kelas($siswa_detail_id, $tahun_ajaran_id, $semester_id)['catatan']??'';
		$data = array_merge($data, ['catatan_wali_kelas'=>$catatan_wali_kelas]);
		// status akhir semester
		if($semester == 2) {		
			$status_akhir = $db->get_one_status_akhir_semester($siswa_detail_id, $tahun_ajaran_id, $semester_id)['status_akhir']??'';

			if($status_akhir) {
				if($status_akhir != "lulus" && $status_akhir != "tidak_lulus") {
					$arrStatus_akhir = explode("_", $status_akhir);
					$arrKelas = explode(".", $dbK->get_one_kelas($arrStatus_akhir[3]??'', 'kelas')['kelas']??'');
					$jurusan = $dbK->get_jurusan_where_kelas_id($arrStatus_akhir[3]??'', "nama_jurusan")['nama_jurusan']??'';
					$status_akhir = '<p class="naikTinggal marginTop40px"><span>'.$arrStatus_akhir[0].'</span> '.($arrStatus_akhir[1]??'').' '.($arrStatus_akhir[2]??'').' <b>'. ($arrKelas[0]??'').' '.$jurusan.' '.($arrKelas[1]??'').'</b></p>';
					$data = array_merge($data, ['status_akhir'=>$status_akhir]);
				} else {
					$data = array_merge($data, ['no_status_akhir'=>'yes']);
				}
			} else {
				$data = array_merge($data, ['null_status_akhir'=>'yes']);
			}
		}
		echo json_encode($data);
	} else {
		echo json_encode(['errors'=>['siswa_detail_id'=>'Data tidak ditemukan, Nama siswa atau NISN salah, mohon dicek kembali!']]);
	}

} elseif($action == "login_guru") {
	echo $dbL->proses_login_guru($dbTA, $dbSem);

} elseif($action == "logout") {
	unset($_SESSION['RAPORT']);
	header("Location: ../");
	die;
}
