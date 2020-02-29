<?php  

include '../../init.php';
$db = new raport;
if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

$dbM = new mapel;
$dbS = new siswa;
$dbJK = new juara_kelas;
$dbJU = new juara_umum;
$dbK = new kelas;
$dbIS = new identitas_sekolah;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "add_edit_sikap") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	if($db->cek_has_sikap_siswa($siswa_detail_id) > 0) {
		$edit = $db->edit_sikap($dbS);
		$_SESSION['RAPORT']['pesan_edit_sikap'] = $edit;
		header("Location: ".config::base_url('guru/index.php?ref=edit_sikap&siswa_detail_id='.$siswa_detail_id));
		die;

	} else {
		if($db->add_sikap($dbS) == true) {
			header("Location: ".config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id));
			die;
		} else {
			$_SESSION['RAPORT']['pesan_add_sikap'] = 'gagal';
			header("Location: ".config::base_url('guru/index.php?ref=add_sikap&siswa_detail_id='.$siswa_detail_id));
			die;
		}
	}

} elseif($action == "add_edit_nilai_deskripsi") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	if($db->cek_has_nilai_deskripsi($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id']??'', $_SESSION['RAPORT']['semester_id']??'') > 0) {
		$edit = $db->edit_nilai_deskripsi($dbM, $dbS);
		$_SESSION['RAPORT']['pesan_edit_nilai_deskripsi'] = $edit;
		header("Location: ".config::base_url('guru/index.php?ref=edit_nilai_deskripsi&siswa_detail_id='.$siswa_detail_id));
		die;

	} else {
		if($db->add_nilai_deskripsi($dbM, $dbS) == true) {
			header("Location: ".config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id));
			die;
		} else {
			$_SESSION['RAPORT']['pesan_add_nilai_deskripsi'] = 'gagal';
			header("Location: ".config::base_url('guru/index.php?ref=add_nilai_deskripsi&siswa_detail_id='.$siswa_detail_id));
			die;
		}
	}

} elseif($action == "add_praktik_kerja_industri") {
	echo $db->add_praktik_kerja_industri($dbS);

} elseif($action == "delete_praktik_kerja_industri") {
	echo $db->delete_praktik_kerja_industri($dbS);

} elseif($action == "add_ekstrakurikuler") {
	echo $db->add_ekstrakurikuler($dbS);

} elseif($action == "delete_ekstrakurikuler") {
	echo $db->delete_ekstrakurikuler($dbS);

} elseif($action == "add_prestasi") {
	echo $db->add_prestasi($dbS);

} elseif($action == "delete_prestasi") {
	echo $db->delete_prestasi($dbS);

} elseif($action == "add_edit_ketidakhadiran") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	if($db->cek_has_ketidakhadiran($siswa_detail_id) > 0) {
		$edit = $db->edit_ketidakhadiran($dbS);
		$_SESSION['RAPORT']['pesan_edit_ketidakhadiran'] = $edit;
		header("Location: ".config::base_url('guru/index.php?ref=edit_ketidakhadiran&siswa_detail_id='.$siswa_detail_id));
		die;

	} else {
		if($db->add_ketidakhadiran($dbS) == true) {
			header("Location: ".config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id));
			die;
		} else {
			$_SESSION['RAPORT']['pesan_add_ketidakhadiran'] = 'gagal';
			header("Location: ".config::base_url('guru/index.php?ref=add_ketidakhadiran&siswa_detail_id='.$siswa_detail_id));
			die;
		}
	}

} elseif($action == "add_edit_catatan_wali_kelas") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	if($db->cek_has_catatan_wali_kelas($siswa_detail_id) > 0) {
		$edit = $db->edit_catatan_wali_kelas($dbS);
		$_SESSION['RAPORT']['pesan_edit_catatan_wali_kelas'] = $edit;
		header("Location: ".config::base_url('guru/index.php?ref=edit_catatan_wali_kelas&siswa_detail_id='.$siswa_detail_id));
		die;

	} else {
		if($db->add_catatan_wali_kelas($dbS) == true) {
			header("Location: ".config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id));
			die;
		} else {
			$_SESSION['RAPORT']['pesan_add_catatan_wali_kelas'] = 'gagal';
			header("Location: ".config::base_url('guru/index.php?ref=add_catatan_wali_kelas&siswa_detail_id='.$siswa_detail_id));
			die;
		}
	}

} elseif($action == "add_edit_status_akhir_semester") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	if($db->cek_has_status_akhir_semester($siswa_detail_id) > 0) {
		$edit = $db->edit_status_akhir_semester($dbS, $dbIS, $dbK);
		$_SESSION['RAPORT']['pesan_edit_status_akhir_semester'] = $edit;
		header("Location: ".config::base_url('guru/index.php?ref=edit_status_akhir_semester&siswa_detail_id='.$siswa_detail_id));
		die;

	} else {
		if($db->add_status_akhir_semester($dbS, $dbIS, $dbK) == true) {
			header("Location: ".config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id));
			die;
		} else {
			$_SESSION['RAPORT']['pesan_add_status_akhir_semester'] = 'gagal';
			header("Location: ".config::base_url('guru/index.php?ref=add_status_akhir_semester&siswa_detail_id='.$siswa_detail_id));
			die;
		}
	}

} elseif($action == "reset_data_raport") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	// sikap siswa
	$db->delete_sikap($dbS, $siswa_detail_id);
	// nilai
	$db->delete_nilai_deskripsi($dbS, $siswa_detail_id);
	// praktik kerja industri
	$db->delete_praktik_kerja_industri($dbS, true);
	// ekstrakurikuler
	$db->delete_ekstrakurikuler($dbS, true);
	// prestasi
	$db->delete_prestasi($dbS, true);
	// delete ketidakhadiran
	$db->delete_ketidakhadiran($dbS, $siswa_detail_id);
	// catatan wali kelas
	$db->delete_catatan_wali_kelas($dbS, $siswa_detail_id);
	// juara kelas
	$dbJK->delete_juara_kelas($dbS, $db, $siswa_detail_id);
	// status akhir semester
	if(($_SESSION['RAPORT']['semester']??'') == 2) {
		$db->delete_status_semester($dbS, $siswa_detail_id);
	}
	// juara umum
	$dbJU->delete_juara_umum($db, $dbS, $siswa_detail_id);
	echo json_encode(['success'=>'yes']);

} elseif($action == "tampil_raport_ajax") {
	$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$arrSemester = explode(".", filter_input(INPUT_POST, 'semester_id', FILTER_SANITIZE_STRING));
	$semester_id = $arrSemester[0];
	$semester = $arrSemester[1]??1;
	$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_SANITIZE_STRING);
	// get sikap
	$sikap = $db->tampil_sikap($siswa_detail_id, $tahun_ajaran_id, $semester_id)['sikap']??null;
	$data = ['sikap'=>$sikap];
	// get nilai deskripsi
	$cek_has_nilai_deskripsi = $db->cek_has_nilai_deskripsi($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	if($cek_has_nilai_deskripsi) {
		$nilai_deskripsi = $db->tampil_nilai($siswa_detail_id, $tahun_ajaran_id, $semester_id);
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
	// praktik kerja industri
	$praktik_kerja_industri = $db->tampil_praktik_kerja_industri($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	$data = array_merge($data, ['praktik_kerja_industri'=>$praktik_kerja_industri]);
	// ekstrakurikuler
	$ekstrakurikuler = $db->tampil_ekstrakurikuler($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	if($ekstrakurikuler) {
		$k = 0;
		foreach($ekstrakurikuler as $e) {
			$ekstrakurikuler[$k]['nilai'] = str_replace(".", ",", $e['nilai']);
			$k++;
		}
		$data = array_merge($data, ['ekstrakurikuler'=>$ekstrakurikuler]);
	}
	// prestasi
	$prestasi = $db->tampil_prestasi($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	$data = array_merge($data, ['prestasi'=>$prestasi]);
	// ketidakhadiran
	$ketidakhadiran = $db->tampil_ketidakhadiran($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	if($ketidakhadiran) {
		$data = array_merge($data, ['ketidakhadiran'=>$ketidakhadiran]);
	}
	// catatan wali kelas
	$catatan_wali_kelas = $db->tampil_catatan_wali_kelas($siswa_detail_id, $tahun_ajaran_id, $semester_id)['catatan']??null;
	$data = array_merge($data, ['catatan_wali_kelas'=>$catatan_wali_kelas]);
	// status akhir semester
	if($semester == 2) {		
		$status_akhir = $db->get_one_status_akhir_semester($siswa_detail_id, $tahun_ajaran_id, $semester_id)['status_akhir']??null;

		if($status_akhir) {
			if($status_akhir == "lulus" || $status_akhir == "tidak_lulus") {
				$status_akhir = '<p class="naikTinggal marginTop40px"><b>'.strtoupper(str_replace("_", " ", $status_akhir)).'</b></p>';
		 
			} else {
				$arrStatus_akhir = explode("_", $status_akhir);
				$arrKelas = explode(".", $dbK->get_one_kelas($arrStatus_akhir[3]??'', 'kelas')['kelas']??'');
				$jurusan = $dbK->get_jurusan_where_kelas_id($arrStatus_akhir[3]??'', "nama_jurusan")['nama_jurusan']??'';
				$status_akhir = '<p class="naikTinggal marginTop40px"><span>'.$arrStatus_akhir[0].'</span> '.($arrStatus_akhir[1]??'').' '.($arrStatus_akhir[2]??'').' <b>'. ($arrKelas[0]??'').' '.$jurusan.' '.($arrKelas[1]??'').'</b></p>';
			}
			$data = array_merge($data, ['status_akhir'=>$status_akhir]);
		}
	}
	echo json_encode($data);
}