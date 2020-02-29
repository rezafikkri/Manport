<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbJK = new juara_kelas;
	$dbS = new siswa;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$errors = $db->get_form_errors();
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default">
		<h1 class="judul marginBottom20px">Tambah catatan wali kelas <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form" action="raport_siswa/proses.php?action=add_edit_catatan_wali_kelas" method="post">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<?= $db->pesan_add_catatan_wali_kelas(); ?>
			<label class="label">Catatan wali kelas</label>
			<?= $errors['catatan']??''; ?>
			<textarea spellcheck="false" placeholder="..." rows="5" name="catatan">Juara ke <?= $dbJK->get_one_juara_kelas($siswa_detail_id)['juara']??''; ?></textarea>

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>