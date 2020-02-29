<?php  

/**
* 
*/
class siswa extends config {
	
	public function add_siswa_detail() {
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
			'nama_siswa[Nama siswa]' => 'required|maxLength[30]|unique[siswa_detail.nama_siswa]',
			'nisn[NISN]' => 'required|minLength[10]|maxLength[10]|integer|unique[siswa_detail.nisn]',
			'no_induk[NIS]' => 'required|maxLength[6]|unique[siswa_detail.no_induk]',
			'tempat_lahir[Tempat lahir]' => 'required|maxLength[30]',
			'tanggal_lahir[Tanggal lahir]' => 'required|maxLength[2]',
			'bulan_lahir[Bulan lahir]' => 'required|maxLength[2]',
			'tahun_lahir[Tahun lahir]' => 'required|maxLength[4]',
			'jenis_kelamin[Jenis kelamin]' => 'required|maxLength[10]|must[Laki-laki,Perempuan]',
			'agama[Agama]' => 'required|maxLength[20]',
			'status_dalam_keluarga[Status dalam keluarga]' => 'required|maxLength[30]',
			'anak_ke[Anak ke]' => 'required|maxLength[2]|integer',
			'alamat_peserta_didik[Alamat peserta didik]' => 'required',
			'nomor_telpon_rumah[Nomor telpon rumah]' => 'maxLength[30]|integer',
			'sekolah_asal[Sekolah asal]' => 'required|maxLength[30]',
			'dikelas[Dikelas]' => 'required|maxLength[5]',
			'pada_tanggal[Pada tanggal]' => 'required|maxLength[23]',
			'semester[Semester]' => 'required|maxLength[1]|regex[ /^[0-8]\z/i ]',
			'nama_ayah[Nama ayah]' => 'required|maxLength[30]',
			'nama_ibu[Nama ibu]' => 'required|maxLength[30]',
			'alamat_orang_tua[Alamat orang tua]' => 'required',
			'pekerjaan_ayah[Pekerjaan ayah]' => 'required|maxLength[20]',
			'pekerjaan_ibu[Pekerjaan ibu]' => 'required|maxLength[20]',
			'nama_wali[Nama wali]' => 'maxLength[30]',
			'pekerjaan_wali[Pekerjaan wali]' => 'maxLength[20]'
		], false);
		// cek form error
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$siswa_detail_id = config::generate_uuid();
		$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_SANITIZE_STRING);
		$nama_siswa = filter_input(INPUT_POST, 'nama_siswa', FILTER_SANITIZE_STRING);
		$nisn = filter_input(INPUT_POST, 'nisn', FILTER_SANITIZE_STRING);
		$no_induk = filter_input(INPUT_POST, 'no_induk', FILTER_SANITIZE_STRING);
		$tempat_lahir = filter_input(INPUT_POST, 'tempat_lahir', FILTER_SANITIZE_STRING);
		$tanggal_lahir = filter_input(INPUT_POST, 'tanggal_lahir', FILTER_SANITIZE_STRING);
		$bulan_lahir = filter_input(INPUT_POST, 'bulan_lahir', FILTER_SANITIZE_STRING);
		$tahun_lahir = filter_input(INPUT_POST, 'tahun_lahir', FILTER_SANITIZE_STRING);
		$tempat_tanggal_lahir = $tempat_lahir.'|'.$tanggal_lahir.'|'.$bulan_lahir.'|'.$tahun_lahir;
		$jenis_kelamin = filter_input(INPUT_POST, 'jenis_kelamin', FILTER_SANITIZE_STRING);
		$agama = filter_input(INPUT_POST, 'agama', FILTER_SANITIZE_STRING);
		$status_dalam_keluarga = filter_input(INPUT_POST, 'status_dalam_keluarga', FILTER_SANITIZE_STRING);
		$anak_ke = filter_input(INPUT_POST, 'anak_ke', FILTER_SANITIZE_STRING);
		$alamat_peserta_didik = filter_input(INPUT_POST, 'alamat_peserta_didik', FILTER_SANITIZE_STRING);
		$nomor_telpon_rumah = filter_input(INPUT_POST, 'nomor_telpon_rumah', FILTER_SANITIZE_STRING);
		$sekolah_asal = filter_input(INPUT_POST, 'sekolah_asal', FILTER_SANITIZE_STRING);
		$dikelas = filter_input(INPUT_POST, 'dikelas', FILTER_SANITIZE_STRING);
		$pada_tanggal = filter_input(INPUT_POST, 'pada_tanggal', FILTER_SANITIZE_STRING);
		$semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_STRING);
		$diterima_disekolah_ini = $dikelas.'|'.$pada_tanggal.'|'.$semester;
		$nama_ayah = filter_input(INPUT_POST, 'nama_ayah', FILTER_SANITIZE_STRING);
		$nama_ibu = filter_input(INPUT_POST, 'nama_ibu', FILTER_SANITIZE_STRING);
		$alamat_orang_tua = filter_input(INPUT_POST, 'alamat_orang_tua', FILTER_SANITIZE_STRING);
		$pekerjaan_ayah = filter_input(INPUT_POST, 'pekerjaan_ayah', FILTER_SANITIZE_STRING);
		$pekerjaan_ibu = filter_input(INPUT_POST, 'pekerjaan_ibu', FILTER_SANITIZE_STRING);
		$nama_wali = filter_input(INPUT_POST, 'nama_wali', FILTER_SANITIZE_STRING);
		$alamat_wali = filter_input(INPUT_POST, 'alamat_wali', FILTER_SANITIZE_STRING);
		$pekerjaan_wali = filter_input(INPUT_POST, 'pekerjaan_wali', FILTER_SANITIZE_STRING);
		try {
			$insert = $this->db->prepare("INSERT into siswa_detail set 
				siswa_detail_id=:siswa_detail_id,
				kelas_id=:kelas_id,
				nama_siswa=:nama_siswa,
				nisn=:nisn,
				no_induk = :no_induk,
				tempat_tanggal_lahir=:tempat_tanggal_lahir,
				jenis_kelamin=:jenis_kelamin,
				agama=:agama,
				status_dalam_keluarga=:status_dalam_keluarga,
				anak_ke=:anak_ke,
				alamat_peserta_didik=:alamat_peserta_didik,
				nomor_telp_rumah=:nomor_telpon_rumah,
				sekolah_asal=:sekolah_asal,
				diterima_disekolah_ini=:diterima_disekolah_ini,
				nama_ayah=:nama_ayah,
				nama_ibu=:nama_ibu,
				alamat_orang_tua=:alamat_orang_tua,
				pekerjaan_ayah=:pekerjaan_ayah,
				pekerjaan_ibu=:pekerjaan_ibu,
				nama_wali=:nama_wali,
				alamat_wali=:alamat_wali,
				pekerjaan_wali=:pekerjaan_wali ");
			$exec = $insert->execute([
				':siswa_detail_id' => $siswa_detail_id,
				':kelas_id' => $kelas_id,
				':nama_siswa' => $nama_siswa,
				':nisn' => $nisn,
				':no_induk' => $no_induk,
				':tempat_tanggal_lahir' => $tempat_tanggal_lahir,
				':jenis_kelamin' => $jenis_kelamin,
				':agama' => $agama,
				':status_dalam_keluarga' => $status_dalam_keluarga,
				':anak_ke' => $anak_ke,
				':alamat_peserta_didik' => $alamat_peserta_didik,
				':nomor_telpon_rumah' => $nomor_telpon_rumah,
				':sekolah_asal' => $sekolah_asal,
				':diterima_disekolah_ini' => $diterima_disekolah_ini,
				':nama_ayah'=>$nama_ayah,
				':nama_ibu'=>$nama_ibu,
				':alamat_orang_tua' => $alamat_orang_tua,
				':pekerjaan_ayah'=>$pekerjaan_ayah,
				':pekerjaan_ibu'=>$pekerjaan_ibu,
				':nama_wali' => $nama_wali,
				':alamat_wali' => $alamat_wali,
				':pekerjaan_wali' => $pekerjaan_wali
			]);
		} catch(PDOException$e){
			return false;
		}
		return json_encode(["success"=>"yes"]);
	}

	public function edit_siswa_detail($siswa_detail_id){
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
			'status[Status]' => 'required|must[masih_sekolah,keluar]',
			'nama_siswa[Nama siswa]' => 'required|maxLength[30]|unique[siswa_detail.nama_siswa][siswa_detail_id.'.$siswa_detail_id.']',
			'nisn[NISN]' => 'required|minLength[10]|maxLength[10]|integer|unique[siswa_detail.nisn][siswa_detail_id.'.$siswa_detail_id.']',
			'no_induk[NIS]' => 'required|maxLength[6]|unique[siswa_detail.no_induk][siswa_detail_id.'.$siswa_detail_id.']',
			'tempat_lahir[Tempat lahir]' => 'required|maxLength[30]',
			'tanggal_lahir[Tanggal lahir]' => 'required|maxLength[2]',
			'bulan_lahir[Bulan lahir]' => 'required|maxLength[2]',
			'tahun_lahir[Tahun lahir]' => 'required|maxLength[4]',
			'jenis_kelamin[Jenis kelamin]' => 'required|maxLength[10]|must[Laki-laki,Perempuan]',
			'agama[Agama]' => 'required|maxLength[20]',
			'status_dalam_keluarga[Status dalam keluarga]' => 'required|maxLength[30]',
			'anak_ke[Anak ke]' => 'required|maxLength[2]|integer',
			'alamat_peserta_didik[Alamat peserta didik]' => 'required',
			'nomor_telpon_rumah[Nomor telpon rumah]' => 'maxLength[30]',
			'sekolah_asal[Sekolah asal]' => 'required|maxLength[30]',
			'dikelas[Dikelas]' => 'required|maxLength[5]',
			'pada_tanggal[Pada tanggal]' => 'required|maxLength[24]',
			'semester[Semester]' => 'required|maxLength[1]|regex[ /^[1-8]\z/i ]',
			'nama_ayah[Nama ayah]' => 'required|maxLength[30]',
			'nama_ibu[Nama ibu]' => 'required|maxLength[30]',
			'alamat_orang_tua[Alamat orang tua]' => 'required',
			'pekerjaan_ayah[Pekerjaan ayah]' => 'required|maxLength[20]',
			'pekerjaan_ibu[Pekerjaan ibu]' => 'required|maxLength[20]',
			'nama_wali[Nama wali]' => 'maxLength[30]',
			'pekerjaan_wali[Pekerjaan wali]' => 'maxLength[20]'], false);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form error
		if($this->has_formErrors()) {
			return false;
		}

		$kelas_id = filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING);
		$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
		$nama_siswa = filter_input(INPUT_POST, 'nama_siswa', FILTER_SANITIZE_STRING);
		$nisn = filter_input(INPUT_POST, 'nisn', FILTER_SANITIZE_STRING);
		$no_induk = filter_input(INPUT_POST, 'no_induk', FILTER_SANITIZE_STRING);
		$tempat_lahir = filter_input(INPUT_POST, 'tempat_lahir', FILTER_SANITIZE_STRING);
		$tanggal_lahir = filter_input(INPUT_POST, 'tanggal_lahir', FILTER_SANITIZE_STRING);
		$bulan_lahir = filter_input(INPUT_POST, 'bulan_lahir', FILTER_SANITIZE_STRING);
		$tahun_lahir = filter_input(INPUT_POST, 'tahun_lahir', FILTER_SANITIZE_STRING);
		$tempat_tanggal_lahir = $tempat_lahir.'|'.$tanggal_lahir.'|'.$bulan_lahir.'|'.$tahun_lahir;
		$jenis_kelamin = filter_input(INPUT_POST, 'jenis_kelamin', FILTER_SANITIZE_STRING);
		$agama = filter_input(INPUT_POST, 'agama', FILTER_SANITIZE_STRING);
		$status_dalam_keluarga = filter_input(INPUT_POST, 'status_dalam_keluarga', FILTER_SANITIZE_STRING);
		$anak_ke = filter_input(INPUT_POST, 'anak_ke', FILTER_SANITIZE_STRING);
		$alamat_peserta_didik = filter_input(INPUT_POST, 'alamat_peserta_didik', FILTER_SANITIZE_STRING);
		$nomor_telpon_rumah = filter_input(INPUT_POST, 'nomor_telpon_rumah', FILTER_SANITIZE_STRING);
		$sekolah_asal = filter_input(INPUT_POST, 'sekolah_asal', FILTER_SANITIZE_STRING);
		$dikelas = filter_input(INPUT_POST, 'dikelas', FILTER_SANITIZE_STRING);
		$pada_tanggal = filter_input(INPUT_POST, 'pada_tanggal', FILTER_SANITIZE_STRING);
		$semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_STRING);
		$diterima_disekolah_ini = $dikelas.'|'.$pada_tanggal.'|'.$semester;
		$nama_ayah = filter_input(INPUT_POST, 'nama_ayah', FILTER_SANITIZE_STRING);
		$nama_ibu = filter_input(INPUT_POST, 'nama_ibu', FILTER_SANITIZE_STRING);
		$alamat_orang_tua = filter_input(INPUT_POST, 'alamat_orang_tua', FILTER_SANITIZE_STRING);
		$pekerjaan_ayah = filter_input(INPUT_POST, 'pekerjaan_ayah', FILTER_SANITIZE_STRING);
		$pekerjaan_ibu = filter_input(INPUT_POST, 'pekerjaan_ibu', FILTER_SANITIZE_STRING);
		$nama_wali = filter_input(INPUT_POST, 'nama_wali', FILTER_SANITIZE_STRING);
		$alamat_wali = filter_input(INPUT_POST, 'alamat_wali', FILTER_SANITIZE_STRING);
		$pekerjaan_wali = filter_input(INPUT_POST, 'pekerjaan_wali', FILTER_SANITIZE_STRING);

		try {
			$edit = $this->db->prepare("UPDATE siswa_detail set
				kelas_id=:kelas_id,
				nama_siswa=:nama_siswa,
				nisn=:nisn,
				no_induk = :no_induk,
				tempat_tanggal_lahir=:tempat_tanggal_lahir,
				jenis_kelamin=:jenis_kelamin,
				agama=:agama,
				status_dalam_keluarga=:status_dalam_keluarga,
				anak_ke=:anak_ke,
				alamat_peserta_didik=:alamat_peserta_didik,
				nomor_telp_rumah=:nomor_telpon_rumah,
				sekolah_asal=:sekolah_asal,
				diterima_disekolah_ini=:diterima_disekolah_ini,
				nama_ayah=:nama_ayah,
				nama_ibu=:nama_ibu,
				alamat_orang_tua=:alamat_orang_tua,
				pekerjaan_ayah=:pekerjaan_ayah,
				pekerjaan_ibu=:pekerjaan_ibu,
				nama_wali=:nama_wali,
				alamat_wali=:alamat_wali,
				pekerjaan_wali=:pekerjaan_wali,
				status=:status where siswa_detail_id=:siswa_detail_id");
			$edit->execute([':kelas_id' => $kelas_id,
				':nama_siswa' => $nama_siswa,
				':nisn' => $nisn,
				':no_induk' => $no_induk,
				':tempat_tanggal_lahir' => $tempat_tanggal_lahir,
				':jenis_kelamin' => $jenis_kelamin,
				':agama' => $agama,
				':status_dalam_keluarga' => $status_dalam_keluarga,
				':anak_ke' => $anak_ke,
				':alamat_peserta_didik' => $alamat_peserta_didik,
				':nomor_telpon_rumah' => $nomor_telpon_rumah,
				':sekolah_asal' => $sekolah_asal,
				':diterima_disekolah_ini' => $diterima_disekolah_ini,
				':nama_ayah'=>$nama_ayah,
				':nama_ibu'=>$nama_ibu,
				':alamat_orang_tua' => $alamat_orang_tua,
				':pekerjaan_ayah'=>$pekerjaan_ayah,
				':pekerjaan_ibu'=>$pekerjaan_ibu,
				':nama_wali' => $nama_wali,
				':alamat_wali' => $alamat_wali,
				':pekerjaan_wali' => $pekerjaan_wali,
				':status'=>$status,
				':siswa_detail_id'=>$siswa_detail_id]);
		} catch (PDOException $e) {
			return false;
		}
		if($status == "keluar") {
			return $status;
		} else {
			return "success";
		}
	}

	public function count_jml_siswa($status) {
	    $count = $this->db->prepare("SELECT siswa_detail_id from siswa_detail where status=:status");
	    $count->execute([':status'=>$status]);
	    return $count->rowCount();
	}

	public function update_kelas_or_status_siswa($updateSet, $execute, $siswa_detail_id) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;// false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;// false
		}

	    try {
	    	$edit = $this->db->prepare("UPDATE siswa_detail set
				$updateSet where siswa_detail_id=:siswa_detail_id");
			$edit->execute(array_merge($execute, [':siswa_detail_id' => $siswa_detail_id]));
	    } catch (PDOException $e) {
	    	return false;
	    }
		return true;
	}

	public function update_no_un($siswa_detail_id) {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;// false
		}

		$this->form_validation([
			'no_un[No ujian nasional]'=>'required|maxLength[40]'
		], false);
		// form errors
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form errors
		if($this->has_formErrors()) {
			return false;
		}

		$no_un =  filter_input(INPUT_POST, 'no_un', FILTER_SANITIZE_STRING);
	    $up = $this->db->prepare("UPDATE siswa_detail set no_un=:no_un where siswa_detail_id=:siswa_detail_id");
	    $up->execute([':no_un'=>$no_un, ':siswa_detail_id'=>$siswa_detail_id]);
	    return true;
	}

	public function tampil_siswa_detail($kelas_id=null,$status,$select=null,$join=null) {
		// select default
		if($select == null) {
			$select = "s.siswa_detail_id, s.nama_siswa, s.nisn";
		}
		if($kelas_id != null) {
			$where = "s.kelas_id=:kelas_id and s.status=:status";
			if($status == 'lulus') {
				$where = 's.kelas_id=:kelas_id and (s.status=:status or s.status="tidak_lulus")';
			}
			$execute = [':kelas_id'=>$kelas_id, ':status'=>$status];
		} else {
			$where = "s.status=:status";
			if($status == 'lulus') {
				$where = '(s.status=:status or s.status="tidak_lulus")';
			}
			$execute = [':status'=>$status];
		}

		// khusus tampil siswa lulus
		if($status == 'lulus') {
			$tahun_ajaran_lulus = filter_input(INPUT_POST, 'tahun_ajaran', FILTER_SANITIZE_STRING);
			$where2 = 'and s.tahun_ajaran_kelulusan=:tahun_ajaran_kelulusan';
			$execute = array_merge($execute, [':tahun_ajaran_kelulusan'=>$tahun_ajaran_lulus]);
		} else {
			$where2 = null;
		}
		// khusus tampil siswa lulus end

		$tampil = $this->db->prepare("SELECT $select
			from siswa_detail as s
			$join
			where $where $where2 order by s.nama_siswa asc");
		$tampil->execute($execute);
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}
		return @$hasil;
	}

	public function get_one_siswa_detail($siswa_detail_id, $status, $select=null, $kelas_id=null, $join=null, $where=null, $execute=null) {
		// select default
		if($select == null) {
			$select = "sd.siswa_detail_id, sd.kelas_id, sd.nama_siswa, sd.nisn, sd.no_induk, sd.tempat_tanggal_lahir, sd.jenis_kelamin, sd.agama, sd.status_dalam_keluarga, sd.anak_ke, sd.alamat_peserta_didik, sd.nomor_telp_rumah, sd.sekolah_asal, sd.diterima_disekolah_ini, sd.nama_ayah, sd.nama_ibu, sd.pekerjaan_ayah, sd.pekerjaan_ibu, sd.alamat_orang_tua, sd.nama_wali, sd.alamat_wali, sd.pekerjaan_wali, sd.status, k.jurusan_id";
		}
		// set execute1 dan tambahan where jika kelas_id ada
		$execute1 = [ ':siswa_detail_id' => $siswa_detail_id, ':status'=>$status ];
		$where1 = "siswa_detail_id=:siswa_detail_id and status=:status";
		if($status == 'lulus') {
			$where1 = "siswa_detail_id=:siswa_detail_id and (status=:status or status='tidak_lulus')";
		}
		if($kelas_id != null) {
			$where1 .= " and sd.kelas_id=:kelas_id";
			$execute1 = array_merge($execute1, [':kelas_id'=>$kelas_id]);
		}

		$tampil = $this->db->prepare("SELECT $select
			from siswa_detail as sd
			$join
			where $where1 $where");
		$tampil->execute(array_merge($execute1, $execute??[]));
		return $tampil->fetch(PDO::FETCH_ASSOC);
	}

	public function get_one_siswa_detail_not_where_siswa_detail_id($select,$where,$execute) {
	    $get = $this->db->prepare("SELECT $select from siswa_detail where $where");
	    $get->execute($execute);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function tampil_siswa_detail_where_IN($siswa_detail_id,$status,$select=null,$join=null) {
		if($select == null) {
			$select = "nama_siswa, nisn, no_induk, tempat_tanggal_lahir, jenis_kelamin, agama, status_dalam_keluarga, anak_ke, alamat_peserta_didik, nomor_telp_rumah, sekolah_asal, diterima_disekolah_ini, nama_ayah, nama_ibu, alamat_orang_tua, pekerjaan_ayah, pekerjaan_ibu, nama_wali, alamat_wali, pekerjaan_wali";
		}
		if($siswa_detail_id != null) {
			$dataIn = config::change_idForQuery_IN($siswa_detail_id);
			$siswa_detail_id = $dataIn['id'];
			$questionmarks = $dataIn['questionmarks'];
			if($status == "lulus") {
				$statusQ = "(status='lulus' OR status='tidak_lulus')";
			} else {
				$statusQ = "status=?";
				array_push($siswa_detail_id, $status);
			}

		    $get = $this->db->prepare("SELECT $select from siswa_detail $join where siswa_detail_id in($questionmarks) and $statusQ order by nama_siswa asc");
		    $get->execute($siswa_detail_id);
		    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
		    	$hasil[] = $r;
		    }
		    return @$hasil;
		} else {
			return null;
		}
	}

	public function delete_siswa_detail($siswa_detail_id, $where){
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
		try {
			$deleteSiswa = $this->db->prepare("DELETE from siswa_detail where siswa_detail_id=:siswa_detail_id $where");
			$deleteSiswa->execute([ ':siswa_detail_id' => $siswa_detail_id ]);
		} catch(PDOException$e) {}
		if($deleteSiswa->rowCount() > 0){
			return json_encode(["success"=>"yes"]);
		}
		return false;
	}

	public function pesan_edit_siswa_detail() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_siswa_detail']) && $_SESSION['RAPORT']['pesan_edit_siswa_detail'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_siswa_detail']);
	    	return '<p class="pesan good">Siswa berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_siswa_detail']) && $_SESSION['RAPORT']['pesan_edit_siswa_detail'] == "keluar") {
	    	unset($_SESSION['RAPORT']['pesan_edit_siswa_detail']);
	    	return '<p class="pesan good">Siswa berhasil diedit, Status siswa telah diubah menjadi keluar!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_siswa_detail']) && $_SESSION['RAPORT']['pesan_edit_siswa_detail'] == "masih_sekolah") {
	    	unset($_SESSION['RAPORT']['pesan_edit_siswa_detail']);
	    	return '<p class="pesan good">Siswa berhasil diedit, Status siswa telah diubah menjadi masih sekolah!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_siswa_detail'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_siswa_detail']);
	    	return '<p class="pesan warning">Siswa gagal diedit!</p>';
	    }
	}

	public static function bulanIndo($bulan) {
		switch ($bulan) {
			case 1:
			$bulan = "Januari";
		break;
			case 2:
			$bulan = "Februari";
		break;
			case 3:
			$bulan = "Maret";
		break;
			case 4:
			$bulan = "April";
		break;
			case 5:
			$bulan = "Mei";
		break;
			case 6:
			$bulan = "Juni";
		break;
			case 7:
			$bulan = "Juli";
		break;
			case 8:
			$bulan = "Agustus";
		break;
			case 9:
			$bulan = "September";
		break;
			case 10:
			$bulan = "Oktober";
		break;
			case 11:
			$bulan = "November";
		break;
			case 12:
			$bulan = "Desember";
		break;
		}

		return $bulan;
	}
}