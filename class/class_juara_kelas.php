<?php  

/**
* 
*/
class juara_kelas extends config {

	public function cek_has_juara_kelas($siswa_detail_id=null) {
		$onJOIN = "and sd.kelas_id='".$_SESSION['RAPORT']['kelas_id']."'";
		if($siswa_detail_id != null) {
			$onJOIN .= " and sd.siswa_detail_id='".$siswa_detail_id."'";
		}
	    $cek = $this->db->prepare("SELECT jk.juara_kelas_id from juara_kelas as jk
					JOIN siswa_detail as sd ON sd.siswa_detail_id=jk.siswa_detail_id 
						 $onJOIN
					where jk.semester_id='".$_SESSION['RAPORT']['semester_id']."' and tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and sd.status='masih_sekolah' limit 1");
	    $cek->execute();
	    return $cek->rowCount();
	}

	public function tampil_juara_kelas($select=null) {
		if($select == null) {
			$select = "jk.rata_rata_nilai, jk.jml_nilai, jk.juara, sd.nama_siswa";
		}
	    $get = $this->db->prepare("SELECT $select
	    	from juara_kelas as jk
	    	JOIN siswa_detail as sd ON sd.siswa_detail_id=jk.siswa_detail_id
	    		and sd.kelas_id='".$_SESSION['RAPORT']['kelas_id']."' 
	    	where jk.semester_id='".$_SESSION['RAPORT']['semester_id']."' and jk.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and sd.status='masih_sekolah'
	    	order by juara asc");
	    $get->execute();
	    $i = 0;
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[$i]=$r;
	    	if(isset($r['rata_rata_nilai'])) $hasil[$i]['rata_rata_nilai'] = number_format($r['rata_rata_nilai'],2,',','.');
	    	if(isset($r['jml_nilai'])) $hasil[$i]['jml_nilai'] = number_format($r['jml_nilai'],2,',','');
	    	$i++;
	    }
	    return @$hasil;
	}

	public function tentukan_juara_kelas() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;// false
		}

		$juara_kelas = $this->tampil_juara_kelas('jk.juara_kelas_id');
	    $tentukan_juara_kelas = $this->db->prepare("SELECT (AVG(n.nilai_k)+AVG(n.nilai_p))/2 as rata_rata_nilai, SUM(n.nilai_k)+SUM(n.nilai_p) as jml_nilai, n.siswa_detail_id, sd.nama_siswa
			from nilai as n
			JOIN siswa_detail as sd ON sd.siswa_detail_id=n.siswa_detail_id 
				and sd.kelas_id='".$_SESSION['RAPORT']['kelas_id']."'
			where n.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and n.semester_id='".$_SESSION['RAPORT']['semester_id']."' and sd.status='masih_sekolah'
			group by n.siswa_detail_id order by jml_nilai desc");
	    $tentukan_juara_kelas->execute();

	    // insert juara kelas
		$juara = 0;
		$jmlNilai_sebelum = 0;
		$i = 0;
	   	while ($r=$tentukan_juara_kelas->fetch(PDO::FETCH_ASSOC)) {
	   		// generate juara
			if($r['jml_nilai'] != $jmlNilai_sebelum) {
				$jmlNilai_sebelum = $r['jml_nilai'];
				$juara++;
			}

			if($juara_kelas == true && isset($juara_kelas[$i]['juara_kelas_id'])) {
				$up = $this->db->prepare("UPDATE juara_kelas set rata_rata_nilai=:rata_rata_nilai, jml_nilai=:jml_nilai, juara=:juara, siswa_detail_id=:siswa_detail_id where juara_kelas_id=:juara_kelas_id and tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
				$up->execute([ ':rata_rata_nilai'=>$r['rata_rata_nilai'], ':jml_nilai'=>$r['jml_nilai'], ':juara'=>$juara, ':siswa_detail_id'=>$r['siswa_detail_id'], ':juara_kelas_id'=>$juara_kelas[$i]['juara_kelas_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);

			} else {
				$juara_kelas_id = $this->generate_uuid();
				$insert = $this->db->prepare("INSERT into juara_kelas set juara_kelas_id=:juara_kelas_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, rata_rata_nilai=:rata_rata_nilai, jml_nilai=:jml_nilai, juara=:juara");
				$insert->execute([ ':juara_kelas_id'=>$juara_kelas_id, ':siswa_detail_id'=>$r['siswa_detail_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':rata_rata_nilai'=>$r['rata_rata_nilai'], ':jml_nilai'=>$r['jml_nilai'], ':juara'=>$juara ]);
			}
			
	   		$hasil[] = $r;
	   		$hasil[$i] = array_merge($hasil[$i], ['juara'=>$juara]);
			$hasil[$i]['rata_rata_nilai'] = number_format($r['rata_rata_nilai'],2,',','.');
			$hasil[$i]['jml_nilai'] = number_format($r['jml_nilai'],2,',','');
	   		$i++;
	   	}

		return $hasil;
	}

	public function get_one_juara_kelas($siswa_detail_id) {
	    $get = $this->db->prepare("SELECT juara from juara_kelas where siswa_detail_id=:siswa_detail_id and tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and semester_id='".$_SESSION['RAPORT']['semester_id']."'");
	    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function delete_juara_kelas($dbS, $db, $siswa_detail_id, $action=null) {
	    $token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodGuru();
		if($cekLoginNo) {
			return !$cekLoginNo;// false
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

		$del = $this->db->prepare("DELETE from juara_kelas where siswa_detail_id=:siswa_detail_id $where");
		$del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
		if($del->rowCount() > 0){
			return true;
		}
		return false;
	}
}