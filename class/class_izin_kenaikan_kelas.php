<?php

/**
* 
*/
class izin_kenaikan_kelas extends config {

	public function tampil_izin_kenaikan_kelas($select=null) {
		if($select == null) {
			$select = "*";
		}
	    $get = $this->db->prepare("SELECT $select from izin_kenaikan_kelas order by kelas desc");
	    $get->execute();
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    }
	    return @$hasil;
	}

	public function generate_data_izin_kenaikan_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}
		
		function filter_kelasDB($r){
			return $r['kelas'];
		}
		$kelas = $this->db->prepare("SELECT kelas from kelas order by kelas desc");
		$kelas->execute();
		$kelasSebelum = '';
		$kelasDB = $this->tampil_izin_kenaikan_kelas("kelas");
		while ($r = $kelas->fetch(PDO::FETCH_ASSOC)) {
			$arrKelas = explode(".", $r['kelas']);
			if($arrKelas[0] != $kelasSebelum) {
				// jika kelasDB sudah ada, maka cek apakah kelas yang baru sudah ada datanya di DB
				if($kelasDB) {
					$kelas_lama = array_map('filter_kelasDB', $kelasDB);
					if(!in_array($arrKelas[0], $kelas_lama)) {
						$izin_kenaikan_kelas_id = $this->generate_uuid();
						$add = $this->db->prepare("INSERT into izin_kenaikan_kelas set izin_kenaikan_kelas_id=:izin_kenaikan_kelas_id, kelas=:kelas, status=:status");
						$add->execute([ ':izin_kenaikan_kelas_id'=>$izin_kenaikan_kelas_id, ':kelas'=>$arrKelas[0], ':status'=>'off' ]);

						$dataKelas[] = ['kelas'=>$arrKelas[0], 'izin_kenaikan_kelas_id'=>$izin_kenaikan_kelas_id];
						$kelasSebelum = $arrKelas[0];
					}
				} else {
					$izin_kenaikan_kelas_id = $this->generate_uuid();
					$add = $this->db->prepare("INSERT into izin_kenaikan_kelas set izin_kenaikan_kelas_id=:izin_kenaikan_kelas_id, kelas=:kelas, status=:status");
					$add->execute([ ':izin_kenaikan_kelas_id'=>$izin_kenaikan_kelas_id, ':kelas'=>$arrKelas[0], ':status'=>'off' ]);

					$dataKelas[] = ['kelas'=>$arrKelas[0], 'izin_kenaikan_kelas_id'=>$izin_kenaikan_kelas_id];
					$kelasSebelum = $arrKelas[0];
				}
			}
		}
		return json_encode(['success'=>@$dataKelas]);
	}

	public function change_status_izin_kenaikan_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'status' => 'required|must[on,off]'
		], false);
		// cek form errors
		$errors = $this->get_form_errors();
		if($errors) {
			return false;
		}

	    $izin_kenaikan_kelas_id = filter_input(INPUT_POST, 'izin_kenaikan_kelas_id', FILTER_SANITIZE_STRING);
	    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);;
	    $up = $this->db->prepare("UPDATE izin_kenaikan_kelas set status=:status where izin_kenaikan_kelas_id=:izin_kenaikan_kelas_id");
	    $up->execute([ ':status'=>$status, ':izin_kenaikan_kelas_id'=>$izin_kenaikan_kelas_id ]);
	    if($up->rowCount() > 0) {
	    	return json_encode(['success'=>'yes']);
	    } else {
	    	return false;
	    }
	}

	public function get_one_izin_kenaikan_kelas($kelas) {
	    $get = $this->db->prepare("SELECT status from izin_kenaikan_kelas where kelas=:kelas");
	    $get->execute([ ':kelas'=>$kelas ]);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function delete_izin_kenaikan_kelas($izin_kenaikan_kelas_id) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}
		
	    $del = $this->db->prepare("DELETE from izin_kenaikan_kelas where izin_kenaikan_kelas_id=:izin_kenaikan_kelas_id");
	    $del->execute([':izin_kenaikan_kelas_id'=>$izin_kenaikan_kelas_id]);
	    if($del->rowCount() > 0) {
	    	return json_encode(['success'=>'yes']);
	    }
	    return false;
	}
}