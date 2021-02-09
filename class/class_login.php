<?php  

/**
* 
*/
class login extends config {

	public function proses_login_admin($dbTA, $dbSem) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation([
			'username[Username]' => 'required',
			'password[Password]' => 'required',
		], true);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form error
		if($this->has_formErrors()) {
			return false;
		}

		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

		$get = $this->db->prepare("SELECT * FROM admin where username=:username");
		$get->execute([ ':username' => $username ]);
		$r = $get->fetch(PDO::FETCH_ASSOC);

		if($r) {
			if(password_verify($password,$r['password'])) {
				$data_tahun_ajaran = $dbTA->get_tahun_ajaran_status_run();
				$data_semester = $dbSem->get_semester_status_yes();
				if($data_tahun_ajaran) {
					$_SESSION['RAPORT']['tahun_ajaran_id'] = $data_tahun_ajaran['tahun_ajaran_id'];
		    		$_SESSION['RAPORT']['tahun_ajaran'] = $data_tahun_ajaran['tahun'];
				}
				if($data_semester) {
					$_SESSION['RAPORT']['semester_id'] = $data_semester['semester_id'];
					$_SESSION['RAPORT']['semester'] = $data_semester['semester'];
				}
				$_SESSION['RAPORT']['statusLogin'] = 'yes';
				$_SESSION['RAPORT']['level'] = $r['level'];
				$_SESSION['RAPORT']['nama'] = $r['username'];
				$_SESSION['RAPORT']['admin_id'] = $r['admin_id'];

				return "success";

			} else {
				return "passwordWorng";
			}
		}
		else{
			return "usernameNotFound";
		}
	}

	public function proses_login_guru($dbTA, $dbSem) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation([
			'nama[Nama]' => 'required',
			'password[Password]' => 'required',
		], false);
		// cek form error
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

		$data_tahun_ajaran = $dbTA->get_tahun_ajaran_status_run();
		$data_semester = $dbSem->get_semester_status_yes();

		if($data_tahun_ajaran == true && $data_semester == true) {
			$get = $this->db->prepare("SELECT wk.*, k.jurusan_id, k.kelas, j.nama_jurusan
				FROM wali_kelas as wk
				JOIN kelas as k USING(kelas_id)
				JOIN jurusan as j USING(jurusan_id)
				where nama=:nama");
			$get->execute([ ':nama' => $nama ]);
			$r = $get->fetch(PDO::FETCH_ASSOC);
			if($r) {
				if(password_verify($password,$r['password'])) {
					$_SESSION['RAPORT']['tahun_ajaran_id'] = $data_tahun_ajaran['tahun_ajaran_id'];
			    	$_SESSION['RAPORT']['tahun_ajaran'] = $data_tahun_ajaran['tahun'];
					$_SESSION['RAPORT']['semester_id'] = $data_semester['semester_id'];
					$_SESSION['RAPORT']['semester'] = $data_semester['semester'];
					$_SESSION['RAPORT']['statusLogin'] = 'yes';
					$_SESSION['RAPORT']['level'] = $r['level'];
					$_SESSION['RAPORT']['nama'] = $r['nama'];
					$_SESSION['RAPORT']['jurusan_id'] = $r['jurusan_id'];
					$_SESSION['RAPORT']['jurusan'] = $r['nama_jurusan'];
					$_SESSION['RAPORT']['kelas_id'] = $r['kelas_id'];
					$_SESSION['RAPORT']['kelas'] = $r['kelas'];
					$_SESSION['RAPORT']['wali_kelas_id'] = $r['wali_kelas_id'];

					return json_encode(["success"=>'yes']);

				} else {
					return json_encode(["errors"=>["password"=>"Password salah!"]]);
				}
			} else {
				return json_encode(["errors"=>["nama"=>"Nama tidak ditemukan!"]]);
			}
		}
		else{
			return json_encode(['errors'=>["izin"=>"Login belum diizinkan, hubungi admin!"]]);
		}
	}

	// for halaman guru yang diakses oleh admin
	public function make_session_kelas_jurusan($dbWK, $dbJ, $dbK) {
		$jurusan_id = filter_input(INPUT_GET, 'jurusan_id', FILTER_SANITIZE_STRING);
		$kelas_id = filter_input(INPUT_GET, 'kelas_id', FILTER_SANITIZE_STRING);
		if(empty(trim($jurusan_id)) || empty(trim($kelas_id))) {
		    return false;

		} elseif(isset($_SESSION['RAPORT']['statusLogin']) && $_SESSION['RAPORT']['level'] == "admin") {
			// jika jurusan_id tidak sama dengan jurusan_id session
			if($jurusan_id != ($_SESSION['RAPORT']['jurusan_id']??'')) {
				$jurusan = $dbJ->get_one_jurusan($jurusan_id, 'nama_jurusan');
				if($jurusan) {
					$_SESSION['RAPORT']['jurusan_id'] = $jurusan_id;
		    		$_SESSION['RAPORT']['jurusan'] = $jurusan['nama_jurusan'];
				} else {
					return false;
				}
			}
			// jika kelas_id tidak sama dengan kelas_id session
			if($kelas_id != ($_SESSION['RAPORT']['kelas_id']??'')) {
				$kelas = $dbK->get_one_kelas($kelas_id, 'kelas', 'and jurusan_id=:jurusan_id', [':jurusan_id'=>$jurusan_id]);
				if($kelas) {
					$_SESSION['RAPORT']['kelas_id'] = $kelas_id;
					$_SESSION['RAPORT']['kelas'] = $kelas['kelas'];
				} else {
					unset($_SESSION['RAPORT']['jurusan_id']);
					unset($_SESSION['RAPORT']['jurusan']);
					unset($_SESSION['RAPORT']['kelas_id']);
					unset($_SESSION['RAPORT']['kelas']);
					unset($_SESSION['RAPORT']['wali_kelas_id']);
					return false;
				}
				$wali_kelas_id = $dbWK->get_one_wali_kelas_not_where_wali_kelas_id('wali_kelas_id', 'kelas_id=:kelas_id', [':kelas_id'=>$kelas_id]);
				if($wali_kelas_id) {
					$_SESSION['RAPORT']['wali_kelas_id'] = $wali_kelas_id['wali_kelas_id'];
					return true;
				} else {
					unset($_SESSION['RAPORT']['wali_kelas_id']);
					return false;
				}

			} else {
				$wali_kelas_id = $dbWK->get_one_wali_kelas_not_where_wali_kelas_id('wali_kelas_id', 'kelas_id=:kelas_id', [':kelas_id'=>$kelas_id]);
				if($wali_kelas_id && !isset($_SESSION['RAPORT']['wali_kelas_id'])) {
					$_SESSION['RAPORT']['wali_kelas_id'] = $wali_kelas_id['wali_kelas_id'];
					return true;
				} elseif(!$wali_kelas_id && isset($_SESSION['RAPORT']['wali_kelas_id'])) {
					unset($_SESSION['RAPORT']['wali_kelas_id']);
					return false;
				}
			}
		    return true;
		}
	}

	public function pesan_login() {
	    if(isset($_SESSION['RAPORT']['usernameNotFound'])) {
	    	unset($_SESSION['RAPORT']['usernameNotFound']);
	    	return '<p class="pesan warning">Username tidak ditemukan!</p>';
	    }
	    if(isset($_SESSION['RAPORT']['passwordWorng'])) {
	    	unset($_SESSION['RAPORT']['passwordWorng']);
	    	return '<p class="pesan warning">Password salah!</p>';
	    }
	}
}
