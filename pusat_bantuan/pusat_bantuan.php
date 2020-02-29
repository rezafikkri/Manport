<?php  if(!class_exists("config")) { die; }

	$db = new bantuan;
	if($db->cekLoginNo_halamanGuru() === true) die;

	$pusat_bantuan = $db->tampil_pusat_bantuan();
?>
<div class="col-8 offset-right-2 offset-left-2 marginBottom100px">
	<div class="home default no_box_shadow pusat_bantuan">
		<h1 class="judul marginBottom20px">PUSAT BANTUAN</h1>
		<ul>
			<?php if($pusat_bantuan) : foreach($pusat_bantuan as $r) : ?>
			<?php if($_SESSION['RAPORT']['level']=="admin") : ?>
			<li><a href="index.php?ref=bantuan_detail&pusat_bantuan_id=<?= $r['pusat_bantuan_id']; ?>"><?= str_replace("_", " ", $r['nama_bantuan']); ?></a></li>
			<?php elseif($_SESSION['RAPORT']['level']=="guru" && $r['for_to'] == "admin_guru") : ?>
			<li><a href="index.php?ref=bantuan_detail&pusat_bantuan_id=<?= $r['pusat_bantuan_id']; ?>"><?= str_replace("_", " ", $r['nama_bantuan']); ?></a></li>
			<?php endif; ?>
			<?php endforeach; endif; ?>
		</ul>
	</div>
</div>
