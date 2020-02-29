<?php

class raport extends config {
	public function cek_benar_kelas_siswa($dbS, $siswa_detail_id) {
		if($dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", "sd.siswa_detail_id", $_SESSION['RAPORT']['kelas_id'])) {
			return true;
		}
		return false;
	}

	public function generate_predikat($nilai, $kurang, $cukup, $baik, $sangat_baik) {
	    $kurang = explode("-", $kurang);
	    $cukup = explode("-", $cukup);
	    $baik = explode("-", $baik);
	    $sangat_baik = explode("-", $sangat_baik);
	    if($nilai >= $kurang[0] && $nilai < $cukup[0]) {
	    	return "D(kurang)";
	    } elseif($nilai >= $cukup[0] && $nilai < $baik[0]) {
	    	return "C(cukup)";
	    } elseif($nilai >= $baik[0] && $nilai < $sangat_baik[0]) {
	    	return "B(baik)";
	    } elseif($nilai >= $sangat_baik[0]) {
	    	return "A(sangat baik)";
	    }
	}

	public function cek_nilai_belum_dimasukkan($siswa_detail_id) {
	    $cek = $this->db->prepare("SELECT n.nilai_id
				from nilai as n
				RIGHT JOIN mata_pelajaran as mp ON mp.mata_pelajaran_id=n.mapel_id 
				    and n.siswa_detail_id=:siswa_detail_id
				    and n.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."'
				    and n.semester_id='".$_SESSION['RAPORT']['semester_id']."'
				JOIN tahun_ajaran as ta1 ON mp.awal_tahun_ajaran = ta1.tahun_ajaran_id
				JOIN tahun_ajaran as ta2 ON mp.akhir_tahun_ajaran = ta2.tahun_ajaran_id
				where mp.kelas_id='".$_SESSION['RAPORT']['kelas_id']."' and n.nilai_id is null and ta1.tahun <= '".$_SESSION['RAPORT']['tahun_ajaran']."' and ta2.tahun >= '".$_SESSION['RAPORT']['tahun_ajaran']."' limit 1");
	    $cek->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
	    return $cek->rowCount();
	}

	public function cek_status_akhir_belum_dimasukkan() {
	    $cek = $this->db->prepare("SELECT sd.siswa_detail_id, sd.nama_siswa FROM status_akhir_semester as sas
				RIGHT JOIN siswa_detail as sd ON sd.siswa_detail_id=sas.siswa_detail_id
    				and sas.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."'
    				and sas.semester_id='".$_SESSION['RAPORT']['semester_id']."'
				where sd.kelas_id='".$_SESSION['RAPORT']['kelas_id']."' and sd.status='masih_sekolah' and sas.status_akhir_semester_id is null 
				order by sd.nama_siswa asc");
	    $cek->execute();
	    while ($r=$cek->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    }
	    return @$hasil;
	}

	public function tampil_siswa_tidak_naik_kelas_tidak_lulus($status) {
	    $get = $this->db->prepare("SELECT sas.siswa_detail_id, sd.nama_siswa, (AVG(n.nilai_p)+AVG(n.nilai_k))/2 as rata_rata_nilai
            from status_akhir_semester as sas
            JOIN siswa_detail as sd USING(siswa_detail_id)
            JOIN nilai as n on n.siswa_detail_id=sas.siswa_detail_id 
            	and n.semester_id='".$_SESSION['RAPORT']['semester_id']."' 
            	and n.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."'
            where sas.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and sas.semester_id='".$_SESSION['RAPORT']['semester_id']."' and sd.kelas_id='".$_SESSION['RAPORT']['kelas_id']."' and sd.status='masih_sekolah' and sas.status_akhir $status group by sas.siswa_detail_id");
	    $get->execute();
	    $i = 0;
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[$i] = $r;
	    	$hasil[$i]['rata_rata_nilai'] = number_format($hasil[$i]['rata_rata_nilai'],2,',','.');
	    	$i++;
	    }
	    return @$hasil;
	}
	/* sikap */
		public function cek_has_sikap_siswa($siswa_detail_id) {
			$tahun_ajaran_id = $_SESSION['RAPORT']['tahun_ajaran_id']??'';
			$semester_id = $_SESSION['RAPORT']['semester_id']??'';

		    $cek = $this->db->prepare("SELECT sikap_siswa_id from sikap_siswa as ss
		    	JOIN siswa_detail as sd USING(siswa_detail_id) 
		    	where ss.tahun_ajaran_id=:tahun_ajaran_id and ss.semester_id=:semester_id and ss.siswa_detail_id=:siswa_detail_id");
		    $cek->execute([ ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id, ':siswa_detail_id'=>$siswa_detail_id ]);
		    return $cek->rowCount();
		}

		public function count_siswa_has_sikap($tahun_ajaran_id, $semester_id){
			$count = $this->db->prepare("SELECT sikap_siswa_id from sikap_siswa
				JOIN siswa_detail USING(siswa_detail_id)
				where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id and status='masih_sekolah'");
			$count->execute([':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id]);
			return $count->rowCount();
		}

		public function add_sikap($dbS) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'sikap[Sikap]' => 'required',
			], true);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			if($this->has_formErrors()) {
				return false;
			}

			$sikap_siswa_id = $this->generate_uuid();
			$sikap_siswa = filter_input(INPUT_POST, 'sikap', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
		 	$add = $this->db->prepare("INSERT into sikap_siswa set sikap_siswa_id=:sikap_siswa_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, sikap=:sikap");
		 	$add->execute([ ':sikap_siswa_id'=>$sikap_siswa_id, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':sikap'=>$sikap_siswa ]);
		 	return true;
		}

		public function tampil_sikap($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		    $get = $this->db->prepare("SELECT sikap from sikap_siswa as ss 
		    	where ss.siswa_detail_id=:siswa_detail_id and ss.tahun_ajaran_id=:tahun_ajaran_id and ss.semester_id=:semester_id");
		    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		    return $get->fetch(PDO::FETCH_ASSOC);
		}

		public function edit_sikap($dbS) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'sikap[Sikap]' => 'required',
			], true);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			if($this->has_formErrors()) {
				return false;
			}

			$sikap_siswa = filter_input(INPUT_POST, 'sikap', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
		 	$add = $this->db->prepare("UPDATE sikap_siswa set sikap=:sikap where siswa_detail_id=:siswa_detail_id and tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
		 	$add->execute([ ':sikap'=>$sikap_siswa, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
		 	return "success";
		}

		public function pesan_add_sikap() {
		    if(isset($_SESSION['RAPORT']['pesan_add_sikap']) && $_SESSION['RAPORT']['pesan_add_sikap'] == "gagal") {
		    	unset($_SESSION['RAPORT']['pesan_add_sikap']);
		    	return '<p class="pesan warning">Sikap gagal ditambahkan!</p>';
		    }
		}

		public function pesan_edit_sikap() {
		    if(isset($_SESSION['RAPORT']['pesan_edit_sikap']) && $_SESSION['RAPORT']['pesan_edit_sikap'] == "success") {
		    	unset($_SESSION['RAPORT']['pesan_edit_sikap']);
		    	return '<p class="pesan good">Sikap berhasil diedit!</p>';

		    } elseif(isset($_SESSION['RAPORT']['pesan_edit_sikap'])) {
		    	unset($_SESSION['RAPORT']['pesan_edit_sikap']);
		    	return '<p class="pesan warning">Sikap gagal diedit!</p>';
		    }
		}

		public function delete_sikap($dbS, $siswa_detail_id, $action=null) {
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
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}

		    $del = $this->db->prepare("DELETE from sikap_siswa where siswa_detail_id=:siswa_detail_id $where");
		    $del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
		    if($del->rowCount() > 0) {
		    	return true;
		    }
		    return false;
		}
	/* sikap */
	/* nilai deskripsi */
		public function tampil_nilai($siswa_detail_id, $tahun_ajaran_id, $semester_id, $select=null, $action=null) {
			// select default
			if($select == null) {
				$select = "n.nilai_k, n.deskripsi_k, n.nilai_p, n.deskripsi_p, mp.nama_mapel, mp.kelompok_mapel, mp.mata_pelajaran_id, km.kkm, km.predikat_d, km.predikat_c, km.predikat_b, km.predikat_a";
			}
			if(($_SESSION['RAPORT']['tahun_ajaran_id']??'') == $tahun_ajaran_id && ($_SESSION['RAPORT']['semester_id']??'') == $semester_id && $action == null) {
				$typeJoin = "RIGHT";
				$join = "JOIN tahun_ajaran as ta1 ON ta1.tahun_ajaran_id=mp.awal_tahun_ajaran
                    JOIN tahun_ajaran as ta2 ON ta2.tahun_ajaran_id=mp.akhir_tahun_ajaran";
				$where = "where mp.kelas_id='".($_SESSION['RAPORT']['kelas_id']??'')."' and ta1.tahun <= '".($_SESSION['RAPORT']['tahun_ajaran']??'')."' and ta2.tahun >= '".($_SESSION['RAPORT']['tahun_ajaran']??'')."'";
			} else {
				$typeJoin = null;
				$join = null;
				$where = null;
			}
			$get = $this->db->prepare("SELECT $select FROM nilai as n
                    $typeJoin JOIN mata_pelajaran as mp ON mp.mata_pelajaran_id=n.mapel_id 
                        and n.siswa_detail_id=:siswa_detail_id
                        and n.tahun_ajaran_id=:tahun_ajaran_id
                        and n.semester_id=:semester_id
                    JOIN kkm as km USING(kkm_id)
                    $join
                    $where order by mp.kelompok_mapel asc");
			$get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
			while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
				$hasil[]=$r;
			}
			return @$hasil;
		}

		public function count_siswa_has_nilai($tahun_ajaran_id, $semester_id) {
			$count = $this->db->prepare("SELECT nilai_id from nilai 
				JOIN siswa_detail USING(siswa_detail_id)
				where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id and status='masih_sekolah' group by siswa_detail_id");
			$count->execute([':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id]);
			return $count->rowCount();
		}

		public function cek_has_nilai_deskripsi($siswa_detail_id, $tahun_ajaran_id, $semester_id, $action=null) {

			if(($_SESSION['RAPORT']['tahun_ajaran_id']??'') == $tahun_ajaran_id && ($_SESSION['RAPORT']['semester_id']??'') == $semester_id && $action==null) {
				$join = "JOIN mata_pelajaran as mp ON mp.mata_pelajaran_id=n.mapel_id";
				$where = "mp.kelas_id='".($_SESSION['RAPORT']['kelas_id']??'')."' and";
			} else {
				$join = null;
				$where = null;
			}

		   	$cek = $this->db->prepare("SELECT n.nilai_id from nilai as n
		    	$join
		    	where $where n.tahun_ajaran_id=:tahun_ajaran_id and n.semester_id=:semester_id and n.siswa_detail_id=:siswa_detail_id limit 1");
		    $cek->execute([ ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id, ':siswa_detail_id'=>$siswa_detail_id ]);
		    return $cek->rowCount();
		}

		public function generate_param_for_form_validation($data) {
		    $param = [];
		    foreach($data as $r) {
		    	$param = array_merge($param, ['nilai_pengetahuan_'.$r['mata_pelajaran_id'].'[Nilai pengetahuan '.$r['nama_mapel'].']' => 'required|float']);
		    	$param = array_merge($param, ['nilai_keterampilan_'.$r['mata_pelajaran_id'].'[Nilai keterampilan '.$r['nama_mapel'].']' => 'required|float']);
		    	$param = array_merge($param, ['deskripsi_pengetahuan_'.$r['mata_pelajaran_id'].'[Deskripsi pengetahuan '.$r['nama_mapel'].']' => 'required']);
		    	$param = array_merge($param, ['deskripsi_keterampilan_'.$r['mata_pelajaran_id'].'[Deskripsi keterampilan '.$r['nama_mapel'].']' => 'required']);
		    }
		    return $param;
		}

		public function add_nilai_deskripsi($dbM, $dbS) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}

			// data mapel
			$mapel = $dbM->tampil_mapel($_SESSION['RAPORT']['kelas_id'], "mp.mata_pelajaran_id, mp.nama_mapel");
			if($mapel) {
				// generate param for form validation
				$param = $this->generate_param_for_form_validation($mapel);
				$this->form_validation($param, true);
				$this->set_delimiter('<p class="pesan warning">','</p>');
				// cek form errors
				if($this->has_formErrors()) {
					return false;
				}

				// add
				foreach($mapel as $r) {
					$nilai_p = str_replace(",", ".", filter_input(INPUT_POST, 'nilai_pengetahuan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING));
					$deskripsi_p = filter_input(INPUT_POST, 'deskripsi_pengetahuan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING);
					$nilai_k = str_replace(",", ".", filter_input(INPUT_POST, 'nilai_keterampilan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING));
					$deskripsi_k = filter_input(INPUT_POST, 'deskripsi_keterampilan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING);
					$nilai_id = $this->generate_uuid();

					$add = $this->db->prepare("INSERT into nilai set nilai_id=:nilai_id, siswa_detail_id=:siswa_detail_id, mapel_id=:mapel_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, nilai_p=:nilai_p, deskripsi_p=:deskripsi_p, nilai_k=:nilai_k, deskripsi_k=:deskripsi_k");
					$add->execute([ ':nilai_id'=>$nilai_id, ':siswa_detail_id'=>$siswa_detail_id, ':mapel_id'=>$r['mata_pelajaran_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':nilai_p'=>$nilai_p, ':deskripsi_p'=>$deskripsi_p, ':nilai_k'=>$nilai_k, ':deskripsi_k'=>$deskripsi_k ]);
				}
				return true;

			} else {
				return false;
			}
		}

		public function edit_nilai_deskripsi($dbM, $dbS) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}

			// data mapel
			$nilai_deskripsi = $this->tampil_nilai($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id'], "n.nilai_id, mp.mata_pelajaran_id, mp.nama_mapel, n.nilai_k, n.deskripsi_k, n.nilai_p, n.deskripsi_p");
			if($nilai_deskripsi) {
				// generate param for form validation
				$param = $this->generate_param_for_form_validation($nilai_deskripsi);
				$this->form_validation($param, false);
				$this->set_delimiter('<p class="pesan warning">','</p>');
				// cek form errors
				if($this->has_formErrors()) {
					return false;
				}

				// edit add
				foreach($nilai_deskripsi as $r) {
					$nilai_p = str_replace(",", ".", filter_input(INPUT_POST, 'nilai_pengetahuan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING));
					$deskripsi_p = filter_input(INPUT_POST, 'deskripsi_pengetahuan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING);
					$nilai_k = str_replace(",", ".", filter_input(INPUT_POST, 'nilai_keterampilan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING));
					$deskripsi_k = filter_input(INPUT_POST, 'deskripsi_keterampilan_'.$r['mata_pelajaran_id'], FILTER_SANITIZE_STRING);

					// jika nilai belum ada
					if($r['nilai_id'] == null) {
						$nilai_id = $this->generate_uuid();
						$add = $this->db->prepare("INSERT into nilai set nilai_id=:nilai_id, siswa_detail_id=:siswa_detail_id, mapel_id=:mapel_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, nilai_p=:nilai_p, deskripsi_p=:deskripsi_p, nilai_k=:nilai_k, deskripsi_k=:deskripsi_k");
						$add->execute([ ':nilai_id'=>$nilai_id, ':siswa_detail_id'=>$siswa_detail_id, ':mapel_id'=>$r['mata_pelajaran_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':nilai_p'=>$nilai_p, ':deskripsi_p'=>$deskripsi_p, ':nilai_k'=>$nilai_k, ':deskripsi_k'=>$deskripsi_k ]);
						
					} else {
						$edit = $this->db->prepare("UPDATE nilai set nilai_p=:nilai_p, deskripsi_p=:deskripsi_p, nilai_k=:nilai_k, deskripsi_k=:deskripsi_k where siswa_detail_id=:siswa_detail_id and mapel_id=:mapel_id and tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
						$edit->execute([ ':nilai_p'=>$nilai_p, ':deskripsi_p'=>$deskripsi_p, ':nilai_k'=>$nilai_k, ':deskripsi_k'=>$deskripsi_k, ':siswa_detail_id'=>$siswa_detail_id, ':mapel_id'=>$r['mata_pelajaran_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
					}	
				}
				return "success";

			} else {
				return false;
			}
		}

		public function pesan_add_nilai_deskripsi() {
		    if(isset($_SESSION['RAPORT']['pesan_add_nilai_deskripsi']) && $_SESSION['RAPORT']['pesan_add_nilai_deskripsi'] == "gagal") {
		    	unset($_SESSION['RAPORT']['pesan_add_nilai_deskripsi']);
		    	return '<p class="pesan warning">Nilai Deskripsi gagal ditambahkan!</p>';
		    }
		}

		public function pesan_edit_nilai_deskripsi() {
		    if(isset($_SESSION['RAPORT']['pesan_edit_nilai_deskripsi']) && $_SESSION['RAPORT']['pesan_edit_nilai_deskripsi'] == "success") {
		    	unset($_SESSION['RAPORT']['pesan_edit_nilai_deskripsi']);
		    	return '<p class="pesan good">Nilai Deskripsi berhasil diedit!</p>';

		    } elseif(isset($_SESSION['RAPORT']['pesan_edit_nilai_deskripsi'])) {
		    	unset($_SESSION['RAPORT']['pesan_edit_nilai_deskripsi']);
		    	return '<p class="pesan warning">Nilai Deskripsi gagal diedit!</p>';
		    }
		}

		public function delete_nilai_deskripsi($dbS, $siswa_detail_id, $action=null) {
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
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}
		    try {
		    	$del = $this->db->prepare("DELETE from nilai where siswa_detail_id=:siswa_detail_id $where");
		   	 	$del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);	
		    } catch (Exception $e) {}
		    if($del->rowCount() > 0) {
		    	return true;
		    }
		    return false;
		}
	/* nilai deskripsi */
	/* praktek_kerja_industri */
		public function add_praktik_kerja_industri($dbS) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'mitra_du_di[Mitra DU/DI]' => 'required|maxLength[32]',
				'lokasi[Lokasi]' => 'required',
				'lamanya[Lamanya]' => 'required|maxLength[15]'
			], false);
			// cek form errors
			$errors = $this->get_form_errors();
			if($errors) {
				return json_encode(['errors'=>$errors]);
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
			$prakerin_id = $this->generate_uuid();
			$mitra_du_di = filter_input(INPUT_POST, 'mitra_du_di', FILTER_SANITIZE_STRING);
			$lokasi = filter_input(INPUT_POST, 'lokasi', FILTER_SANITIZE_STRING);
			$lamanya = filter_input(INPUT_POST, 'lamanya', FILTER_SANITIZE_STRING);
			$keterangan = filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING);

			$add = $this->db->prepare("INSERT into prakerin set prakerin_id=:prakerin_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."', semester_id='".$_SESSION['RAPORT']['semester_id']."', mitra_du_di=:mitra_du_di, lokasi=:lokasi, lamanya=:lamanya, keterangan=:keterangan");
			$add->execute([ ':prakerin_id'=>$prakerin_id, ':siswa_detail_id'=>$siswa_detail_id, ':mitra_du_di'=>$mitra_du_di, ':lokasi'=>$lokasi, ':lamanya'=>$lamanya, ':keterangan'=>$keterangan ]);
			return json_encode(["success"=>'yes']);
		}

		public function tampil_praktik_kerja_industri($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		    $get = $this->db->prepare("SELECT prakerin_id, mitra_du_di, lokasi, lamanya, keterangan from prakerin where siswa_detail_id=:siswa_detail_id and tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
		    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
		    	$hasil[]=$r;
		    }
		    return @$hasil;
		}

		public function delete_praktik_kerja_industri($dbS, $reset_raport=false, $action=null) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			$whiteListAction = ['deleteSiswaKeluar','deleteSiswaLulus'];
			if($_SESSION['RAPORT']['level']=='admin' && in_array($action, $whiteListAction)) {
				$where2 = null;
			} else {
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where2 = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}

			$execute = [ ':siswa_detail_id'=>$siswa_detail_id ];
			if($reset_raport == false) {
				$prakerin_id = filter_input(INPUT_POST, 'prakerin_id', FILTER_SANITIZE_STRING);
				$where = "prakerin_id=:prakerin_id and";
				$execute = array_merge($execute, [':prakerin_id'=>$prakerin_id]);
			} else {
				$where = null;
			}

			$del = $this->db->prepare("DELETE from prakerin where $where siswa_detail_id=:siswa_detail_id $where2");
			$del->execute($execute);
			if($del->rowCount() > 0) {
		    	return json_encode(["success"=>'yes']);
		    }
		    return false;
		}
	/* praktek_kerja_industri */
	/* ekstrakurikuler */
		public function tampil_ekstrakurikuler($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		   	$get = $this->db->prepare("SELECT e.ekstrakurikuler_id, e.nama_ekstrakurikuler, e.nilai, e.keterangan from ekstrakurikuler as e 
		   		where e.siswa_detail_id=:siswa_detail_id and e.tahun_ajaran_id=:tahun_ajaran_id and e.semester_id=:semester_id");
		   	$get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		   	while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
		   		$hasil[]=$r;
		   	}
		   	return @$hasil;
		}

		public function add_ekstrakurikuler($dbS) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'nama_ekstrakurikuler[Nama ekstrakurikuler]' => 'required|maxLength[50]',
				'nilai[Nilai]' => 'required|float'
			], false);
			// cek form errors
			$errors = $this->get_form_errors();
			if($errors) {
				return json_encode(['errors'=>$errors]);
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}

			$tahun_ajaran_id = $_SESSION['RAPORT']['tahun_ajaran_id'];
			$semester_id = $_SESSION['RAPORT']['semester_id'];
			$nama_ekstrakurikuler = filter_input(INPUT_POST, 'nama_ekstrakurikuler', FILTER_SANITIZE_STRING);
			$ekstrakurikuler_id = $this->generate_uuid();
			$nilai = str_replace(",", ".", filter_input(INPUT_POST, 'nilai', FILTER_SANITIZE_STRING));
			$keterangan = filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING);
			$add = $this->db->prepare("INSERT into ekstrakurikuler set ekstrakurikuler_id=:ekstrakurikuler_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, nama_ekstrakurikuler=:nama_ekstrakurikuler, nilai=:nilai, keterangan=:keterangan");
			$add->execute([ ':ekstrakurikuler_id'=>$ekstrakurikuler_id, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id, ':nama_ekstrakurikuler'=>$nama_ekstrakurikuler, ':nilai'=>$nilai, ':keterangan'=>$keterangan ]);
			return json_encode(["success"=>'yes']);
		}

		public function delete_ekstrakurikuler($dbS, $reset_raport=false, $action=null) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			$whiteListAction = ['deleteSiswaKeluar','deleteSiswaLulus'];
			if($_SESSION['RAPORT']['level']=='admin' && in_array($action, $whiteListAction)) {
				$where2 = null;
			} else {
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where2 = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}

			$execute = [ ':siswa_detail_id'=>$siswa_detail_id ];
			if($reset_raport == false) {
				$ekstrakurikuler_id = filter_input(INPUT_POST, 'ekstrakurikuler_id', FILTER_SANITIZE_STRING);
				$where = "ekstrakurikuler_id=:ekstrakurikuler_id and";
				$execute = array_merge($execute, [':ekstrakurikuler_id'=>$ekstrakurikuler_id]);
			} else {
				$where = null;
			}

		    $delete = $this->db->prepare("DELETE from ekstrakurikuler where $where siswa_detail_id=:siswa_detail_id $where2");
		    $delete->execute($execute);
		    if($delete->rowCount() > 0) {
		    	return json_encode(["success"=>'yes']);
		    }
		    return false;
		}
	/* ekstrakurikuler */
	/* prestasi */
		public function tampil_prestasi($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		    $get = $this->db->prepare("SELECT p.prestasi_id, p.jenis_prestasi, p.keterangan from prestasi as p
		    	where p.siswa_detail_id=:siswa_detail_id and p.tahun_ajaran_id=:tahun_ajaran_id and p.semester_id=:semester_id");
		    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
		    	$hasil[]=$r;
		    }
		    return @$hasil;
		}

		public function add_prestasi($dbS) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'jenis_prestasi[Jenis prestasi]' => 'required|maxLength[50]',
				'keterangan[Keterangan]' => 'required'
			], false);
			// cek form errors
			$errors = $this->get_form_errors();
			if($errors) {
				return json_encode(['errors'=>$errors]);
			}
		
			$jenis_prestasi = filter_input(INPUT_POST, 'jenis_prestasi', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			$tahun_ajaran_id = $_SESSION['RAPORT']['tahun_ajaran_id'];
			$semester_id = $_SESSION['RAPORT']['semester_id'];
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}

			$prestasi_id = $this->generate_uuid();
			$keterangan = filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING);
			$add = $this->db->prepare("INSERT into prestasi set prestasi_id=:prestasi_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, jenis_prestasi=:jenis_prestasi, keterangan=:keterangan");
			$add->execute([ ':prestasi_id'=>$prestasi_id, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id, ':jenis_prestasi'=>$jenis_prestasi, ':keterangan'=>$keterangan ]);
			return json_encode(["success"=>'yes']);
		}

		public function delete_prestasi($dbS, $reset_raport=false, $action=null) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			$whiteListAction = ['deleteSiswaKeluar','deleteSiswaLulus'];
			if($_SESSION['RAPORT']['level']=='admin' && in_array($action, $whiteListAction)) {
				$where2 = null;
			} else {
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where2 = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}
			
			$execute = [ ':siswa_detail_id'=>$siswa_detail_id ];
			if($reset_raport == false) {
				$prestasi_id = filter_input(INPUT_POST, 'prestasi_id', FILTER_SANITIZE_STRING);
				$where = "prestasi_id=:prestasi_id and";
				$execute = array_merge($execute, [':prestasi_id'=>$prestasi_id]);
			} else {
				$where = null;
			}

		    try {
		    	$delete = $this->db->prepare("DELETE from prestasi where $where siswa_detail_id=:siswa_detail_id $where2");
		    	$delete->execute($execute);
		    } catch (Exception $e) {}
		    if($delete->rowCount() > 0) {
		    	return json_encode(["success"=>'yes']);
		    }
		    return false;
		}
	/* prestasi */
	/* ketidakhadiran */
		public function count_siswa_has_ketidakhadiran($tahun_ajaran_id, $semester_id) {
		    $count = $this->db->prepare("SELECT ketidakhadiran_id from ketidakhadiran 
		    	JOIN siswa_detail USING(siswa_detail_id)
		    	where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
		    $count->execute([':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id]);
		    return $count->rowCount();
		}

		public function cek_has_ketidakhadiran($siswa_detail_id) {
		    $cek = $this->db->prepare("SELECT k.ketidakhadiran_id from ketidakhadiran as k
		    	JOIN siswa_detail as sd USING(siswa_detail_id)
		    	where k.tahun_ajaran_id=:tahun_ajaran_id and k.semester_id=:semester_id and k.siswa_detail_id=:siswa_detail_id");
		    $cek->execute([ ':tahun_ajaran_id'=>($_SESSION['RAPORT']['tahun_ajaran_id']??''), ':semester_id'=>($_SESSION['RAPORT']['semester_id']??''), ':siswa_detail_id'=>$siswa_detail_id ]);
		    return $cek->rowCount();
		}

		public function tampil_ketidakhadiran($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		    $get = $this->db->prepare("SELECT k.sakit, k.izin, k.tanpa_keterangan, k.bolos from ketidakhadiran as k
		    	where k.siswa_detail_id=:siswa_detail_id and k.tahun_ajaran_id=:tahun_ajaran_id and k.semester_id=:semester_id");
		    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		    return $get->fetch(PDO::FETCH_ASSOC);
		}

		public function add_ketidakhadiran($dbS) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'sakit[Sakit]' => 'maxLength[2]|integer',
				'izin[Izin]' => 'maxLength[2]|integer',
				'tanpa_keterangan[Tanpa keterangan]' => 'maxLength[2]|integer',
				'bolos[Bolos]' => 'maxLength[2]|integer',
			], true);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			if($this->has_formErrors()) {
				return false;
			}

			$ketidakhadiran_id = $this->generate_uuid();
			$sakit = (int)filter_input(INPUT_POST, 'sakit', FILTER_SANITIZE_STRING);
			$izin = (int)filter_input(INPUT_POST, 'izin', FILTER_SANITIZE_STRING);
			$tanpa_keterangan = (int)filter_input(INPUT_POST, 'tanpa_keterangan', FILTER_SANITIZE_STRING);
			$bolos = (int)filter_input(INPUT_POST, 'bolos', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
		 	$add = $this->db->prepare("INSERT into ketidakhadiran set ketidakhadiran_id=:ketidakhadiran_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, sakit=:sakit, izin=:izin, tanpa_keterangan=:tanpa_keterangan, bolos=:bolos");
		 	$add->execute([ ':ketidakhadiran_id'=>$ketidakhadiran_id, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':sakit'=>$sakit, ':izin'=>$izin, ':tanpa_keterangan'=>$tanpa_keterangan, ':bolos'=>$bolos ]);
		 	return true;
		}

		public function edit_ketidakhadiran($dbS) {
			$token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'sakit[Sakit]' => 'maxLength[2]|integer',
				'izin[Izin]' => 'maxLength[2]|integer',
				'tanpa_keterangan[Tanpa keterangan]' => 'maxLength[2]|integer',
				'bolos[Bolos]' => 'maxLength[2]|integer',
			], false);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			if($this->has_formErrors()) {
				return false;
			}

			$sakit = (int)filter_input(INPUT_POST, 'sakit', FILTER_SANITIZE_STRING);
			$izin = (int)filter_input(INPUT_POST, 'izin', FILTER_SANITIZE_STRING);
			$tanpa_keterangan = (int)filter_input(INPUT_POST, 'tanpa_keterangan', FILTER_SANITIZE_STRING);
			$bolos = (int)filter_input(INPUT_POST, 'bolos', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
		 	$edit = $this->db->prepare("UPDATE ketidakhadiran set sakit=:sakit, izin=:izin, tanpa_keterangan=:tanpa_keterangan, bolos=:bolos where siswa_detail_id=:siswa_detail_id and tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
		 	$edit->execute([ ':sakit'=>$sakit, ':izin'=>$izin, ':tanpa_keterangan'=>$tanpa_keterangan, ':bolos'=>$bolos, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
		 	return "success";
		}

		public function pesan_add_ketidakhadiran() {
		    if(isset($_SESSION['RAPORT']['pesan_add_ketidakhadiran']) && $_SESSION['RAPORT']['pesan_add_ketidakhadiran'] == "gagal") {
		    	unset($_SESSION['RAPORT']['pesan_add_ketidakhadiran']);
		    	return '<p class="pesan warning">Ketidakhadiran gagal ditambahkan!</p>';
		    }
		}

		public function pesan_edit_ketidakhadiran() {
		    if(isset($_SESSION['RAPORT']['pesan_edit_ketidakhadiran']) && $_SESSION['RAPORT']['pesan_edit_ketidakhadiran'] == "success") {
		    	unset($_SESSION['RAPORT']['pesan_edit_ketidakhadiran']);
		    	return '<p class="pesan good">Ketidakhadiran berhasil diedit!</p>';

		    } elseif(isset($_SESSION['RAPORT']['pesan_edit_ketidakhadiran'])) {
		    	unset($_SESSION['RAPORT']['pesan_edit_ketidakhadiran']);
		    	return '<p class="pesan warning">Ketidakhadiran gagal diedit!</p>';
		    }
		}

		public function delete_ketidakhadiran($dbS, $siswa_detail_id, $action=null) {
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
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}

		    $del = $this->db->prepare("DELETE from ketidakhadiran where siswa_detail_id=:siswa_detail_id $where");
		    $del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
		    if($del->rowCount() > 0) {
		    	return true;
		    }
		    return false;
		}
	/* ketidakhadiran */
	/* catatan wali kelas */
		public function cek_has_catatan_wali_kelas($siswa_detail_id) {
		    $cek = $this->db->prepare("SELECT cwk.catatan_wali_kelas_id from catatan_wali_kelas as cwk
		    	where cwk.tahun_ajaran_id=:tahun_ajaran_id and cwk.semester_id=:semester_id and cwk.siswa_detail_id=:siswa_detail_id");
		    $cek->execute([ ':tahun_ajaran_id'=>($_SESSION['RAPORT']['tahun_ajaran_id']??''), ':semester_id'=>($_SESSION['RAPORT']['semester_id']??''), ':siswa_detail_id'=>$siswa_detail_id ]);
		    return $cek->rowCount();
		}

		public function tampil_catatan_wali_kelas($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		    $get = $this->db->prepare("SELECT cwk.catatan from catatan_wali_kelas as cwk 
		    	where cwk.siswa_detail_id=:siswa_detail_id and cwk.tahun_ajaran_id=:tahun_ajaran_id and cwk.semester_id=:semester_id");
		    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		    return $get->fetch(PDO::FETCH_ASSOC);
		}

		public function add_catatan_wali_kelas($dbS) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'catatan[Catatan]' => 'required'
			], false);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			if($this->has_formErrors()) {
				return false;
			}

			$catatan_wali_kelas_id = $this->generate_uuid();
			$catatan = filter_input(INPUT_POST, 'catatan', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
		 	$add = $this->db->prepare("INSERT into catatan_wali_kelas set catatan_wali_kelas_id=:catatan_wali_kelas_id, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id=:tahun_ajaran_id, semester_id=:semester_id, catatan=:catatan");
		 	$add->execute([ ':catatan_wali_kelas_id'=>$catatan_wali_kelas_id, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':catatan'=>$catatan ]);
		 	return true;
		}

		public function edit_catatan_wali_kelas($dbS) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$this->form_validation([
				'catatan[Catatan]' => 'required'
			], false);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			if($this->has_formErrors()) {
				return false;
			}

			$catatan = filter_input(INPUT_POST, 'catatan', FILTER_SANITIZE_STRING);
			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}
		 	$add = $this->db->prepare("UPDATE catatan_wali_kelas set catatan=:catatan where siswa_detail_id=:siswa_detail_id and tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id");
		 	$add->execute([ ':catatan'=>$catatan, ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':semester_id'=>$_SESSION['RAPORT']['semester_id'] ]);
		 	return "success";
		}

		public function pesan_add_catatan_wali_kelas() {
		    if(isset($_SESSION['RAPORT']['pesan_add_catatan_wali_kelas']) && $_SESSION['RAPORT']['pesan_add_catatan_wali_kelas'] == "gagal") {
		    	unset($_SESSION['RAPORT']['pesan_add_catatan_wali_kelas']);
		    	return '<p class="pesan warning">Catatan wali kelas gagal ditambahkan!</p>';
		    }
		}

		public function pesan_edit_catatan_wali_kelas() {
		    if(isset($_SESSION['RAPORT']['pesan_edit_catatan_wali_kelas']) && $_SESSION['RAPORT']['pesan_edit_catatan_wali_kelas'] == "success") {
		    	unset($_SESSION['RAPORT']['pesan_edit_catatan_wali_kelas']);
		    	return '<p class="pesan good">Catatan wali kelas berhasil diedit!</p>';

		    } elseif(isset($_SESSION['RAPORT']['pesan_edit_catatan_wali_kelas'])) {
		    	unset($_SESSION['RAPORT']['pesan_edit_catatan_wali_kelas']);
		    	return '<p class="pesan warning">Catatan wali kelas gagal diedit!</p>';
		    }
		}

		public function delete_catatan_wali_kelas($dbS, $siswa_detail_id, $action=null) {
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
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}

			$del = $this->db->prepare("DELETE from catatan_wali_kelas where siswa_detail_id=:siswa_detail_id $where");
			$del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
			if($del->rowCount() > 0){
				return true;
			}
			return false;
		}
	/* catatan wali kelas */
	/* status akhir semester */
		public function generate_where($lama_belajar) {
		    $where = null;
			if($lama_belajar == 3) {
				if($_SESSION['RAPORT']['kelas'] >= 'X' && $_SESSION['RAPORT']['kelas'] < 'XI') {
					$where = 'and kelas >= "XI" and kelas < "XII"';
				} elseif($_SESSION['RAPORT']['kelas'] >= 'XI') {
					$where = 'and kelas >= "XII"';
				}
			}
			if($lama_belajar == 4) {
				if($_SESSION['RAPORT']['kelas'] >= 'X' && $_SESSION['RAPORT']['kelas'] < 'XI') {
					$where = 'and kelas >= "XI" and kelas < "XII"';
				} elseif($_SESSION['RAPORT']['kelas'] >= 'XI' && $_SESSION['RAPORT']['kelas'] < 'XII') {
					$where = 'and kelas >= "XII" and kelas < "XIII"';
				} elseif($_SESSION['RAPORT']['kelas'] >= 'XII') {
					$where = 'and kelas >= "XIII"';
				}
			}
			return $where;
		}

		public function count_siswa_has_status_akhir_semester($tahun_ajaran_id, $semester_id) {
		    $count = $this->db->prepare("SELECT status_akhir_semester_id from status_akhir_semester 
		    	JOIN siswa_detail USING(siswa_detail_id)
		    	where tahun_ajaran_id=:tahun_ajaran_id and semester_id=:semester_id and status='masih_sekolah'");
		    $count->execute([':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id]);
		    return $count->rowCount();
		}

		public function cek_has_status_akhir_semester($siswa_detail_id) {
		    $cek = $this->db->prepare("SELECT sas.status_akhir_semester_id from status_akhir_semester as sas 
		    	where sas.siswa_detail_id=:siswa_detail_id and sas.tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and sas.semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'");
		    $cek->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
		    return $cek->rowCount();
		}

		private function cek_benar_kelas_id($dbK, $status_akhir_semester) {
			$kelas_id = explode("_", $status_akhir_semester)[3];
			if($dbK->get_one_kelas($kelas_id, 'kelas_id')) {
				return true;
			}
			return false;
		}

		// for run kenaikan kelas
		public function tampil_status_akhir_semester() {
		    $get = $this->db->prepare("SELECT status_akhir, siswa_detail_id from status_akhir_semester where semester_id=:semester_id and tahun_ajaran_id=:tahun_ajaran_id");
		    $get->execute([':semester_id'=>$_SESSION['RAPORT']['semester_id'], ':tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id']]);
		    while($r=$get->fetch(PDO::FETCH_ASSOC)) {
		    	$hasil[]=$r;
		    }
		    return @$hasil;
		}

		public function get_one_status_akhir_semester($siswa_detail_id, $tahun_ajaran_id, $semester_id) {
		    $get = $this->db->prepare("SELECT sas.status_akhir 
		    	from status_akhir_semester as sas
		    	where sas.siswa_detail_id=:siswa_detail_id and sas.tahun_ajaran_id=:tahun_ajaran_id and sas.semester_id=:semester_id");
		    $get->execute([ ':siswa_detail_id'=>$siswa_detail_id, ':tahun_ajaran_id'=>$tahun_ajaran_id, ':semester_id'=>$semester_id ]);
		    return $get->fetch(PDO::FETCH_ASSOC);
		}

		public function add_status_akhir_semester($dbS, $dbIS, $dbK) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}

			$this->form_validation([
				'status_akhir_semester[Status akhir semester]' => 'required'
			], false);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
			if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) {
				// update no Ujian Nasional
				$dbS->update_no_un($siswa_detail_id);
			}
			if($this->has_formErrors()) {
				return false;
			}

			$status_akhir_semester = filter_input(INPUT_POST, 'status_akhir_semester', FILTER_SANITIZE_STRING);
			if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) {
				if(!preg_match("/^tidak_lulus|lulus\z/", $status_akhir_semester)){
					return false;
				}
			} else {
				if(!preg_match("/^tinggal_di_kelas_[\d\w-]+\z/i", $status_akhir_semester) && !preg_match("/^naik_ke_kelas_[\d\w-]+\z/i", $status_akhir_semester)) {
					return false;
				} else if(!$this->cek_benar_kelas_id($dbK, $status_akhir_semester)) {
					return false;
				}
			}

			$status_akhir_semester_id = $this->generate_uuid();
			$add = $this->db->prepare("INSERT into status_akhir_semester set status_akhir_semester_id=:status_akhir_semester_id, status_akhir
				=:status_akhir, siswa_detail_id=:siswa_detail_id, tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."', semester_id='".$_SESSION['RAPORT']['semester_id']."'");
			$add->execute([ ':status_akhir_semester_id'=>$status_akhir_semester_id, ':status_akhir'=>$status_akhir_semester, ':siswa_detail_id'=>$siswa_detail_id ]);
			return true;
		}

		public function edit_status_akhir_semester($dbS, $dbIS, $dbK) {
		    $token = $this->cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
			$cekLoginNo = $this->cekLoginNo_methodGuru();
			if($cekLoginNo) {
				return !$cekLoginNo;// false
			}

			$siswa_detail_id = filter_input(INPUT_POST, 'siswa_detail_id', FILTER_SANITIZE_STRING);
			if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
				return false;
			}

			$this->form_validation([
				'status_akhir_semester[Status akhir semester]' => 'required'
			], true);
			$this->set_delimiter('<p class="pesan warning">','</p>');
			// cek form errors
			$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
			if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) {
				// update no Ujian Nasional
				$dbS->update_no_un($siswa_detail_id);
			}
			if($this->has_formErrors()) {
				return false;
			}

			$status_akhir_semester = filter_input(INPUT_POST, 'status_akhir_semester', FILTER_SANITIZE_STRING);
			if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) {
				if(!preg_match("/^tidak_lulus|lulus\z/", $status_akhir_semester)){
					return false;
				}
			} else {
				if(!preg_match("/^tinggal_di_kelas_[\d\w-]+\z/i", $status_akhir_semester) && !preg_match("/^naik_ke_kelas_[\d\w-]+\z/i", $status_akhir_semester)) {
					return false;
				} else if(!$this->cek_benar_kelas_id($dbK, $status_akhir_semester)) {
					return false;
				}
			}

			$add = $this->db->prepare("UPDATE status_akhir_semester set status_akhir
				=:status_akhir where siswa_detail_id=:siswa_detail_id and tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and semester_id='".$_SESSION['RAPORT']['semester_id']."'");
			$add->execute([ ':status_akhir'=>$status_akhir_semester, ':siswa_detail_id'=>$siswa_detail_id ]);
			return "success";
		}

		public function pesan_add_status_akhir_semester() {
		    if(isset($_SESSION['RAPORT']['pesan_add_status_akhir_semester']) && $_SESSION['RAPORT']['pesan_add_status_akhir_semester'] == "gagal") {
		    	unset($_SESSION['RAPORT']['pesan_add_status_akhir_semester']);
		    	return '<p class="pesan warning">Status akhir semester gagal ditambahkan!</p>';
		    }
		}

		public function pesan_edit_status_akhir_semester() {
		    if(isset($_SESSION['RAPORT']['pesan_edit_status_akhir_semester']) && $_SESSION['RAPORT']['pesan_edit_status_akhir_semester'] == "success") {
		    	unset($_SESSION['RAPORT']['pesan_edit_status_akhir_semester']);
		    	return '<p class="pesan good">Status akhir semester berhasil diedit!</p>';

		    } elseif(isset($_SESSION['RAPORT']['pesan_edit_status_akhir_semester'])) {
		    	unset($_SESSION['RAPORT']['pesan_edit_status_akhir_semester']);
		    	return '<p class="pesan warning">Status akhir semester gagal diedit!</p>';
		    }
		}

		public function delete_status_semester($dbS, $siswa_detail_id, $action=null) {
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
				if(!$this->cek_benar_kelas_siswa($dbS, $siswa_detail_id)) {
					return false;
				}
				$where = "and tahun_ajaran_id='".($_SESSION['RAPORT']['tahun_ajaran_id']??'')."' and semester_id='".($_SESSION['RAPORT']['semester_id']??'')."'";
			}
			

			$del = $this->db->prepare("DELETE from status_akhir_semester where siswa_detail_id=:siswa_detail_id $where");
			$del->execute([ ':siswa_detail_id'=>$siswa_detail_id ]);
			if($del->rowCount() > 0){
				return true;
			}
			return false;
		}
	/* status akhir semester */
	/* transkip nilai */
		public function get_nilai_transkip($siswa_detail_id) {
		    $get = $this->db->prepare("SELECT nilai_k, nilai_p, nama_mapel, tahun, semester from nilai
				join mata_pelajaran ON mata_pelajaran_id=mapel_id
				JOIN tahun_ajaran USING(tahun_ajaran_id)
				JOIN semester using(semester_id)
				where siswa_detail_id =:siswa_detail_id
				order by tahun");
		    $get->execute([':siswa_detail_id'=>$siswa_detail_id]);
		    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
		    	$hasil[]=$r;
		    }
		    return @$hasil;
		}

		public function get_awal_akhir_tahun_ajaran_nilai($siswa_detail_id) {
		    $get = $this->db->prepare("SELECT MAX(tahun) as tahun_akhir, MIN(tahun) as tahun_awal from nilai
		    	JOIN tahun_ajaran USING(tahun_ajaran_id)
		    	where siswa_detail_id=:siswa_detail_id");
		    $get->execute([':siswa_detail_id'=>$siswa_detail_id]);
		    return $get->fetch(PDO::FETCH_ASSOC);
		}
	/* transkip nilai */
}