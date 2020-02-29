<?php  if(!class_exists("config")) { die; }

	$db = new jurusan;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$errors = $db->get_form_errors();
	$jurusan_id = filter_input(INPUT_GET, 'jurusan_id', FILTER_SANITIZE_STRING);
	$r = $db->get_one_jurusan($jurusan_id);
?>
<div class="col-6 offset-right-3 offset-left-3">
	<div class="home jurusan">
		<h1 class="judul marginBottom20px">Edit Jurusan</h1>
		<form id="form" method="post" action="<?= config::base_url('admin/jurusan/proses.php?action=edit_jurusan'); ?>">
			<?= $db->pesan_edit_jurusan(); ?>
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="jurusan_id" value="<?= $r['jurusan_id']??''; ?>">
			<label class="label">Nama jurusan</label>
			<?= $errors['nama_jurusan']??''; ?>
			<input type="text" name="nama_jurusan" placeholder="..." value="<?= $r['nama_jurusan']??''; ?>">

			<a href="<?= config::base_url('admin/index.php?ref=jurusan'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>