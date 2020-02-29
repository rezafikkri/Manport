<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbM = new mapel;
	$dbS = new siswa;

	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$errors = $db->get_form_errors();
	$old = $db->get_old_value();
?>
<div class="col-8 offset-left-2 offset-right-2">
	<div class="home default marginBottom100px cf">
		<h1 class="judul marginBottom20px">Tambah Nilai Deskripsi <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form" action="raport_siswa/proses.php?action=add_edit_nilai_deskripsi" method="post">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<?= $db->pesan_add_nilai_deskripsi(); ?>
			<?php  
				$mapel = $dbM->tampil_mapel($_SESSION['RAPORT']['kelas_id'], "mp.mata_pelajaran_id, mp.nama_mapel");
				if($mapel) :
				$no = 1;
				foreach($mapel as $m) :
				if($no > 1) :
			?>
			<hr>
			<?php endif; ?>
			<label class="label marginTop10px">Nilai pengetahuan <?= $m['nama_mapel']; ?></label>
			<?= $errors['nilai_pengetahuan_'.$m['mata_pelajaran_id']]??''; ?>
			<input type="text" name="nilai_pengetahuan_<?= $m['mata_pelajaran_id']; ?>" placeholder="..." value="<?= $old['nilai_pengetahuan_'.$m['mata_pelajaran_id']]??''; ?>">
			<label class="label">Deskrpsi pengetahuan <?= $m['nama_mapel']; ?></label>
			<?= $errors['deskripsi_pengetahuan_'.$m['mata_pelajaran_id']]??''; ?>
			<textarea spellcheck="false" rows="4" name="deskripsi_pengetahuan_<?= $m['mata_pelajaran_id']; ?>" placeholder="..."><?= $old['deskripsi_pengetahuan_'.$m['mata_pelajaran_id']]??''; ?></textarea>

			<label class="label">Nilai keterampilan <?= $m['nama_mapel']; ?></label>
			<?= $errors['nilai_keterampilan_'.$m['mata_pelajaran_id']]??''; ?>
			<input type="text" name="nilai_keterampilan_<?= $m['mata_pelajaran_id']; ?>" placeholder="..." value="<?= $old['nilai_keterampilan_'.$m['mata_pelajaran_id']]??''; ?>">

			<label class="label">Deskrpsi keterampilan <?= $m['nama_mapel']; ?></label>
			<?= $errors['deskripsi_keterampilan_'.$m['mata_pelajaran_id']]??''; ?>
			<textarea spellcheck="false" rows="4" name="deskripsi_keterampilan_<?= $m['mata_pelajaran_id']; ?>" placeholder="..."><?= $old['deskripsi_keterampilan_'.$m['mata_pelajaran_id']]??''; ?></textarea>
			<?php $no++; endforeach; endif; ?>

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>