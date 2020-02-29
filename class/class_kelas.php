<?php

/**
* 
*/
class kelas extends config {

	public function count_jml_kelas() {
	    $count = $this->db->prepare("SELECT kelas_id from kelas");
	    $count->execute();
	    return $count->rowCount();
	}
	
	public function tampil_kelas($jurusan_id=null, $where1=null, $select=null) {
		if($select == null) {
			$select = "*";
		}
		if($jurusan_id != null) {
			$where = "where jurusan_id=:jurusan_id";
			$execute = [':jurusan_id'=>$jurusan_id];
		} else {
			$where = null;
			$execute = null;
		}
		$tampil = $this->db->prepare("SELECT $select from kelas $where $where1 order by kelas asc");
		$tampil->execute($execute);
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}
	
	public function add_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'jurusan_id[Jurusan]' => 'required',
			'kelas[Kelas]' => 'required|maxLength[6]|regex[ /^X([IV]+)?(.[0-9a-z]+)?\z/i ]',
		], false);
		// cek form errors
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$kelas = strtoupper(filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING));
		$jurusan_id = filter_input(INPUT_POST, 'jurusan_id', FILTER_SANITIZE_STRING);
		$kelas_id = config::generate_uuid();
		try {
			$tambah = $this->db->prepare("INSERT into kelas set kelas_id=:kelas_id, jurusan_id=:jurusan_id, kelas=:kelas");
			$tambah->execute([ ':kelas_id' => $kelas_id, ':jurusan_id'=>$jurusan_id, ':kelas' => $kelas ]);
		} catch (PDOException $e) {
			return false;
		}
		return json_encode(["success"=>"yes"]);
	}

	public function edit_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'jurusan[Jurusan]' => 'required',
			'kelas[Kelas]' => 'required|maxLength[6]|regex[ /^X([IV]+)?(.[0-9a-z]+)?\z/i ]',
		], false);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form errors
		if($this->has_formErrors()) {
			return false;
		}
		
		$kelas = strtoupper(filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING));
		$jurusan_id = filter_input(INPUT_POST, 'jurusan', FILTER_SANITIZE_STRING);
		$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
		try {
			$edit = $this->db->prepare("UPDATE kelas set jurusan_id=:jurusan_id, kelas=:kelas where kelas_id=:kelas_id");
			$edit->execute([ ':jurusan_id'=>$jurusan_id, ':kelas' => $kelas, ':kelas_id' => $kelas_id ]);
		} catch (PDOException $e) {
			return false;
		}
		return "success";
	}

	public function get_one_kelas($kelas_id, $select=null, $where=null, $execute=null) {
		if($select == null) {
			$select = "*";
		}
		$execute1 = [ ':kelas_id' => $kelas_id ];
		if($execute != null) $execute1 = array_merge($execute1, $execute);
		$get = $this->db->prepare("SELECT $select from kelas where kelas_id=:kelas_id $where");
		$get->execute($execute1);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function get_jurusan_where_kelas_id($kelas_id, $select) {
	    $get = $this->db->prepare("SELECT $select FROM kelas JOIN jurusan USING(jurusan_id) where kelas_id=:kelas_id");
	    $get->execute([':kelas_id'=>$kelas_id]);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function delete_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
		$dataIn = config::change_idForQuery_IN($kelas_id);
		if($dataIn == null) {
			return json_encode(['dataNull'=>'yes']);
		} else {
			$kelas_id = $dataIn['id'];
			$questionmarks = $dataIn['questionmarks'];
		}

		try {
			$del = $this->db->prepare("DELETE from kelas where kelas_id in($questionmarks)");
			$del->execute($kelas_id);
		} catch (PDOException $e) {}
		if($del->rowCount() > 0) {
			return json_encode(['success'=>'yes']);
		}
		return false;
	}

	public function pesan_edit_kelas() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_kelas']) && $_SESSION['RAPORT']['pesan_edit_kelas'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_kelas']);
	    	return '<p class="pesan good">Kelas berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_kelas'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_kelas']);
	    	return '<p class="pesan warning">Kelas gagal diedit!</p>';
	    }
	}
}