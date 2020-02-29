<?php  

include '../../init.php';
$db = new raport;
if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

$dbS = new siswa;
$dbJK = new juara_kelas;
$dbWK = new wali_kelas;
$dbIS = new identitas_sekolah;
$dbIKK = new izin_kenaikan_kelas;
$dbUA = new user_admin;
$dbM = new mapel;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "cek_nilai_belum_dimasukkan") {
	$dataSiswa = $dbS->tampil_siswa_detail($_SESSION['RAPORT']['kelas_id'],'masih_sekolah','s.siswa_detail_id, s.nama_siswa');
	if($dataSiswa) {
		if($dbM->cek_has_mapel_for_cek_nilai_belum_dimasukkan($_SESSION['RAPORT']['kelas_id']) > 0) {
			$hasilCekNilaiBelum_dimasukkan = [];
			foreach($dataSiswa as $ds) {
				if($db->cek_nilai_belum_dimasukkan($ds['siswa_detail_id']) > 0) {
					array_push($hasilCekNilaiBelum_dimasukkan, ['siswa_detail_id'=>$ds['siswa_detail_id'], 'nama_siswa'=>$ds['nama_siswa']]);
				}
			}
			if(count($hasilCekNilaiBelum_dimasukkan) > 0) {
				echo json_encode(['nilai_belum_dimasukkan'=>$hasilCekNilaiBelum_dimasukkan]);
			}
		} else {
			echo json_encode(['mapel_null'=>'yes']);
		}
	}

} elseif($action == "juara_kelas") {
	if($dbJK->cek_has_juara_kelas() > 0) {
		echo json_encode(['juara_kelas'=>$dbJK->tampil_juara_kelas()]);

	} else {
		$dataSiswa = $dbS->tampil_siswa_detail($_SESSION['RAPORT']['kelas_id'],'masih_sekolah','s.siswa_detail_id, s.nama_siswa');
		if($dataSiswa) {
			if($dbM->cek_has_mapel_for_cek_nilai_belum_dimasukkan($_SESSION['RAPORT']['kelas_id']) > 0) {
				foreach($dataSiswa as $ds) {
					if($db->cek_nilai_belum_dimasukkan($ds['siswa_detail_id']) > 0) {
						echo json_encode(['nilai_belum_dimasukkan'=>'yes']);
						die;
					}
				} 
				echo json_encode(['juara_kelas'=>$dbJK->tentukan_juara_kelas()]);
			} else {
				echo json_encode(['mapel_null'=>'yes']);
			}
		}
	}

} elseif($action == "reload_juara_kelas") {
	$dataSiswa = $dbS->tampil_siswa_detail($_SESSION['RAPORT']['kelas_id'],'masih_sekolah','s.siswa_detail_id, s.nama_siswa');
	if($dataSiswa) {
		if($dbM->cek_has_mapel_for_cek_nilai_belum_dimasukkan($_SESSION['RAPORT']['kelas_id']) > 0) {
			foreach($dataSiswa as $ds) {
				if($db->cek_nilai_belum_dimasukkan($ds['siswa_detail_id']) > 0) {
					echo json_encode(['nilai_belum_dimasukkan'=>'yes']);
					die;
				}
			} 
			echo json_encode(['juara_kelas'=>$dbJK->tentukan_juara_kelas()]);
		} else {
			echo json_encode(['mapel_null'=>'yes']);
		}
	}

} elseif($action == "cek_status_akhir_belum_dimasukkan") {
	if($_SESSION['RAPORT']['semester'] == 2) {
		echo json_encode($db->cek_status_akhir_belum_dimasukkan());
	} else {
		echo json_encode(null);
	}

} elseif($action == "run_kenaikan_kelas") {
	if($_SESSION['RAPORT']['semester']==2) {
		// cek status perizinan kenaikan kelas
		$kelas = explode(".", $_SESSION['RAPORT']['kelas'])[0];
		$izinKenaikanKelas = $dbIKK->get_one_izin_kenaikan_kelas($kelas);
		if(!$izinKenaikanKelas) {
			echo json_encode(['izin_kenaikan_off'=>'yes']);
			die;
		} else if($izinKenaikanKelas && $izinKenaikanKelas['status'] != 'on'){
			echo json_encode(['izin_kenaikan_off'=>'yes']);
			die;
		}

		// cek password
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		if(!empty(trim($password))) {
			if($_SESSION['RAPORT']['level'] == 'admin') {
				$passwordDB = $dbUA->tampil_user_admin('password')['password'];
			} else {
				$passwordDB = $dbWK->get_one_wali_kelas($_SESSION['RAPORT']['wali_kelas_id'], 'wk.password')['password'];
			}
			if(!password_verify($password, $passwordDB)) {
				echo json_encode(['password_salah'=>'yes']);
				die;
			}

		} else {
			echo json_encode(['password_null'=>'yes']);
			die;
		}

		// cek nilai belum dimasukkan
		$dataSiswa = $dbS->tampil_siswa_detail($_SESSION['RAPORT']['kelas_id'],'masih_sekolah','s.siswa_detail_id, s.nama_siswa');
		if($dataSiswa) {
			if($dbM->cek_has_mapel_for_cek_nilai_belum_dimasukkan($_SESSION['RAPORT']['kelas_id']) > 0) {
				foreach($dataSiswa as $ds) {
					if($db->cek_nilai_belum_dimasukkan($ds['siswa_detail_id']) > 0) {
						echo json_encode(['nilai_belum_dimasukkan'=>'yes']);
						die;
					}
				}
			} else {
				echo json_encode(['mapel_null'=>'yes']);
				die;
			}
			// cek status akhir belum dimasukkan
			if($db->cek_status_akhir_belum_dimasukkan() > 0) {
				echo json_encode(['status_akhir_belum_dimasukkan'=>'yes']);
				die;
			}
			// run
			$dataStatus = $db->tampil_status_akhir_semester();
			$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
			foreach($dataStatus as $s) {
				// update kelas siswa
				if($db->cek_benar_kelas_siswa($dbS, $s['siswa_detail_id'])) {
					// jika lama belajar 3 tahun dan kelas >= XII atau lama belajar 4 tahun dan kelas >= XIII
					if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) {
						$dbS->update_kelas_or_status_siswa('status=:status, tahun_ajaran_kelulusan=:tahun_ajaran_kelulusan', [':status'=>$s['status_akhir'], ':tahun_ajaran_kelulusan'=>$_SESSION['RAPORT']['tahun_ajaran']], $s['siswa_detail_id']);
						
					} else {
						$kelas_idUp = explode("_", $s['status_akhir'])[3];
						if($kelas_idUp != $_SESSION['RAPORT']['kelas_id']) {
							$dbS->update_kelas_or_status_siswa('kelas_id=:kelas_id', [':kelas_id'=>$kelas_idUp], $s['siswa_detail_id']);
						}
					}
				}
			}
			// delete akun wali kelas
			$dbWK->delete_wali_kelas_where_kelas_id($_SESSION['RAPORT']['kelas_id']);
			// delete session raport
			unset($_SESSION['RAPORT']);
			echo json_encode(['success'=>'yes']);
		}
	}

} elseif($action == "tampil_siswa_tidak_naik_kelas_tidak_lulus") {
	$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
	if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) {
		$status = "= 'tidak_lulus'";
	} else {
		$status = "LIKE 'tinggal_di_kelas_%'";;
	}
	$data = $db->tampil_siswa_tidak_naik_kelas_tidak_lulus($status);
	// karena select menggunakan join dan memakai fungsi agregasi pada table yang dijoin, maka harus dicek apakah data benar-benar ada
	if(($data[0]['siswa_detail_id']??'') != null) {
		echo json_encode($data);
	} else {
		echo json_encode(null);
	}
}