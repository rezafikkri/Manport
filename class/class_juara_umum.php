<?php

/** 
* 
*/
class juara_umum extends config {

	public function cek_has_juara_umum($where) {
	    $cek = $this->db->prepare("SELECT ju.juara_umum_id from juara_umum as ju 
	    	JOIN siswa_detail as sd ON sd.siswa_detail_id=ju.siswa_detail_id
	    	JOIN kelas as k ON k.kelas_id=sd.kelas_id
	    	where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id and sd.status='masih_sekolah' $where");
	    $cek->execute([ ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
	    return $cek->rowCount();
	}

	public function get_tipe_juara_umum() {
	    $cek = $this->db->prepare("SELECT tipe_juara from juara_umum where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
	    $cek->execute([ ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
	    return $cek->fetch(PDO::FETCH_ASSOC)['tipe_juara']??null;
	}

	public function reset_juara_umum() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$reset = $this->db->prepare("DELETE from juara_umum where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
		$reset->execute([ ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
		return true;
	}

	public function generate_where($kelas) {
		if($kelas == 'X') {
			$where = "and k.kelas >= 'X' and k.kelas < 'XI'";
		} elseif($kelas == 'XI') {
			$where = "and k.kelas >= 'XI' and k.kelas < 'XII'";
		} elseif($kelas == 'XII') {
			$where = "and k.kelas >= 'XII' and k.kelas < 'XIII'";
		} elseif($kelas == 'XIII') {
			$where = "and k.kelas >= 'XIII and k.kelas < 'XIV'";
		} else {
			$where = "and k.kelas = 'kelas invalid'";
		}
		return $where;
	}

	public function tampil_juara_umum($select=null, $where=null) {
		if($select == null) {
			$select = "ju.jml_nilai, ju.tipe_juara, ju.rata_rata_nilai, ju.juara, sd.nama_siswa, sd.nisn, k.kelas, j.nama_jurusan";
		}
	    $get = $this->db->prepare("SELECT $select from juara_umum as ju
	    	JOIN siswa_detail as sd ON sd.siswa_detail_id=ju.siswa_detail_id
	    	JOIN kelas as k ON sd.kelas_id=k.kelas_id
	    	JOIN jurusan as j ON k.jurusan_id=j.jurusan_id
	    	where ju.tahun_ajaran_id=:tahun_ajaran_id and ju.semester_id=:semester_id and sd.status='masih_sekolah' $where
	    	order by ju.juara asc");
	    $get->execute([ ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
	    $i=0;
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[$i]=$r;
	    	if(isset($r['rata_rata_nilai'])) $hasil[$i]['rata_rata_nilai'] = number_format($r['rata_rata_nilai'],2,',','.');
	    	if(isset($r['jml_nilai'])) $hasil[$i]['jml_nilai'] = number_format($r['jml_nilai'],2,',','');
	    	$i++;	    
	    }
	    return @$hasil;
	}
	
	public function tentukan_juara_umum($reload=false, $where=null, $tipeJuara=null){
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		if($reload == true) {
			$this->form_validation([
			'tipeJuara[Tipe juara]'=>'required|must[all,perjenjang]'
			], false);
			// form errors
			$errors = $this->get_form_errors();
			if($errors) {
				return json_encode(['errors'=>$errors]);
			}

			$tipeJuara = filter_input(INPUT_POST, 'tipeJuara', FILTER_SANITIZE_STRING);
			if($tipeJuara == "perjenjang") {
				$kelas = filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_STRING);
				if(!preg_match("/^([IVX]+)(.[0-9a-z]+)?\z/i", $kelas)) {
					return false;
				}
				$where = $this->generate_where($kelas);
			} else {
				$where = null;
			}
		}

		$juara_umum = $this->tampil_juara_umum('ju.juara_umum_id', $where);
		$tentukan_juara_umum = $this->db->prepare("SELECT (AVG(n.nilai_k)+AVG(n.nilai_p))/2 as rata_rata_nilai, SUM(n.nilai_k)+SUM(n.nilai_p) as jml_nilai, sd.siswa_detail_id, sd.nama_siswa, sd.nisn, k.kelas, j.nama_jurusan 
				FROM nilai as n
				JOIN siswa_detail as sd ON sd.siswa_detail_id=n.siswa_detail_id
				JOIN kelas as k ON k.kelas_id=sd.kelas_id
				JOIN jurusan as j ON j.jurusan_id=k.jurusan_id
				where n.tahun_ajaran_id=:tahun_ajaran_id and n.semester_id=:semester_id and sd.status='masih_sekolah' $where
				group by n.siswa_detail_id order by jml_nilai desc limit 10");
		$tentukan_juara_umum->execute([ ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);

		$juara = 0;
		$jmlNilaiSebelum = 0;
		$i = 0;
		while ($r=$tentukan_juara_umum->fetch(PDO::FETCH_ASSOC)) {
			if($jmlNilaiSebelum != $r['jml_nilai']) {
				$jmlNilaiSebelum = $r['jml_nilai'];
				$juara++;
			}
			if($juara_umum == true && isset($juara_umum[$i]['juara_umum_id'])) {
				$juara_umum_id = $juara_umum[$i]['juara_umum_id'];
				$up = $this->db->prepare("UPDATE juara_umum set 
					siswa_detail_id=:siswa_detail_id, 
					tahun_ajaran_id=:tahun_ajaran_id, 
					semester_id=:semester_id, 
					jml_nilai=:jml_nilai, 
					rata_rata_nilai=:rata_rata_nilai, 
					juara=:juara, 
					tipe_juara=:tipeJuara where juara_umum_id=:juara_umum_id");
				$up->execute([ ':siswa_detail_id'=>$r['siswa_detail_id'], 
					':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], 
					':semester_id'=>$_SESSION['RAPORT']['semester_id'], 
					':jml_nilai'=>$r['jml_nilai'], 
					':rata_rata_nilai'=>$r['rata_rata_nilai'], 
					':juara'=>$juara, 
					':tipeJuara'=>$tipeJuara, 
					':juara_umum_id'=>$juara_umum_id ]); 
			} else {
				$juara_umum_id = $this->generate_uuid();
				$insert = $this->db->prepare("INSERT into juara_umum set juara_umum_id=:juara_umum_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, jml_nilai=:jml_nilai, rata_rata_nilai=:rata_rata_nilai, juara=:juara, tipe_juara=:tipe_juara");
				$insert->execute([ ':juara_umum_id'=>$juara_umum_id, ':siswa_detail_id'=>$r['siswa_detail_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':jml_nilai'=>$r['jml_nilai'], ':rata_rata_nilai'=>$r['rata_rata_nilai'], ':juara'=>$juara, ':tipe_juara'=>$tipeJuara ]);
			}
			$hasil[]=$r;
			$hasil[$i] = array_merge($hasil[$i], ['juara'=>$juara]);
			$hasil[$i]['rata_rata_nilai'] = number_format($r['rata_rata_nilai'],2,',','.');
			$hasil[$i]['jml_nilai'] = number_format($r['jml_nilai'],2,',','');
			$i++;
		}

		if(isset($hasil)) {
			return json_encode(['success'=>$hasil]);
		}
		return false;
	}

	public function delete_juara_umum($db, $dbS, $siswa_detail_id, $action=null) {
	    $token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}
		
		$whiteListAction = ['deleteSiswaKeluar','deleteSiswaLulus'];
		if($_SESSION['RAPORT']['level']=='admin' && in_array($action, $whiteListAction)) {
			$where = null;
		} else {
			if(!$db->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
			$where = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
		}

		$del = $this->db->prepare("DELETE from juara_umum where siswa_detail_id=:siswa_detail_id $where");
		$del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
		if($del->rowCount() > 0){
			return true;
		}
		return false;
	}
}