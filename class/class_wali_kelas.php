<?php
/**
* 
*/
class wali_kelas extends config {
	
	public function add_wali_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'kelas_id[Kelas]' => 'required',
			'nama[Nama]' => 'required|maxLength[50]|unique[wali_kelas.nama]',
			'nip[NIP]' => 'required|maxLength[18]|integer',
			'password[Password]' => 'required',
		], false);
		// cek form error
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$kelas = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
		$nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
		$nip = filter_input(INPUT_POST, 'nip', FILTER_SANITIZE_STRING);
		$password = password_hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING), PASSWORD_ARGON2I);
		$wali_kelas_id = config::generate_uuid();

		try {
			$add = $this->db->prepare("INSERT into wali_kelas set wali_kelas_id=:wali_kelas_id, kelas_id=:kelas_id, nama=:nama,password=:password,nip=:nip,level=:level");
			$add->execute([ ':wali_kelas_id'=>$wali_kelas_id, ':kelas_id' => $kelas, ':nama' => $nama, ':nip' => $nip, ':password'=>$password,':level'=>'guru' ]);
		} catch (PDOException $e) {
			return false;
		}
		return json_encode(["success"=>'yes']);
	}

	public function edit_wali_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$wali_kelas_id = filter_input(INPUT_POST, 'wali_kelas_id', FILTER_SANITIZE_STRING);
		$this->form_validation([
			'kelas[Kelas]' => 'required',
			'nama[Nama]' => 'required|maxLength[50]|unique[wali_kelas.nama][wali_kelas_id.'.$wali_kelas_id.']',
			'nip[NIP]' => 'required|maxLength[18]|integer',
		], false);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form error
		if($this->has_formErrors()) {
			return false;
		}

		$kelas = filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING);
		$nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
		$nip = filter_input(INPUT_POST, 'nip', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

		if(empty(trim($password))) {
			try {
				$edit = $this->db->prepare("UPDATE wali_kelas set kelas_id=:kelas_id, nama=:nama, nip=:nip,level=:level where wali_kelas_id=:wali_kelas_id");
				$edit->execute([ ':kelas_id' => $kelas, ':nama' => $nama, ':nip' => $nip, ':level'=>'guru', ':wali_kelas_id'=>$wali_kelas_id ]);
			} catch (PDOException $e) {
				return false;
			}
			return "success";

		} else if(!empty(trim($password))) {
			try {
				$password = password_hash($password, PASSWORD_ARGON2I);
				$edit = $this->db->prepare("UPDATE wali_kelas set kelas_id=:kelas_id, nama=:nama, password=:password,nip=:nip,level=:level where wali_kelas_id=:wali_kelas_id");
				$edit->execute([ ':kelas_id' => $kelas, ':nama' => $nama, ':nip' => $nip, ':password'=>$password, ':level'=>'guru', ':wali_kelas_id'=>$wali_kelas_id ]);
			} catch (PDOException $e) {
				return false;
			}
			return "success";

		}
		return false;
	}

	public function tampil_wali_kelas() {
	    $get = $this->db->prepare("SELECT wk.*, k.kelas, j.nama_jurusan 
	    	FROM wali_kelas as wk
	    	JOIN kelas as k USING(kelas_id)
	    	JOIN jurusan as j USING(jurusan_id) order by kelas asc");
	    $get->execute();
	    while($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    }
	    return @$hasil;
	}

	public function get_one_wali_kelas($wali_kelas_id, $select=null, $where=null, $execute=null) {
		if($select == null) {
			$select = "wk.*, k.kelas, k.jurusan_id";
			$join = "JOIN kelas as k USING(kelas_id)";
		} else {
			$join = null;
		}

		$where1 = "wali_kelas_id=:wali_kelas_id";
		$execute1 = [ ':wali_kelas_id'=>$wali_kelas_id ];

		if($execute != null) {
			$execute1 = array_merge($execute1, $execute);
		}
		
	    $get = $this->db->prepare("SELECT $select FROM 
	    	wali_kelas as wk
	    	$join
	    	where $where1 $where");
	    $get->execute($execute1);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function get_one_wali_kelas_not_where_wali_kelas_id($select,$where,$execute) {
	    $get = $this->db->prepare("SELECT $select from wali_kelas where $where");
	    $get->execute($execute);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function delete_wali_kelas($wali_kelas_id) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

	   	$del = $this->db->prepare("DELETE FROM wali_kelas where wali_kelas_id=:wali_kelas_id");
	    $del->execute([ ':wali_kelas_id'=>$wali_kelas_id ]);
	    if($del->rowCount() > 0) {
	    	return json_encode(['success'=>'yes']);
	    }
	    return false;
	}

	public function delete_wali_kelas_where_kelas_id($kelas_id) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

	   	$del = $this->db->prepare("DELETE FROM wali_kelas where kelas_id=:kelas_id");
	    $del->execute([ ':kelas_id'=>$kelas_id ]);
	    if($del->rowCount() > 0) {
	    	return json_encode(['success'=>'yes']);
	    }
	    return false;
	}

	public function pesan_edit_wali_kelas() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_wali_kelas']) && $_SESSION['RAPORT']['pesan_edit_wali_kelas'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_wali_kelas']);
	    	return '<p class="pesan good">Wali kelas berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_wali_kelas'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_wali_kelas']);
	    	return '<p class="pesan warning">Wali kelas gagal diedit!</p>';
	    }
	}
}