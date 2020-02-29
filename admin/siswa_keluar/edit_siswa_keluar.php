<?php  if(!class_exists("config")) { die; }

	$db = new siswa;
	if($db->cekLoginNo_halamanAdmin() === true) die;
	  
	$dbJ = new jurusan;
	$dbK = new kelas;

	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$data_siswa = $db->get_one_siswa_detail($siswa_detail_id, 'keluar', 'sd.nama_siswa, sd.status');
	$errors = $db->get_form_errors();
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default">
		<h1 class="judul marginBottom20px">Edit status siswa <span><?= $data_siswa['nama_siswa']??''; ?></span></h1>
		<form id="form" method="post" action="siswa_keluar/proses.php?action=edit_siswa_keluar">
			<input type="hidden" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">

			<?= $db->pesan_edit_siswa_detail(); ?>
		<?php if($data_siswa) : ?>
			<label class="label">Status</label>
			<?= $errors['status']??''; ?>
			<select name="status">
				<option disabled="" selected="">...</option>
				<?php  
					$status = ['keluar','masih_sekolah'];
					foreach($status as $s) :
				?>
				<option value="<?= $s; ?>"
				<?= $s==($data_siswa['status']??'')?'selected':''; ?>
				><?= strtoupper(str_replace("_", " ", $s)); ?></option>
				<?php endforeach; ?>
			</select>
			<label class="label">Nama siswa</label>
			<?= $errors['nama_siswa']??''; ?>
			<input type="text" name="nama_siswa" placeholder="..." value="<?= $data_siswa['nama_siswa']; ?>">
		<?php endif; ?>
			<a href="<?= config::base_url('admin/index.php?ref=siswa_keluar'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
		<?php if($data_siswa) : ?>
			<button class="button green" type="submit"><span class="fa fa-send"></span> Simpan</button>
		<?php endif; ?>
		</form>
	</div>
</div>