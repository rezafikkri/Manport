<?php 
	include "../init.php";
	$dbWKIndex = new wali_kelas;
	$dbKIndex = new kelas;
	$dbJIndex = new jurusan;
	$dbLIndex = new login;
	$dbLIndex->make_session_kelas_jurusan($dbWKIndex, $dbJIndex, $dbKIndex);
?>
<!DOCTYPE html>
<html lang="en-ID">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Management Raport</title>
	<?php include '../header.php'; ?>
</head>
<body class="bgSilver">

<div class="container-big">
	
	<ul class="menuheader">
		<div class="container">
			<li><a id="btn_drop_menu_siswa"><div class="menu_humberger"></div></a></li>

			<li><a href="index.php" class="benner"><span class="fa fa-home ico"></span> MANPORT</a></li>

			<li class="right sign-out"><a href="<?= config::base_url('home/proses.php?action=logout'); ?>"><span class="fa fa-sign-out ico"></span></a></li>
			<li class="right"><a target="_blank" title="Pusat Bantuan" href="<?= config::base_url('index.php?ref=pusat_bantuan'); ?>"><span class="fa fa-question-circle-o ico"></span></a></li>

		</div>
	</ul><!-- menuheader -->

	<!-- progress bar -->
	<div class="progress_bar_back">
		<div id="mybar" class="progress_bar green default"></div>
	</div>

	<!-- menu siswa -->
	<ul class="menu_siswa <?php if(!isset($_GET['ref'])) echo "muncul"; ?>">
		<li class="nama_siswa"><a href="index.php">Nama Siswa</a></li>
		<hr>
		<?php
			$dataSiswaIndex = new siswa;
			$dataSiswaIndex = $dataSiswaIndex->tampil_siswa_detail($_SESSION['RAPORT']['kelas_id'],'masih_sekolah'); 
			if($dataSiswaIndex) :
			foreach($dataSiswaIndex as $s) :
			if(filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING)==$s['siswa_detail_id']) :
		?>
		<li class="daftar_siswa"><a class="active" href="index.php?ref=raport_siswa&siswa_detail_id=<?= $s['siswa_detail_id']; ?>"><?= $s['nama_siswa']; ?></a></li>
		<?php else : ?>
		<li class="daftar_siswa"><a href="index.php?ref=raport_siswa&siswa_detail_id=<?= $s['siswa_detail_id']; ?>"><?= $s['nama_siswa']; ?></a></li>
		<?php endif; endforeach;endif; ?>
	</ul>
	<!-- menu siswa end -->

	<div class="container content">
	<?php 
		config::page("homeGuru/homeGuru.php");
	?>
	</div>

</div><!-- container-big -->

<script type="text/javascript" src="<?= config::base_url('assets/js/action/dropdown.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	$("li a#btn_drop_menu_siswa").click(function(){
		if(document.querySelector('body').clientWidth <= 991) {
			$("ul.menu_siswa").toggleClass("md_muncul");
		} else {
			$("ul.menu_siswa").toggleClass("muncul");
		}
	})
})
</script>
</body>
</html>