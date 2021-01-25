<?php

class config  {
	// data to connect db
	private $host = "localhost";
	protected $dbname = "raport";
	private $username = "root";
	private $password = "";
	// db object
	protected $db;
	
	function __construct()
	{
		date_default_timezone_set("Asia/Jakarta");
		if(!isset($_SESSION)) {
			session_start();
		}

		ob_start();
				
		try {
			$this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname",$this->username,$this->password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException$e)
		{
			echo "gagal konek".$e->getMessage()."<br>";
		}
	}

	public static function protocol() {
		if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}
		return $protocol;
	}

	public static function base_url($uri='') {
		if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}
		return self::protocol().$_SERVER['HTTP_HOST']."/Manport/".$uri;
	}

	public static function generate_uuid() {
		$data = openssl_random_pseudo_bytes(16);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	public static function change_idForQuery_IN($id) {
		if(!empty(trim($id))) {
			$id = explode(',', $id);
			$questionmarks = str_repeat('?,', count($id)-1).'?';
			return [ 'id'=>$id, 'questionmarks'=>$questionmarks ];
		} else {
			return null;
		}
	}

	public function sendDataRealTime($id, $msg) {
		echo "id: $id" . PHP_EOL;
		echo 'data: {'.$msg.'}' . PHP_EOL;
		echo PHP_EOL;
		ob_flush();
		flush();
		return true;
	}

	/* form validation */
		private function maxLength_minLength($data,$type,$rule,$fieldForHuman) {
			$rule = str_replace("]", "", $rule);
			$arrRule = explode("[", $rule);

			if($type=="maxLength" && strlen($data)>end($arrRule)) {
				return $fieldForHuman." tidak boleh lebih dari ".end($arrRule)." karakter!";

			} elseif($type=="minLength" && strlen($data)<end($arrRule)) {
				return $fieldForHuman." tidak boleh kurang dari ".end($arrRule)." karakter!";

			} else {
				return null;
			}
		}

		private function generate_arrRuleReplace($rule){
			return explode("[", str_replace([']'], "", $rule));
		}

		private function cek_rules($data, $fieldForHuman, $rule) {
		    $rule = explode('|', $rule);
		    for($i = 0; $i < count($rule); $i++) {

		    	// required
		    	if(strtolower($rule[$i]) == "required") {
		    		if(empty(trim($data))) {
		    			return $fieldForHuman." tidak boleh kosong!";
		    		}
		    	}
		    	// max length
		    	else if(preg_match("/^maxLength\[\d+\]\z/i", $rule[$i])) {
		    		$cek = $this->maxLength_minLength($data,'maxLength',$rule[$i],$fieldForHuman);
		    		if($cek) {
		    			return $cek;
		    		}
		    	}
		    	// min length
		    	else if(preg_match("/^minLength\[\d+\]\z/i", $rule[$i])) {
		    		$cek = $this->maxLength_minLength($data,'minLength',$rule[$i],$fieldForHuman);
		    		if($cek) {
		    			return $cek;
		    		}
		    	}
		    	// email
		    	else if(strtolower($rule[$i]) == "email") {
		    		if(!empty(trim($data)) && !filter_var($data, FILTER_VALIDATE_EMAIL)) {
		    			return $fieldForHuman." salah!";
		    		}
		    	}
		    	// integer
		    	else if(strtolower($rule[$i]) == "integer") {
		    		if(!empty(trim($data)) && !preg_match("/^\+?[\d]+\z/", $data)) {
		    			return $fieldForHuman." salah!";
		    		}
		    	}
		    	// float
		    	else if(strtolower($rule[$i]) == "float") {
		    		if(!empty(trim($data)) && !preg_match("/^\d+,?\.?\d*\z/", $data)) {
		    			return $fieldForHuman." salah!";
		    		}
		    	}
		    	// unique table.field
		    	else if(!empty(trim($data)) && preg_match("/(^unique\[\w+\.\w+\])(\[\w+\.[\w\d-]+\])?\z/i", $rule[$i])) {
		    		$arrRuleReplace = $this->generate_arrRuleReplace($rule[$i]);
					$arrWhere1 = explode(".", $arrRuleReplace[1]);
					$table = $arrWhere1[0];
					$field1 = $arrWhere1[1];
					$execute = [ ':'.$field1 => $data ];

					if(isset($arrRuleReplace[2])) {
						$arrWhere2 = explode(".", $arrRuleReplace[2]);
						$field2 = $arrWhere2[0];
						$value = $arrWhere2[1];
						$where2 = 'and '.$field2.'!=:'.$field2;
						$execute = array_merge($execute, [':'.$field2 => $value]);
					} else {
						$where2 = null;
					}

		    		// cek apakah data sudah ada didatabase
		    		$cek = $this->db->prepare("SELECT $field1 from $table where $field1=:$field1 $where2");
		    		$cek->execute($execute);
		    		if($cek->rowCount() >= 1) {
		    			return $fieldForHuman." sudah ada, mohon cari ".$fieldForHuman." yang lain!";
		    		}
		    	}
		    	// must[]
		    	elseif(!empty(trim($data)) && preg_match("/^must\[[\w\s-]+(,[\w\s-]+)*\]\z/i", $rule[$i])) {
					$arrRuleReplace = $this->generate_arrRuleReplace($rule[$i]);
					$whiteListMust = explode(",", str_replace(" ", "", end($arrRuleReplace)));
					if(!in_array($data, $whiteListMust)) {
						return $fieldForHuman." harus berisi ".implode(" atau ", $whiteListMust);
					}
				}
				// regex custom
				elseif(!empty(trim($data)) && preg_match("/^regex\[\s.+\s\]\z/i", $rule[$i])) {
					$arrRuleReplace = explode("[ ", str_replace([' ]'], "", $rule[$i]));;
					$regexCustom = end($arrRuleReplace);
					if(!preg_match("$regexCustom", $data)) {
						return $fieldForHuman." salah!";
					}
				}
		    }

		    return false;
		}

		private function generate_realField_fieldForHuman($field) {
			if(preg_match("/^[\w\d\-\s]+\[[\w\d\-\s\/,&]+\]\z/", $field)) {
				$arrField = explode("[", $field);
				$realField = $arrField[0];
				$fieldForHuman = str_replace("]", "", $arrField[1]);
			} else {
				$realField = $field;
			}
			if(!isset($fieldForHuman)) {
				$fieldForHuman = $realField;
			}
			return ['realField'=>$realField, 'fieldForHuman'=>$fieldForHuman];
		}

		public function form_validation($param=null, $old_val=true) {
			if($param != null && is_array($param)) {
				foreach($param as $field=>$rule) {
					$arrDataField = $this->generate_realField_fieldForHuman($field);
					$data = filter_input(INPUT_POST, $arrDataField['realField'], FILTER_SANITIZE_STRING);

					// jika data tidak valid
					$cek = $this->cek_rules($data, $arrDataField['fieldForHuman'], $rule);
					if($cek) {
						// set session error
						$_SESSION['RAPORT']['form_errors'][$arrDataField['realField']] = $cek;
						$return = true;
					} else {
						$return = false;
					}
				}

				// set session old value
				if(isset($_SESSION['RAPORT']['form_errors']) && $old_val==true) {
					foreach($param as $field=>$rule) {
						$arrDataField = $this->generate_realField_fieldForHuman($field);
						$data = filter_input(INPUT_POST, $arrDataField['realField'], FILTER_SANITIZE_STRING);
						$_SESSION['RAPORT']['old_val'][$arrDataField['realField']] = $data;
					}
				}

				return $return;
			}
		}

		public function set_delimiter($delimiterOpen=null, $delimiterClose=null) {
		    
		    if( isset($_SESSION['RAPORT']['form_errors']) && !empty(trim($delimiterOpen)) && !empty(trim($delimiterClose)) ) {

		    	foreach($_SESSION['RAPORT']['form_errors'] as $key=>$val) {
		    		$_SESSION['RAPORT']['form_errors'][$key] = $delimiterOpen.$val.$delimiterClose;
		    	}

		    	return true;
		    }
		    return false;
		}

		public function has_formErrors() {
		    if(isset($_SESSION['RAPORT']['form_errors'])) {
		    	return true;
		    } else {
		    	return false;
		    }
		}

		public function get_form_errors() {
		    if(isset($_SESSION['RAPORT']['form_errors'])) {
		    	$errors = $_SESSION['RAPORT']['form_errors'];
		    	unset($_SESSION['RAPORT']['form_errors']);
		    	return $errors;
		    }

		    return null;
		}

		public function get_old_value() {
		    if(isset($_SESSION['RAPORT']['old_val'])) {
		    	$old_val = $_SESSION['RAPORT']['old_val'];
		    	unset($_SESSION['RAPORT']['old_val']);
		    	return $old_val;
		    }

		    return null;
		}
	/* form validation */

	/* cek login */
		// jika user belum login
		private function cekLoginNo() {
		    if(!isset($_SESSION['RAPORT']['statusLogin'])) {
				return true;
			}
			return false;
		}

		public function cekLoginNo_methodGuru() {
			if($this->cekLoginNo()) {
				return true;
			} elseif(!$this->cekLoginNo() && $_SESSION['RAPORT']['level'] != "admin" && $_SESSION['RAPORT']['level'] != "guru") {
				return true;
			}
			return false;
		}

		public function cekLoginNo_methodAdmin() {
		    if($this->cekLoginNo()) {
		    	return true;
		    } elseif(!$this->cekLoginNo() && $_SESSION['RAPORT']['level'] != "admin") {
		    	return true;
		    }
		    return false;
		}

		public function cekLoginYes_forHalamanLogin() {
			if(!$this->cekLoginNo() && $_SESSION['RAPORT']['level'] == "admin") {
				header("Location: ".config::base_url('admin'));
				return true;
			} elseif(!$this->cekLoginNo() && $_SESSION['RAPORT']['level'] == "guru") {
				header("Location: ".config::base_url('guru'));
				return true;
			}
		}

		public function cekLoginYes_forHalamanLoginAdmin() {
		    if(!$this->cekLoginNo()) {
				return true;
			}
		}

		public function cekLoginNo_halamanAdmin() {
			if(!$this->cekLoginNo() && $_SESSION['RAPORT']['level'] != 'admin' && $_SESSION['RAPORT']['level']=="guru") {
				header("Location: ".config::base_url('guru'));
				return true;
			} elseif($this->cekLoginNo()) {
				header("Location: ".config::base_url('admin/login/login.php'));
				return true;
			}
		}

		public function cekLoginNo_halamanGuru() {
		    if($this->cekLoginNo()) {
				header("Location: ".config::base_url('index.php?harusLogin=yes'));
				return true;
			} elseif(!$this->cekLoginNo() && $_SESSION['RAPORT']['level'] != "admin" && $_SESSION['RAPORT']['level'] != "guru") {
				header("Location: ".config::base_url('home/proses.php?action=logout'));
				return true;
			}
		}
	/* cek login */

	/* cek tahun ajaran semester session kelas jurusan for admin akses halaman guru */
		public function cek_has_tahun_ajaran_semester_session_kelas_jurusan() {
		    if(!isset($_SESSION['RAPORT']['tahun_ajaran_id']) || !isset($_SESSION['RAPORT']['semester_id']) || !isset($_SESSION['RAPORT']['jurusan_id']) || !isset($_SESSION['RAPORT']['kelas_id'])) {
		    	if(isset($_SESSION['RAPORT']['statusLogin']) && $_SESSION['RAPORT']['level'] == "admin") {
		    		header("Location: ".config::base_url('admin/index.php'));
		    	} else {
		    		header("Location: ".config::base_url('index.php'));
		    	}
		    	return false;
		    }
		    return true;
		}
	/* cek tahun ajaran semester session kelas jurusan for admin akses halaman guru */
	
	/* token */
		public function generate_tokenCSRF() {
			if(!isset($_SESSION['RAPORT']['CSRF_token'])) {
				$_SESSION['RAPORT']['CSRF_token'] = bin2hex(random_bytes(32));
			}
			return $_SESSION['RAPORT']['CSRF_token'];
		}

		public function cek_CSRF_token() {
			$tokenInput = filter_input(INPUT_POST, 'tokenCSRF', FILTER_SANITIZE_STRING);
			if($tokenInput == null) { $tokenInput = filter_input(INPUT_GET, 'tokenCSRF', FILTER_SANITIZE_STRING); }

			if(isset($_SESSION['RAPORT']['CSRF_token']) && $tokenInput == $_SESSION['RAPORT']['CSRF_token']) {
		    	return true;
			} else {
		    	return false;
		    }
		}
	/* token */

	public static function page($default) {
	    $ref = filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_STRING);
		switch ($ref) {

		// cek target
		default:
			if(!file_exists($default)) die ("file kosong");
			include $default;
		break;
		case "pusat_bantuan":
			if(!file_exists("pusat_bantuan/pusat_bantuan.php")) die ("file kosong");
			include "pusat_bantuan/pusat_bantuan.php";
		break;
		case "bantuan_detail":
			if(!file_exists("pusat_bantuan/bantuan_detail.php")) die ("file kosong");
			include "pusat_bantuan/bantuan_detail.php";
		break;
		/* admin */
			//account
			case "setting_my_account":
				if(!file_exists("create_account/setting_my_account.php")) die ("file kosong");
				include "create_account/setting_my_account.php";
			break;
			// identitas sekolah
				case "identitas_sekolah":
				if(!file_exists("identitas_sekolah/identitas_sekolah.php")) die ("file kosong");
				include "identitas_sekolah/identitas_sekolah.php";
			break;
				case "add_identitas_sekolah":
				if(!file_exists("identitas_sekolah/add_identitas_sekolah.php")) die ("file kosong");
				include "identitas_sekolah/add_identitas_sekolah.php";
			break;
				case "edit_identitas_sekolah":
				if(!file_exists("identitas_sekolah/edit_identitas_sekolah.php")) die ("file kosong");
				include "identitas_sekolah/edit_identitas_sekolah.php";
			break;
			//jurusan
				case "jurusan":
				if(!file_exists("jurusan/jurusan.php")) die("file kosong");
				include "jurusan/jurusan.php";
			break;
				case "add_jurusan":
				if(!file_exists("jurusan/add_jurusan.php")) die("file kosong");
				include "jurusan/add_jurusan.php";
			break;
				case "edit_jurusan":
				if(!file_exists("jurusan/edit_jurusan.php")) die("file kosong");
				include "jurusan/edit_jurusan.php";
			break;
			//siswa detail
				case "siswa_detail":
				if(!file_exists("siswa_detail/siswa_detail.php")) die ("file kosong");
				include "siswa_detail/siswa_detail.php";
			break;
				case "add_siswa_detail":
				if(!file_exists("siswa_detail/add_siswa_detail.php")) die ("file kosong");
				include "siswa_detail/add_siswa_detail.php";
			break;
				case "edit_siswa_detail":
				if(!file_exists("siswa_detail/edit_siswa_detail.php")) die ("file kosong");
				include "siswa_detail/edit_siswa_detail.php";
			break;
			//mapel
			case "mapel":
				if(!file_exists("mapel/mapel.php")) die ("file kosong");
				include 'mapel/mapel.php';
			break;
				case "add_mapel":
				if(!file_exists("mapel/add_mapel.php")) die ("file kosong");
				include "mapel/add_mapel.php";
			break;
				case "edit_mapel":
				if(!file_exists("mapel/edit_mapel.php")) die ("file kosong");
				include "mapel/edit_mapel.php";
			break;
			//kkm
			case "kkm":
				if(!file_exists("kkm/kkm.php")) die ("file kosong");
				include "kkm/kkm.php";
			break;
			case "add_kkm":
				if(!file_exists("kkm/add_kkm.php")) die ("file kosong");
				include "kkm/add_kkm.php";
			break;
			case 'edit_kkm':
				if(!file_exists("kkm/edit_kkm.php")) die ("file kosong");
				include "kkm/edit_kkm.php";
			break;
			// kelas
			case "kelas":
				if(!file_exists("kelas/kelas.php"))  die ("file kosong");
				include "kelas/kelas.php";
			break;
			case "add_kelas":
				if(!file_exists("kelas/add_kelas.php")) die ("file kosong");
				include "kelas/add_kelas.php";
			break;
			case "edit_kelas":
				if(!file_exists("kelas/edit_kelas.php")) die ("file kosong");
				include "kelas/edit_kelas.php";
			break;
			// wali kelas
			case "wali_kelas":
				if(!file_exists("wali_kelas/wali_kelas.php")) die ("file kosong");
				include "wali_kelas/wali_kelas.php";
			break;
			case "add_wali_kelas":
				if(!file_exists("wali_kelas/add_wali_kelas.php")) die ("file kosong");
				include "wali_kelas/add_wali_kelas.php";
			break;
			case "edit_wali_kelas":
				if(!file_exists("wali_kelas/edit_wali_kelas.php")) die ("file kosong");
				include "wali_kelas/edit_wali_kelas.php";
			break;
			// tahun ajaran
			case "tahun_ajaran":
				if(!file_exists("tahun_ajaran/tahun_ajaran.php")) die ("file kosong");
				include("tahun_ajaran/tahun_ajaran.php");
			break;
			case "add_tahun_ajaran":
				if(!file_exists("tahun_ajaran/add_tahun_ajaran.php")) die ("add_tahun_ajaran");
				include "tahun_ajaran/add_tahun_ajaran.php";
			break;
			// semester
			case "semester":
				if(!file_exists("semester/semester.php")) die ("file kosong");
				include "semester/semester.php";
			break;
			// data siswa lulus
			case "siswa_lulus":
				if(!file_exists("siswa_lulus/siswa_lulus.php")) die ("file kosong");
				include "siswa_lulus/siswa_lulus.php";
			break;
			case "edit_siswa_lulus":
				if(!file_exists("siswa_lulus/edit_siswa_lulus.php")) die ("file kosong");
				include "siswa_lulus/edit_siswa_lulus.php";
			break;
			// juara umum
			case "juara_umum":
				if(!file_exists("juara_umum/juara_umum.php")) die ("file kosong");
				include "juara_umum/juara_umum.php";
			break;
			// izin kenaikan kelas
			case 'izin_kenaikan_kelas':
				if(!file_exists("izin_kenaikan_kelas/izin_kenaikan_kelas.php")) die ("file kosong");
				include "izin_kenaikan_kelas/izin_kenaikan_kelas.php";
			break;
			// siswa keluar
			case 'siswa_keluar':
				if(!file_exists("siswa_keluar/siswa_keluar.php")) die ("file kosong");
				include "siswa_keluar/siswa_keluar.php";
			break;
			case 'edit_siswa_keluar':
				if(!file_exists("siswa_keluar/edit_siswa_keluar.php")) die ("file kosong");
				include "siswa_keluar/edit_siswa_keluar.php";
			break;
			// user admin
			case 'user_admin':
				if(!file_exists("user_admin/user_admin.php")) die ("file kosong");
				include "user_admin/user_admin.php";
			break;
		/* admin */

		/* guru */
			//data siswa perjurusan
			case "data_siswa_perjurusan":
				if(!file_exists("data_siswa_perjurusan/data_siswa_perjurusan.php")) die ("file kosong");
				include "data_siswa_perjurusan/data_siswa_perjurusan.php";
			break;
			//raport
			case "raport_siswa":
				if(!file_exists("raport_siswa/raport_siswa.php")) die ("file kosong");
				include 'raport_siswa/raport_siswa.php';
			break;
			// sikap
			case "add_sikap":
				if(!file_exists("raport_siswa/add_sikap.php")) die ("file kosong");
				include "raport_siswa/add_sikap.php";
			break;
			case "edit_sikap":
				if(!file_exists("raport_siswa/edit_sikap.php")) die ("file kosong");
				include "raport_siswa/edit_sikap.php";
			break;
			// deskripsi
			case "add_nilai_deskripsi":
				if(!file_exists("raport_siswa/add_nilai_deskripsi.php")) die ("file kosong");
				include "raport_siswa/add_nilai_deskripsi.php";
			break;
			case "edit_nilai_deskripsi":
				if(!file_exists("raport_siswa/edit_nilai_deskripsi.php")) die ("file kosong");
				include "raport_siswa/edit_nilai_deskripsi.php";
			break;
			// ekstrakurikuler
			case "add_ekstrakurikuler":
				if(!file_exists("raport_siswa/add_ekstrakurikuler.php")) die ("file kosong");
				include "raport_siswa/add_ekstrakurikuler.php";
			break;
			// prestasi
			case "add_prestasi":
				if(!file_exists("raport_siswa/add_prestasi.php")) die ("file kosong");
				include "raport_siswa/add_prestasi.php";
			break;
			// ketidakhadiran
			case "add_ketidakhadiran":
				if(!file_exists("raport_siswa/add_ketidakhadiran.php")) die ("file kosong");
				include "raport_siswa/add_ketidakhadiran.php";
			break;
			case "edit_ketidakhadiran":
				if(!file_exists("raport_siswa/edit_ketidakhadiran.php")) die ("file kosong");
				include "raport_siswa/edit_ketidakhadiran.php";
			break;
			// catatan wali kelas
			case "add_catatan_wali_kelas":
				if(!file_exists("raport_siswa/add_catatan_wali_kelas.php")) die ("file kosong");
				include "raport_siswa/add_catatan_wali_kelas.php";
			break;
			case "edit_catatan_wali_kelas":
				if(!file_exists("raport_siswa/edit_catatan_wali_kelas.php")) die ("file kosong");
				include "raport_siswa/edit_catatan_wali_kelas.php";
			break;
			// juara kelas
			case "juara_kelas":
				if(!file_exists("juara/juara.php")) die ("file kosong");
				include "juara/juara.php";
			break;
			// status akhir semester
			case "add_status_akhir_semester":
				if(!file_exists("raport_siswa/add_status_akhir_semester.php")) die ("file kosong");
				include "raport_siswa/add_status_akhir_semester.php";
			break;
			case "edit_status_akhir_semester":
				if(!file_exists("raport_siswa/edit_status_akhir_semester.php")) die ("file kosong");
				include "raport_siswa/edit_status_akhir_semester.php";
			break;
			// praktik kerja industri
			case "add_praktik_kerja_industri":
				if(!file_exists("raport_siswa/add_praktik_kerja_industri.php")) die ("file kosong");
				include "raport_siswa/add_praktik_kerja_industri.php";
			break;
		/* guru */
		}
	}
}
