<?php  
	include '../init.php';
	$pp = filter_input(INPUT_GET, 'pp', FILTER_SANITIZE_STRING);
?>
<!DOCTYPE html>
<html lang="en-ID">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MANPORT Panduan Penggunaan</title>
	<?php include '../header.php'; ?>
</head>
<body class="bgSilver">
<div class="container-big">
	<ul class="menuheader">
		<div class="container">
			<li><a href="index.php" class="benner"><span class="fa fa-home ico"></span> MANPORT</a></li>
		</div>
	</ul>

	<div class="container">
		<?php
			switch ($pp) {
				default:
					if(!file_exists("panduan_penggunaan.php")) die ("file kosong");
					include "panduan_penggunaan.php";
				break;
				case 'assalamualaikum':
					if(!file_exists("assalamualaikum.php")) die ("file kosong");
					include "assalamualaikum.php";
				break;
				case 'admin':
					if(!file_exists("admin.php")) die ("file kosong");
					include "admin.php";
				break;
				case 'identitas_sekolah':
					if(!file_exists("identitas_sekolah.php")) die ("file kosong");
					include "identitas_sekolah.php";
				break;
				case 'jurusan':
					if(!file_exists("jurusan.php")) die ("file kosong");
					include "jurusan.php";
				break;
				case 'kelas':
					if(!file_exists("kelas.php")) die ("file kosong");
					include "kelas.php";
				break;
				case 'tahun_ajaran':
					if(!file_exists("tahun_ajaran.php")) die ("file kosong");
					include "tahun_ajaran.php";
				break;
				case 'semester':
					if(!file_exists("semester.php")) die ("file kosong");
					include "semester.php";
				break;
				case 'data_siswa_detail':
					if(!file_exists("data_siswa_detail.php")) die ("file kosong");
					include "data_siswa_detail.php";
				break;
				case 'kkm':
					if(!file_exists("kkm.php")) die ("file kosong");
					include "kkm.php";
				break;
				case 'mata_pelajaran':
					if(!file_exists("mata_pelajaran.php")) die ("file kosong");
					include "mata_pelajaran.php";
				break;
				case 'wali_kelas':
					if(!file_exists("wali_kelas.php")) die ("file kosong");
					include "wali_kelas.php";
				break;
				case 'data_siswa_lulus':
					if(!file_exists("data_siswa_lulus.php")) die ("file kosong");
					include "data_siswa_lulus.php";
				break;
				case 'data_siswa_keluar':
					if(!file_exists("data_siswa_keluar.php")) die ("file kosong");
					include "data_siswa_keluar.php";
				break;
				case 'izin_kenaikan_kelas':
					if(!file_exists("izin_kenaikan_kelas.php")) die ("file kosong");
					include "izin_kenaikan_kelas.php";
				break;
				case 'juara_umum':
					if(!file_exists("juara_umum.php")) die ("file kosong");
					include "juara_umum.php";
				break;
				case 'admin_akses_halaman_raport_siswa':
					if(!file_exists("admin_akses_halaman_raport_siswa.php")) die ("file kosong");
					include "admin_akses_halaman_raport_siswa.php";
				break;
				case 'cadangan_data':
					if(!file_exists("cadangan_data.php")) die ("file kosong");
					include "cadangan_data.php";
				break;
				case 'home_guru_wali_kelas':
					if(!file_exists("home_guru_wali_kelas.php")) die ("file kosong");
					include "home_guru_wali_kelas.php";
				break;
				case 'raport_siswa':
					if(!file_exists("raport_siswa.php")) die ("file kosong");
					include "raport_siswa.php";
				break;
			}
		?>
	</div>
</div>

	<script type="text/javascript" src="<?= config::base_url('assets/js/action/dropdown.js'); ?>"></script>
	<script type="text/javascript">
	$(function(){
		$("a#btnMenuPusatBantuan").click(function(){
			$("ul.menuPusatBantuan").toggleClass("muncul");
			$("div.menuPusatBantuan_bg").toggleClass("muncul");
		})
	})
	</script>
</body>
</html>