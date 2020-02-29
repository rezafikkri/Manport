<?php  

/**
* 
*/
class jurusan extends config {

	public function tampil_jurusan() {
		$tampil = $this->db->prepare("SELECT * FROM jurusan order by nama_jurusan asc");
		$tampil->execute();
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}
	
	public function add_jurusan() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'nama_jurusan[Nama Jurusan]' => 'required|maxLength[30]|unique[jurusan.nama_jurusan]',
		], false);
		// cek form errors
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$jurusan_id = config::generate_uuid();
		$nama_jurusan = filter_input(INPUT_POST, 'nama_jurusan', FILTER_SANITIZE_STRING);
		$insert = $this->db->prepare("INSERT into jurusan set jurusan_id=:jurusan_id, nama_jurusan=:nama_jurusan");
		$insert->execute([ ':jurusan_id' => $jurusan_id, ':nama_jurusan' => $nama_jurusan ]);
		return json_encode(["success"=>"yes"]);
	}

	public function delete_jurusan() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
		try {
			$del = $this->db->prepare("DELETE from jurusan where jurusan_id=:jurusan_id");
			$del->execute([ ':jurusan_id' => $jurusan_id ]);
		} catch (PDOException$e) {}
		if($del->rowCount() > 0) {
			return json_encode(["success"=>'yes']);
		} else {
			return false;
		}
	}

	public function get_one_jurusan($jurusan_id, $select=null) {
		if($select == null){
			$select = "*";
		}
		$tampil = $this->db->prepare("SELECT $select from jurusan where jurusan_id=:jurusan_id");
		$tampil->execute([ ':jurusan_id' => $jurusan_id ]);
		return $tampil->fetch(PDO::FETCH_ASSOC);
	}

	public function edit_jurusan() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
		$this->form_validation([
			'nama_jurusan[Nama Jurusan]' => 'required|maxLength[30]|unique[jurusan.nama_jurusan][jurusan_id.'.$jurusan_id.']',
		], false);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form errors
		if($this->has_formErrors()) {
			return false;
		}

		$nama_jurusan = filter_input(INPUT_POST, 'nama_jurusan', FILTER_SANITIZE_STRING);
		$edit = $this->db->prepare("UPDATE jurusan set nama_jurusan=:nama_jurusan where jurusan_id=:jurusan_id");
		$edit->execute([ ':nama_jurusan' => $nama_jurusan, ':jurusan_id' => $jurusan_id ]);
		return "success";
	}

	public function pesan_edit_jurusan() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_jurusan']) && $_SESSION['RAPORT']['pesan_edit_jurusan'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_jurusan']);
	    	return '<p class="pesan good">Jurusan berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_jurusan'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_jurusan']);
	    	return '<p class="pesan warning">Jurusan gagal diedit!</p>';
	    }
	}
}