<?php

/**
* 
*/
class semester extends config {

	public function tampil_semester() {
		$get = $this->db->prepare("SELECT * FROM semester order by semester");
		$get->execute();
		while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}

	public function make_session_semester() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}
		
		if(isset($_SESSION['RAPORT']['semester_id'])) {
			$semester_id_sebelum = $_SESSION['RAPORT']['semester_id'];
		} else {
			$semester_id_sebelum = null;
		}
		
	    $semester_id = filter_input(INPUT_POST, "semester_id", FILTER_SANITIZE_STRING);
	    if($semester_id_sebelum != $semester_id) {
		    $upStatusToYes = $this->db->prepare("UPDATE semester set status='yes' where semester_id=:semester_id");
		    $upStatusToYes->execute([':semester_id'=>$semester_id]);
		    if($upStatusToYes->rowCount() > 0) {
		    	if(isset($_SESSION['RAPORT']['semester_id'])) {
		    		$upStatusToNo = $this->db->prepare("UPDATE semester set status='no' where semester_id=:semester_id");
		    		$upStatusToNo->execute([':semester_id'=>$_SESSION['RAPORT']['semester_id']]);
		    	}
		    	$semester = $this->get_one_semester($semester_id, '*');
		    	$_SESSION['RAPORT']['semester_id'] = $semester['semester_id'];
		    	$_SESSION['RAPORT']['semester'] = $semester['semester'];
		    	// generate pesan
			    if(!isset($_SESSION['RAPORT']['tahun_ajaran_id'])) {
			    	$pesan = "no_yet_session_tahun_ajaran";
			    } else {
			    	$pesan = "yes";
			    }
		    	return json_encode(['success'=>[
		    		'pesan'=>$pesan
		    	]]);
		    }
		    return false;
		} else {
			return false;
		}
	}

	private function get_one_semester($semester_id, $select){
		$get = $this->db->prepare("SELECT $select from semester where semester_id=:semester_id");
		$get->execute([':semester_id'=>$semester_id]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	// for login
	public function get_semester_status_yes() {
	   $get = $this->db->prepare("SELECT * from semester where status='yes'");
	   $get->execute();
	   return $get->fetch(PDO::FETCH_ASSOC);
	}
}