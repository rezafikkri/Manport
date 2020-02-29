<?php

/**
* 
*/
class tahun_ajaran extends config {
	
	public function add_tahun_ajaran() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'tahun_ajaran[Tahun ajaran]' => 'required|maxLength[9]|unique[tahun_ajaran.tahun]|regex[ /^[0-9]+-[0-9]+\z/ ]',
		], false);
		// cek form error
		$error = $this->get_form_errors();
		if($error) {
			return json_encode(["errors"=>$error]);
		}

		$tahun_ajaran = filter_input(INPUT_POST, 'tahun_ajaran', FILTER_SANITIZE_STRING);
		$tahun_ajaran_id = config::generate_uuid();
		$insert = $this->db->prepare("INSERT INTO tahun_ajaran set tahun_ajaran_id=:tahun_ajaran_id, tahun=:tahun");
		$insert->execute([ ':tahun_ajaran_id' => $tahun_ajaran_id, ':tahun' => $tahun_ajaran ]);
		return json_encode(['success'=>'yes']);
	}

	public function tampil_tahun_ajaran($limit=null,$offset=null,$select=null) {
		if($limit != null && $offset != null) {
			$limit = "LIMIT $offset,$limit";
		} elseif($limit != null && $offset == null) {
			$limit = "LIMIT $limit";
		} else {
			$limit = "";
		}
		// select
		if($select == null) {
			$select = "*";
		}
		
		$get = $this->db->prepare("SELECT $select FROM tahun_ajaran order by tahun desc $limit");
		$get->execute();
		while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}

	public function delete_tahun_ajaran() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}
		
		$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_SANITIZE_STRING);
		$dataIn = config::change_idForQuery_IN($tahun_ajaran_id);
		if($dataIn == null) {
			return json_encode(["dataNull"=>"yes"]);
		} else {
			$tahun_ajaran_id = $dataIn['id'];
			$questionmarks = $dataIn['questionmarks'];
		}
		if(isset($_SESSION['RAPORT']['tahun_ajaran_id'])) {
			array_push($tahun_ajaran_id, $_SESSION['RAPORT']['tahun_ajaran_id']);
			$where = "and tahun_ajaran_id!=?";
		} else {
			$where = null;
		}
		try {
			$del = $this->db->prepare("DELETE FROM tahun_ajaran where tahun_ajaran_id in($questionmarks) $where");
			$del->execute($tahun_ajaran_id);
		} catch (PDOException $e) {}
		if($del->rowCount() > 0) {
			return json_encode(["success"=>"yes"]);
		} else {
			return false;
		}
	}

	public function make_session_tahun_ajaran() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		// untuk perubahan tahun ajaran dari-ke
	    if(isset($_SESSION['RAPORT']['tahun_ajaran'])){
	    	$tahun_sebelum = $_SESSION['RAPORT']['tahun_ajaran'];
	    	$tahun_id_sebelum = $_SESSION['RAPORT']['tahun_ajaran_id'];
	    } else {
	    	$tahun_sebelum = null;
	    	$tahun_id_sebelum = null;
	    }

	    $tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_SANITIZE_STRING);
	    if($tahun_ajaran_id != $tahun_id_sebelum) {

	    	$upStatustoRun = $this->db->prepare("UPDATE tahun_ajaran set status=:status where tahun_ajaran_id=:tahun_ajaran_id");
	    	$upStatustoRun->execute(['status'=>'run','tahun_ajaran_id'=>$tahun_ajaran_id]);
		    if($upStatustoRun->rowCount() > 0) {
		    	if(isset($_SESSION['RAPORT']['tahun_ajaran'])) {
			    	$upStatustoYes = $this->db->prepare("UPDATE tahun_ajaran set status=:status where tahun_ajaran_id=:tahun_ajaran_id");
			    	$upStatustoYes->execute([ ':status'=>'yes', ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'] ]);
			    }

			    $tahun_ajaran = $this->get_one_tahun_ajaran($tahun_ajaran_id, '*');
			    $_SESSION['RAPORT']['tahun_ajaran_id'] = $tahun_ajaran['tahun_ajaran_id']??'';
			    $_SESSION['RAPORT']['tahun_ajaran'] = $tahun_ajaran['tahun']??'';
			    // generate pesan
			    if(!isset($_SESSION['RAPORT']['semester_id'])) {
			    	$pesan = "no_yet_session_semester";
			    } else {
			    	$pesan = "yes";
			    }
				return json_encode([ 'success'=>[
					'tahun_sebelum' => $tahun_sebelum??'dataNull',
					'tahun_sekarang' => $tahun_ajaran['tahun']??'',
					'pesan'=>$pesan
				] ]);
		    }
		    return false;

	    } else {
	    	return false;
	    }		
	}

	private function get_one_tahun_ajaran($tahun_ajaran_id, $select){
		$get = $this->db->prepare("SELECT $select from tahun_ajaran where tahun_ajaran_id=:tahun_ajaran_id");
		$get->execute(['tahun_ajaran_id'=>$tahun_ajaran_id]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	// for login
	public function get_tahun_ajaran_status_run() {
	   $get = $this->db->prepare("SELECT * from tahun_ajaran where status='run'");
	   $get->execute();
	   return $get->fetch(PDO::FETCH_ASSOC);
	}
}