<?php
	include "init.php";
	$data_pusat_bantuan = new bantuan; 
?>
<!DOCTYPE html>
<html lang="en-ID">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Management Raport</title>
	<?php include 'header.php'; ?>
</head>
<body class="<?php if(preg_match("/bantuan/", ($_GET['ref']??''))) : ?> bgSilver <?php else: ?> bgGreen <?php endif; ?> noOverflowX">
	<?php if(preg_match("/bantuan/", ($_GET['ref']??''))) : ?>
	<ul class="menuheader">
		<div class="container">
			<?php if(isset($_GET['ref']) && $_GET['ref'] != "pusat_bantuan") : ?>
			<li><a id="btnMenuPusatBantuan"><div class="menu_humberger"></div></a></li>
			<?php endif; ?>
			<li><a href="<?php if($_SESSION['RAPORT']['level'] == "admin") { echo config::base_url('admin/index.php'); } elseif($_SESSION['RAPORT']['level'] == "guru") {echo config::base_url('guru/index.php');} ?>" class="benner"><span class="fa fa-home ico"></span> MANPORT</a></li>
			<?php if($_SESSION['RAPORT']['level'] == "admin") : ?>
			<li class="right sign-out"><a href="<?= config::base_url('admin/login/logout.php'); ?>"><span class="fa fa-sign-out ico"></span></a></li>
			<?php elseif($_SESSION['RAPORT']['level'] == "guru") : ?>
			<li class="right sign-out"><a href="<?= config::base_url('home/proses.php?action=logout'); ?>"><span class="fa fa-sign-out ico"></span></a></li>
			<?php endif; ?>
		</div>
	</ul>
	<?php endif; ?>

	<ul class="menuPusatBantuan">
		<li><a href="index.php?ref=pusat_bantuan">Bantuan</a></li>
		<hr>
	<?php 
		$data_pusat_bantuan = $data_pusat_bantuan->tampil_pusat_bantuan();
		if($data_pusat_bantuan) :
		foreach($data_pusat_bantuan as $pb) :
		if($pb['pusat_bantuan_id'] != ($_GET['pusat_bantuan_id']??'')) :
	?>
		<li><a href="index.php?ref=bantuan_detail&pusat_bantuan_id=<?= $pb['pusat_bantuan_id']; ?>"><?= str_replace("_", " ", $pb['nama_bantuan']); ?></a></li>
	<?php endif; endforeach; endif; ?>
	</ul>
	<div class="menuPusatBantuan_bg"></div>

	<!-- progress bar -->
	<div class="progress_bar_back <?php if(!isset($_GET['ref'])) echo "login"; ?>">
		<div class="progress_bar green default"></div>
	</div>

	<div class="container">
	<?php
		config::page("home/home.php");
	?>
	</div>

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
