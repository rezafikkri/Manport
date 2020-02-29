<?php  if(!class_exists("config")) { die; }

	$db = new kkm;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$kkm_id = filter_input(INPUT_GET, 'kkm_id', FILTER_SANITIZE_STRING);
	$r = $db->get_one_kkm($kkm_id);
	$errors = $db->get_form_errors();

?>
<div class="col-4 offset-left-4 offset-right-4 marginBottom100px">
	<div class="home default marginBottom100px cf">
		<h1 class="judul marginBottom20px">Edit Kkm</h1>
		<form id="form" action="kkm/proses.php?action=edit_kkm" method="POST">
			<?= $db->pesan_edit_kkm(); ?>
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="kkm_id" value="<?= $kkm_id; ?>">

			<!-- kkm -->
			<label class="label">Kkm</label>
			<?= $errors['kkm']??''; ?>
			<input type="text" name="kkm" placeholder="72 ..." value="<?= $r['kkm']??''; ?>">
			<!-- kurang -->
			<label class="label">Kurang</label>
			<?= $errors['kurang']??''; ?>
			<input type="text" name="kurang" placeholder="0-71 ..." value="<?= $r['predikat_d']??''; ?>">
			<!-- cukup -->
			<label class="label">Cukup</label>
			<?= $errors['cukup']??''; ?>
			<input type="text" name="cukup" placeholder="71-83 ..." value="<?= $r['predikat_c']??''; ?>">
			<!-- baik -->
			<label class="label">Baik</label>
			<?= $errors['baik']??''; ?>
			<input type="text" name="baik" placeholder="83-92 ..." value="<?= $r['predikat_b']??''; ?>">
			<!-- sangat baik -->
			<label class="label">Sangat Baik</label>
			<?= $errors['sangat_baik']??''; ?>
			<input type="text" name="sangat_baik" placeholder="92-100 ..." value="<?= $r['predikat_a']??''; ?>">

			<a href="index.php?ref=kkm" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>