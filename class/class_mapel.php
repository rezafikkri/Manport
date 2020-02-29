<?php  

/**
* 
*/
class mapel extends config {

	private function cek_has_mapel($nama_mapel, $kelas_id, $mapel_id=null) {
		$where = "where nama_mapel=:nama_mapel and kelas_id=:kelas_id";
		$execute = [ ':nama_mapel'=>$nama_mapel, ':kelas_id'=>$kelas_id ];
		if($mapel_id != null) {
			$where .= " and mata_pelajaran_id!=:mata_pelajaran_id";
			$execute = array_merge($execute, [':mata_pelajaran_id'=>$mapel_id]);
		}

	    $cek = $this->db->prepare("SELECT mata_pelajaran_id from mata_pelajaran $where");
	    $cek->execute($execute);
	    if($cek->rowCount() > 0) {
	    	return true;
	    }
	    return false;
	}

	public function cek_has_mapel_for_cek_nilai_belum_dimasukkan($kelas_id){
		$cek = $this->db->prepare("SELECT mp.mata_pelajaran_id 
			from mata_pelajaran as mp
			JOIN tahun_ajaran as ta1 ON mp.awal_tahun_ajaran = ta1.tahun_ajaran_id
            JOIN tahun_ajaran as ta2 ON mp.akhir_tahun_ajaran = ta2.tahun_ajaran_id
			where kelas_id=:kelas_id and ta1.tahun <= '".$_SESSION['RAPORT']['tahun_ajaran']."' and ta2.tahun >= '".$_SESSION['RAPORT']['tahun_ajaran']."' limit 1");
		$cek->execute([':kelas_id'=>$kelas_id]);
		return $cek->rowCount();
	}
	
	public function add_mapel() {
		$token = $this->cek_CSRF_token('add_mapel');
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'kelas_id[Kelas]' => 'required',
			'nama_mapel[Nama mapel]' => 'required|maxLength[50]',
			'kelompok_mapel[kelompok mapel]' => 'required|maxLength[32]',
			'kkm_id[Kkm]' => 'required',
			'awal_tahun_ajaran_id[Awal tahun ajaran]' => 'required',
			'akhir_tahun_ajaran_id[Akhir tahun ajaran]' => 'required'
		], false);

		$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
		$nama_mapel = filter_input(INPUT_POST, 'nama_mapel', FILTER_SANITIZE_STRING);
		if(!isset($_SESSION['RAPORT']['form_errors']['nama_mapel']) && $this->cek_has_mapel($nama_mapel, $kelas_id)) {
			$_SESSION['RAPORT']['form_errors']['nama_mapel'] = "Nama mapel sudah ada mohon cari nama mapel yang lain!";
		}
		// cek form errors
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$awal_tahun_ajaran_id = filter_input(INPUT_POST, 'awal_tahun_ajaran_id', FILTER_SANITIZE_STRING);
		$akhir_tahun_ajaran_id = filter_input(INPUT_POST, 'akhir_tahun_ajaran_id', FILTER_SANITIZE_STRING);
		$kelompok_mapel = filter_input(INPUT_POST, 'kelompok_mapel', FILTER_SANITIZE_STRING);
		$kkm_id = filter_input(INPUT_POST, 'kkm_id', FILTER_SANITIZE_STRING);
		$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_SANITIZE_STRING);
		$mapel_id = $this->generate_uuid();

		try {
			$add = $this->db->prepare("INSERT into mata_pelajaran set mata_pelajaran_id=:mapel_id, kelas_id=:kelas_id, nama_mapel=:nama_mapel, kelompok_mapel=:kelompok_mapel, kkm_id=:kkm_id, awal_tahun_ajaran=:awal_tahun_ajaran_id, akhir_tahun_ajaran=:akhir_tahun_ajaran_id ");
			$add->execute([ ':mapel_id' => $mapel_id, ':kelas_id' => $kelas_id, ':nama_mapel' => $nama_mapel, ':kelompok_mapel' => $kelompok_mapel, ':kkm_id' => $kkm_id, ':awal_tahun_ajaran_id' => $awal_tahun_ajaran_id, ':akhir_tahun_ajaran_id'=>$akhir_tahun_ajaran_id ]);
		} catch (PDOException$e){}
		if($add->rowCount() > 0){
			return json_encode(["success"=>'yes']);
		}
		return false;
	}

	public function tampil_mapel($kelas_id, $select=null){
		// select default
        if($select == null) {
        	$select = "mp.mata_pelajaran_id, mp.nama_mapel, mp.kelompok_mapel, km.kkm, concat(ta1.tahun,' sampai ',ta2.tahun) as berlaku";
			$join = "JOIN kkm as km USING(kkm_id)
            JOIN tahun_ajaran as ta1 ON mp.awal_tahun_ajaran = ta1.tahun_ajaran_id
            JOIN tahun_ajaran as ta2 ON mp.akhir_tahun_ajaran = ta2.tahun_ajaran_id";
            $where = null;
        } else {
        	$join = "JOIN tahun_ajaran as ta1 ON mp.awal_tahun_ajaran = ta1.tahun_ajaran_id
            JOIN tahun_ajaran as ta2 ON mp.akhir_tahun_ajaran = ta2.tahun_ajaran_id";
        	$where = "and ta1.tahun <= '".$_SESSION['RAPORT']['tahun_ajaran']."' and ta2.tahun >= '".$_SESSION['RAPORT']['tahun_ajaran']."'";
        }

		$tampil = $this->db->prepare("SELECT $select from mata_pelajaran as mp $join where kelas_id=:kelas_id $where order by mp.kelompok_mapel asc");
		$tampil->execute([ ':kelas_id' => $kelas_id ]);
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}

	public function delete_mapel(){
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$mapel_id = filter_input(INPUT_POST, 'mapel_id', FILTER_SANITIZE_STRING);
		try {
			$delete = $this->db->prepare("DELETE from mata_pelajaran where mata_pelajaran_id=:mapel_id");
			$delete->execute([':mapel_id'=>$mapel_id]);
		} catch (PDOException$e) {}
		if($delete->rowCount() > 0) {
			return json_encode(['success'=>'yes']);
		}
		return false;
	}

	public function get_one_mapel($mapel_id) {
		$get = $this->db->prepare("SELECT mp.*, k.jurusan_id
			from mata_pelajaran as mp 
			JOIN kelas as k USING(kelas_id)
			where mata_pelajaran_id=:mapel_id");
		$get->execute([ ':mapel_id' => $mapel_id ]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function edit_mapel() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'kelas[Kelas]' => 'required',
			'nama_mapel[Nama mapel]' => 'required|maxLength[50]',
			'kelompok_mapel[kelompok mapel]' => 'required|maxLength[32]',
			'kkm[Kkm]' => 'required',
			'awal_tahun_ajaran_id[Awal tahun ajaran]' => 'required',
			'akhir_tahun_ajaran_id[Akhir tahun ajaran]' => 'required'
		], false);

		$kelas_id = filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING);
		$nama_mapel = filter_input(INPUT_POST, 'nama_mapel', FILTER_SANITIZE_STRING);
		$mapel_id = filter_input(INPUT_POST, 'mapel_id', FILTER_SANITIZE_STRING);
		if(!isset($_SESSION['RAPORT']['form_errors']['nama_mapel']) && $this->cek_has_mapel($nama_mapel, $kelas_id, $mapel_id)) {
			$_SESSION['RAPORT']['form_errors']['nama_mapel'] = "Nama mapel sudah ada mohon cari nama mapel yang lain!";
		}
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form errors
		if($this->has_formErrors()) {
			return false;
		}

		$awal_tahun_ajaran_id = filter_input(INPUT_POST, 'awal_tahun_ajaran_id', FILTER_SANITIZE_STRING);
		$akhir_tahun_ajaran_id = filter_input(INPUT_POST, 'akhir_tahun_ajaran_id', FILTER_SANITIZE_STRING);
		$kelompok_mapel = filter_input(INPUT_POST, 'kelompok_mapel', FILTER_SANITIZE_STRING);
		$kkm_id = filter_input(INPUT_POST, 'kkm', FILTER_SANITIZE_STRING);
		
		try {
			$edit = $this->db->prepare("UPDATE mata_pelajaran set kelas_id=:kelas_id, nama_mapel=:nama_mapel, kelompok_mapel=:kelompok_mapel, kkm_id=:kkm_id, awal_tahun_ajaran=:awal_tahun_ajaran_id, akhir_tahun_ajaran=:akhir_tahun_ajaran_id where mata_pelajaran_id=:mapel_id");
			$edit->execute([ ':kelas_id' => $kelas_id, ':nama_mapel' => $nama_mapel, ':kelompok_mapel' => $kelompok_mapel, ':kkm_id' => $kkm_id, ':awal_tahun_ajaran_id' => $awal_tahun_ajaran_id, ':akhir_tahun_ajaran_id'=>$akhir_tahun_ajaran_id, ':mapel_id' => $mapel_id ]);
		} catch (PDOException $e) {
			return false;
		}
		return "success";
		
	}

	public function pesan_edit_mapel() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_mapel']) && $_SESSION['RAPORT']['pesan_edit_mapel'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_mapel']);
	    	return '<p class="pesan good">Mata pelajaran berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_mapel'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_mapel']);
	    	return '<p class="pesan warning">Mata pelajaran gagal diedit!</p>';
	    }
	}
}