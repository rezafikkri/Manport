<?php

/**
* 
*/
class identitas_sekolah extends config {
	public function tampil_identitas_sekolah($select=null) {
		// select default
		if($select == null) {
		   $select = "nama_sekolah, alamat, lama_belajar, kabupaten, nama_kepala_sekolah, nip_kepala_sekolah, provinsi, logo_prov";
		}
		$get = $this->db->prepare("SELECT $select from identitas_sekolah");
		$get->execute();
	    return $get->fetch(PDO::FETCH_ASSOC);
	}

	private function cek_has_identitas_sekolah() {
	    $cek = $this->db->prepare("SELECT identitas_sekolah_id from identitas_sekolah");
	    $cek->execute();
	    return $cek->rowCount();
	}

	private function upload($nama_file_lama=null) {
		$error = $_FILES['logoProvinsi']['error'];
		$nama_file = $_FILES['logoProvinsi']['name'];
		$ukuran_file = $_FILES['logoProvinsi']['size'];
		$tmpName = $_FILES['logoProvinsi']['tmp_name'];

		// jika gambar tidak ada
		if($error == 4) {
			$_SESSION['RAPORT']['form_errors']['logoProvinsi'] = 'File tidak boleh kosong';
			return false;
		}

		if($error == 0) {
			// cek extensi valid
			$ekstensiGambarValid = ['jpg','jpeg','png'];
			$ekstensiGambar = explode(".", $nama_file);
			$ekstensiGambar = strtolower(end($ekstensiGambar));
			if(!in_array($ekstensiGambar, $ekstensiGambarValid)) {
				$_SESSION['RAPORT']['form_errors']['logoProvinsi'] = 'Ekstensi file hanya boleh jpg, jpeg, png';
				return false;
			}

			// cek ukuran file
			if($ukuran_file > 1000000) {
				$_SESSION['RAPORT']['form_errors']['logoProvinsi'] = 'Ukuran file tidak boleh lebih dari 1MB';
				return false;
			}

			// rename nama file
			$nama_file_new = bin2hex(random_bytes(8)).'.'.$ekstensiGambar;
			if(move_uploaded_file($tmpName, '../../assets/img/logo/'.$nama_file_new)) {
				// delete gambar lama
				if($nama_file_lama != null && file_exists("../../assets/img/logo/".$nama_file_lama)) {
						unlink("../../assets/img/logo/".$nama_file_lama);
				}

				return $nama_file_new;
			} else {
				$_SESSION['RAPORT']['form_errors']['logoProvinsi'] = 'File gagal diupload';
				return false;
			}
		} else {
			$_SESSION['RAPORT']['form_errors']['logoProvinsi'] = 'File gagal diupload';
				return false;
		}
	}
	
	public function add_identitas_sekolah() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}
		// jika identitas sekolah sudah ada
		if($this->cek_has_identitas_sekolah() > 0) {
			return true;
		}

		$nama_file = $this->upload();
		$this->form_validation([
			'nama_sekolah[Nama sekolah]' => 'required|maxLength[32]',
			'alamat[Alamat alamat]' => 'required',
			'lama_belajar[Lama belajar]' => 'required|must[3,4]',
			'provinsi[Provinsi]' => 'required|maxLength[32]',
			'kabupaten[Kabupaten]' => 'required|maxLength[30]',
			'nama_kepala_sekolah[Nama kepala sekolah]' => 'required|maxLength[32]',
			'nip_kepala_sekolah[Nip kepala sekolah]' => 'required|maxLength[18]|integer',
		], true);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form errors
		if($this->has_formErrors()) {
			return false;
		}

		$identitas_sekolah_id = config::generate_uuid();
		$nama_sekolah = filter_input(INPUT_POST, 'nama_sekolah', FILTER_SANITIZE_STRING);
		$alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
		$lama_belajar = filter_input(INPUT_POST, 'lama_belajar', FILTER_SANITIZE_STRING);
		$kabupaten = filter_input(INPUT_POST, 'kabupaten', FILTER_SANITIZE_STRING);
		$nama_kepala_sekolah = filter_input(INPUT_POST, 'nama_kepala_sekolah', FILTER_SANITIZE_STRING);
		$nip_kepala_sekolah = filter_input(INPUT_POST, 'nip_kepala_sekolah', FILTER_SANITIZE_STRING);
		$provinsi = filter_input(INPUT_POST, 'provinsi', FILTER_SANITIZE_STRING);

		$add = $this->db->prepare("INSERT into identitas_sekolah set identitas_sekolah_id=:identitas_sekolah_id, nama_sekolah=:nama_sekolah, alamat=:alamat, lama_belajar=:lama_belajar, kabupaten=:kabupaten, nama_kepala_sekolah=:nama_kepala_sekolah, nip_kepala_sekolah=:nip_kepala_sekolah, logo_prov=:logo_prov, provinsi=:provinsi");
		$add->execute([ ':identitas_sekolah_id'=>$identitas_sekolah_id, ':nama_sekolah'=>$nama_sekolah, ':alamat'=>$alamat, ':lama_belajar'=>$lama_belajar, ':kabupaten'=>$kabupaten, ':nama_kepala_sekolah'=>$nama_kepala_sekolah, ':nip_kepala_sekolah'=>$nip_kepala_sekolah, ':logo_prov'=>$nama_file, ':provinsi'=>$provinsi ]);
		return true;
	}

	public function edit_identitas_sekolah() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		if($_FILES['logoProvinsi']['error'] == 0) {
			$nama_file_lama = $this->tampil_identitas_sekolah('logo_prov')['logo_prov']??'';
			$nama_file = $this->upload($nama_file_lama);
		} else {
			$nama_file = null;
		}
		$this->form_validation([
			'nama_sekolah[Nama sekolah]' => 'required|maxLength[32]',
			'alamat[Alamat]' => 'required',
			'lama_belajar[Lama belajar]' => 'required|must[3,4]',
			'kabupaten[Kabupaten]' => 'required|maxLength[30]',
			'nama_kepala_sekolah[Nama kepala sekolah]' => 'required|maxLength[32]',
			'nip_kepala_sekolah[Nip kepala sekolah]' => 'required|maxLength[18]|integer',
			'provinsi[Provinsi]' => 'required|maxLength[32]'
		], true);
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form errors
		if($this->has_formErrors()) {
			return false;
		}

		$nama_sekolah = filter_input(INPUT_POST, 'nama_sekolah', FILTER_SANITIZE_STRING);
		$alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
		$lama_belajar = filter_input(INPUT_POST, 'lama_belajar', FILTER_SANITIZE_STRING);
		$kabupaten = filter_input(INPUT_POST, 'kabupaten', FILTER_SANITIZE_STRING);
		$nama_kepala_sekolah = filter_input(INPUT_POST, 'nama_kepala_sekolah', FILTER_SANITIZE_STRING);
		$nip_kepala_sekolah = filter_input(INPUT_POST, 'nip_kepala_sekolah', FILTER_SANITIZE_STRING);
		$provinsi = filter_input(INPUT_POST, 'provinsi', FILTER_SANITIZE_STRING);

		$execute = [ ':nama_sekolah'=>$nama_sekolah, ':alamat'=>$alamat, ':lama_belajar'=>$lama_belajar, ':kabupaten'=>$kabupaten, ':nama_kepala_sekolah'=>$nama_kepala_sekolah, ':nip_kepala_sekolah'=>$nip_kepala_sekolah, ':provinsi'=>$provinsi ];
		if($nama_file != null) {
			$up = ", logo_prov=:logo_prov";
			$execute = array_merge($execute, [':logo_prov'=>$nama_file]);
		} else {
			$up = null;
		}

		$add = $this->db->prepare("UPDATE identitas_sekolah set nama_sekolah=:nama_sekolah, alamat=:alamat, lama_belajar=:lama_belajar, kabupaten=:kabupaten, nama_kepala_sekolah=:nama_kepala_sekolah, nip_kepala_sekolah=:nip_kepala_sekolah, provinsi=:provinsi $up");
		$add->execute($execute);
		return "success";
	}

	public function pesan_add_identitas_sekolah() {
	    if(isset($_SESSION['RAPORT']['pesan_add_identitas_sekolah']) && $_SESSION['RAPORT']['pesan_add_identitas_sekolah'] == "gagal") {
	    	unset($_SESSION['RAPORT']['pesan_add_identitas_sekolah']);
	    	return '<p class="pesan warning">Identitas sekolah gagal ditambahkan!</p>';
	    }
	}

	public function pesan_edit_identitas_sekolah() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_identitas_sekolah']) && $_SESSION['RAPORT']['pesan_edit_identitas_sekolah'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_identitas_sekolah']);
	    	return '<p class="pesan good">Identitas sekolah berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_identitas_sekolah'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_identitas_sekolah']);
	    	return '<p class="pesan warning">Identitas sekolah gagal diedit!</p>';
	    }
	}
}