<?php

/**
* 
*/
class user_admin extends config {

	public function tampil_user_admin($select=null) {
		if($select == null) {
			$select = "username";
		}
	    $get = $this->db->prepare("SELECT $select from admin");
	    $get->execute();
	    return $get->fetch(PDO::FETCH_ASSOC);
	}
	
	public function edit_user_admin() {
		$token = $this->cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}
		$cekLoginNo = $this->cekLoginNo_methodAdmin();
		if($cekLoginNo) {
			return !$cekLoginNo;//false
		}

		$this->form_validation([
			'username[Username]'=>'required|maxLength[32]',
			'passwordNow[Password Sekarang]'=>'required'
		], false);
		// filter min length passsword jika password ada
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		if(!empty(trim($password))) {
			$this->form_validation([
				'password[Password]'=>'minLength[8]'
			], false);
		}
		// cek password lama
		$passwordNow = filter_input(INPUT_POST, 'passwordNow', FILTER_SANITIZE_STRING);
		$passwordNowDB = $this->db->prepare("SELECT password from admin");
		$passwordNowDB->execute();
		$passwordNowDB = $passwordNowDB->fetch(PDO::FETCH_ASSOC)['password']??'';
		if(!isset($_SESSION['RAPORT']['form_errors']['passwordNow']) && !password_verify($passwordNow, $passwordNowDB)) {
			$_SESSION['RAPORT']['form_errors']['passwordNow'] = 'Password sekarang salah!';
		}
		$this->set_delimiter('<p class="pesan warning">','</p>');
		// cek form error
		if($this->has_formErrors()) {
			return false;
		}

		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		if(!empty(trim($password))) {
			$password = PASSWORD_HASH($password, PASSWORD_ARGON2I);
			$up = $this->db->prepare("UPDATE admin set username=:username, password=:password");
			$up->execute([ ':username'=>$username, ':password'=>$password]);
			return 'success';
		} else {
			$up = $this->db->prepare("UPDATE admin set username=:username");
			$up->execute([ ':username'=>$username ]);
			return 'success';
		}
	}

	public function pesan_edit_user_admin() {
	    if(isset($_SESSION['RAPORT']['pesan_edit_user_admin']) && $_SESSION['RAPORT']['pesan_edit_user_admin'] == "success") {
	    	unset($_SESSION['RAPORT']['pesan_edit_user_admin']);
	    	return '<p class="pesan good">Admin berhasil diedit!</p>';

	    } elseif(isset($_SESSION['RAPORT']['pesan_edit_user_admin'])) {
	    	unset($_SESSION['RAPORT']['pesan_edit_user_admin']);
	    	return '<p class="pesan warning">Admin gagal diedit!</p>';
	    }
	}
}