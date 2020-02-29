<?php 
	include "../init.php"; 
	$dataJurusanIndex = new jurusan();
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
			<?php  
				$dataJurusanIndex = $dataJurusanIndex->tampil_jurusan();
			?>
			<li><a href="index.php" class="benner"><span class="fa fa-home ico"></span> MANPORT</a></li>

			<li class="right button_drop_mobile">
				<a href="dropdownManual" target-menu="dropdown_mobile">
					<div class="menu_humberger"></div>
				</a>
			</li>

			<ul class="dropdown_mobile">
				<li><a href="dropdownAuto">JURUSAN</a>
					<!-- menu dropdown jurusan -->
					<ul class="drop_menu menu_jurusan" target-menu="auto">
						<div class="container">
						<?php  
							if($dataJurusanIndex) :
							$jmlJurusanIndex = ceil(count($dataJurusanIndex)/2);
							if($jmlJurusanIndex > 1) :
						?>
						<div class="col-6">
						<?php else: ?>
						<div class="col-12">
						<?php
							endif;
							$no= 1;
							foreach($dataJurusanIndex as $j) :
						?>
							<li id="<?= $j['jurusan_id']; ?>"><a href="dropdownAuto"><?= $j['nama_jurusan']; ?></a>
								<ul target-menu="auto" class="kelas <?= $j['jurusan_id']; ?>menuheader">
								<?php 
									$dataKelasIndex = new kelas();
									$dataKelasIndex = $dataKelasIndex->tampil_kelas($j['jurusan_id']);
									if($dataKelasIndex) :
									foreach($dataKelasIndex as $k) :
								?>
									<li><a href="<?= config::base_url('guru').'/index.php?jurusan_id='.$j['jurusan_id'].'&kelas_id='.$k['kelas_id'] ?>"><?= $k['kelas']; ?></a></li>

								<?php endforeach; endif; ?>
								</ul>
							</li>
						<?php if($jmlJurusanIndex > 1 && $no == $jmlJurusanIndex) : ?>
						</div><div class="col-6">
						<?php endif; $no++;endforeach;endif; ?>
						</div>
					</ul>
					<!-- menu dropdown jurusan end -->
				</li>
				<li><a href="index.php?ref=juara_umum">JUARA UMUM</a></li>

				<li class="right sign-out"><a title="Log Out" href="<?= config::base_url('admin/login/logout.php'); ?>"><span class="fa fa-sign-out ico"></span></a></li>
				<li class="right"><a target="_blank" title="Pusat Bantuan" href="<?= config::base_url('index.php?ref=pusat_bantuan'); ?>"><span class="fa fa-question-circle-o ico"></span></a></li>
				<li class="right"><a title="Buat Cadangan Database" target="_blank" href="<?= config::base_url('admin/backup_data/backup_data.php'); ?>"><span class="fa fa-cloud-download ico"></span></a></li>
				<li class="right"><a title="Pengaturan" href="dropdownAuto"><span class="fa fa-gear ico"></span></a>
					<!-- menu dropdown setting -->
					<ul class="drop_menu setting" target-menu="auto">
						<div class="container">
						<div class="col-6">
							<li><a href="index.php?ref=identitas_sekolah">Identitas sekolah</a></li>
							<li><a href="index.php?ref=jurusan">Jurusan</a></li>
							<li><a href="index.php?ref=kelas">Kelas</a></li>
							<li><a href="index.php?ref=tahun_ajaran">Tahun ajaran</a></li>
						  	<li><a href="index.php?ref=semester">Semester</a></li>
							<li><a href="index.php?ref=siswa_detail">Data siswa detail</a></li>
							<li><a href="index.php?ref=kkm">KKM</a></li>
						</div>
					  	<div class="col-6">
							<li><a href="index.php?ref=mapel">Mata pelajaran</a></li>
							<li><a href="index.php?ref=wali_kelas">Wali kelas</a></li>
							<li><a href="index.php?ref=siswa_lulus">Data siswa lulus</a></li>
							<li><a href="index.php?ref=siswa_keluar">Data siswa keluar</a></li>
							<li><a href="index.php?ref=izin_kenaikan_kelas">Izin kenaikan kelas</a></li>
							<li><a href="index.php?ref=user_admin">Admin</a></li>
					  	</div>
						</div>
					</ul>
					<!-- menu dropdown setting end -->
				</li>
			</ul><!-- dropdown mobile -->

		</div>
	</ul><!-- menuheader -->

	<!-- progress bar -->
	<div class="progress_bar_back">
		<div id="mybar" class="progress_bar green default"></div>
	</div>

	<div class="container content">
	<?php 
		config::page("homeAdmin/homeAdmin.php");
	?>
	</div>

</div><!-- container-big -->

<?php if(!isset($_SESSION['RAPORT']['tahun_ajaran_id']) || !isset($_SESSION['RAPORT']['semester_id'])) : ?>
<div class="alert">
	<?php 
		if(!isset($_SESSION['RAPORT']['tahun_ajaran_id']) && !isset($_SESSION['RAPORT']['semester_id'])) {
			$pesan = "Tahun ajaran dan Semester";
		} elseif(!isset($_SESSION['RAPORT']['tahun_ajaran_id'])) {
			$pesan = "Tahun ajaran";
		} elseif(!isset($_SESSION['RAPORT']['semester_id'])) {
			$pesan = "Semester";
		}
	?>
	<p><?= $pesan; ?> belum dipilih!</p>

	<a id="closeAlert"><span class="fa fa-remove"></span></a>
</div>
<?php endif; ?>

<script type="text/javascript" src="<?= config::base_url('assets/js/action/dropdown.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	$("a#closeAlert").click(function(){
		$("div.alert").css({"display":"none"});
	})
})
</script>

</body>
</html>