<?php  

/**
* 
*/
class kkm extends config {
	
	public function insert_kkm() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'kkm[Kkm]' => 'required|maxLength[3]|unique[kkm.kkm]',
			'kurang[Interval Kurang]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
			'cukup[Interval Cukup]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
			'baik[Interval Baik]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
			'sangat_baik[Interval Sangat baik]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
		], false);
		// cek form error
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$kurang = filter_input(INPUT_POST, 'kurang', FILTER_SANITIZE_STRING);
		$cukup = filter_input(INPUT_POST, 'cukup', FILTER_SANITIZE_STRING);
		$baik = filter_input(INPUT_POST, 'baik', FILTER_SANITIZE_STRING);
		$sangat_baik = filter_input(INPUT_POST, 'sangat_baik', FILTER_SANITIZE_STRING);
		$kkm = filter_input(INPUT_POST, 'kkm', FILTER_SANITIZE_STRING);
		$kkm_id = config::generate_uuid();

		$insert = $this->db->prepare("INSERT into kkm set kkm_id=:kkm_id, kkm=:kkm, predikat_d=:kurang, predikat_c=:cukup, predikat_b=:baik, predikat_a=:sangat_baik");
		$insert->execute([ ':kkm_id' => $kkm_id, ':kkm' => $kkm, ':kurang' => $kurang, ':cukup' => $cukup, ':baik' => $baik, ':sangat_baik' => $sangat_baik ]);
		return json_encode(['success'=>"yes"]);
	}

	public function tampil_kkm() {
		$get = $this->db->prepare("SELECT * from kkm order by kkm asc");
		$get->execute();
		while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}

	public function get_one_kkm($kkm_id) {
		$get = $this->db->prepare("SELECT * from kkm where kkm_id=:kkm_id");
		$get->execute([ ':kkm_id' => $kkm_id ]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function edit_kkm() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$kkm_id = filter_input(INPUT_POST, 'kkm_id', FILTER_SANITIZE_STRING);
		$this->form_validation([
			'kkm[Kkm]' => 'required|maxLength[3]|unique[kkm.kkm][kkm_id.'.$kkm_id.']',
			'kurang[Interval Kurang]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
			'cukup[Interval Cukup]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
			'baik[Interval Baik]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
			'sangat_baik[Interval Sangat baik]' => 'required|maxLength[6]|regex[ /^\d+-\d+\z/ ]',
		], false);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form error
		if($this->has_formErrors()) {
			return false;
		}

		$kkm = filter_input(INPUT_POST, 'kkm', FILTER_SANITIZE_STRING);
		$kurang = filter_input(INPUT_POST, 'kurang', FILTER_SANITIZE_STRING);
		$cukup = filter_input(INPUT_POST, 'cukup', FILTER_SANITIZE_STRING);
		$baik = filter_input(INPUT_POST, 'baik', FILTER_SANITIZE_STRING);
		$sangat_baik = filter_input(INPUT_POST, 'sangat_baik', FILTER_SANITIZE_STRING);

		$update = $this->db->prepare("UPDATE kkm set kkm=:kkm, predikat_d=:kurang, predikat_c=:cukup, predikat_b=:baik, predikat_a=:sangat_baik where kkm_id=:kkm_id");
		$update->execute([ ':kkm' => $kkm, ':kurang' => $kurang, ':cukup' => $cukup, ':baik' => $baik, ':sangat_baik' => $sangat_baik, ':kkm_id' => $kkm_id ]);
		return "success";
	}

	public function delete_kkm() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$kkm_id = filter_input(INPUT_POST, 'kkm_id', FILTER_SANITIZE_STRING);
		$dataIn = config::change_idForQuery_IN($kkm_id);
		if($dataIn == null) {
			return json_encode(["dataNull"=>"yes"]);
		} else {
			$kkm_id = $dataIn['id'];
			$questionmarks = $dataIn['questionmarks'];
		}

		try {
			$delete = $this->db->prepare("DELETE from kkm where kkm_id in($questionmarks)");
			$delete->execute($kkm_id);
		}catch(PDOException$e){}
		if($delete->rowCount() > 0){
			return json_encode(["success"=>"yes"]);
		} else {
			return false;
		}
	}

	public function pesan_edit_kkm() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_kkm']) && $_SESSION['RAPORT']['pesan_edit_kkm'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_kkm']);
	    	return '<p class="pesan good">Kkm berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_kkm'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_kkm']);
	    	return '<p class="pesan warning">Kkm gagal diedit!</p>';
	    }
	}
}