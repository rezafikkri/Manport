<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbS = new siswa;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$errors = $db->get_form_errors();
	$r = $db->tampil_ketidakhadiran($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default">
		<h1 class="judul marginBottom20px">Edit ketidakhadiran <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form" action="raport_siswa/proses.php?action=add_edit_ketidakhadiran" method="post">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<?= $db->pesan_edit_ketidakhadiran(); ?>
			<label class="label">Sakit</label>
			<?= $errors['sakit']??''; ?>
			<input type="text" name="sakit" placeholder="..." value="<?= $r['sakit']??''; ?>">
			<label class="label">Izin</label>
			<?= $errors['izin']??''; ?>
			<input type="text" name="izin" placeholder="..." value="<?= $r['izin']??''; ?>">
			<label class="label">Tanpa keterangan</label>
			<?= $errors['tanpa_keterangan']??''; ?>
			<input type="text" name="tanpa_keterangan" placeholder="..." value="<?= $r['tanpa_keterangan']??''; ?>">
			<label class="label">Bolos</label>
			<?= $errors['bolos']??''; ?>
			<input type="text" name="bolos" placeholder="..." value="<?= $r['bolos']??''; ?>">

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>