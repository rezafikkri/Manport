<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbS = new siswa;

	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$errors = $db->get_form_errors();
?>
<div class="col-8 offset-left-2 offset-right-2">
	<div class="home default marginBottom100px cf">
		<h1 class="judul marginBottom20px">Edit Nilai Deskripsi <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form" action="raport_siswa/proses.php?action=add_edit_nilai_deskripsi" method="post">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<?= $db->pesan_edit_nilai_deskripsi(); ?>
			<?php  
				$nilai_deskripsi = $db->tampil_nilai($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id'], "mp.mata_pelajaran_id, mp.nama_mapel, n.nilai_k, n.deskripsi_k, n.nilai_p, n.deskripsi_p");
				if($nilai_deskripsi) :
				$no = 1;
				foreach($nilai_deskripsi as $n) :
				if($no > 1) :
			?>
			<hr>
			<?php endif; ?>
			<label class="label marginTop10px">Nilai pengetahuan <?= $n['nama_mapel']; ?></label>
			<?= $errors['nilai_pengetahuan_'.$n['mata_pelajaran_id']]??''; ?>
			<input type="text" name="nilai_pengetahuan_<?= $n['mata_pelajaran_id']; ?>" placeholder="..." value="<?= str_replace(".", ",", ($n['nilai_p']??'')); ?>">

			<label class="label">Deskripsi pengetahuan <?= $n['nama_mapel']; ?></label>
			<?= $errors['deskripsi_pengetahuan_'.$n['mata_pelajaran_id']]??''; ?>
			<textarea spellcheck="false" rows="4" name="deskripsi_pengetahuan_<?= $n['mata_pelajaran_id']; ?>" placeholder="..."><?= $n['deskripsi_p']??''; ?></textarea>

			<label class="label">Nilai keterampilan <?= $n['nama_mapel']; ?></label>
			<?= $errors['nilai_keterampilan_'.$n['mata_pelajaran_id']]??''; ?>
			<input type="text" name="nilai_keterampilan_<?= $n['mata_pelajaran_id']; ?>" placeholder="..." value="<?= str_replace(".", ",", ($n['nilai_k']??'')); ?>">

			<label class="label">Deskripsi keterampilan <?= $n['nama_mapel']; ?></label>
			<?= $errors['deskripsi_keterampilan_'.$n['mata_pelajaran_id']]??''; ?>
			<textarea spellcheck="false" rows="4" name="deskripsi_keterampilan_<?= $n['mata_pelajaran_id']; ?>" placeholder="..."><?= $n['deskripsi_k']??''; ?></textarea>
			<?php $no++; endforeach; endif; ?>

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>