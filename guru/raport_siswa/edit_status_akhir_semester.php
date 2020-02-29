<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbS = new siswa;
	$dbIS = new identitas_sekolah;
	$dbK =  new kelas;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);

	// generate where
	$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
	$where = $db->generate_where($lama_belajar);
	if($where != null) {
		$kelas = $dbK->tampil_kelas($_SESSION['RAPORT']['jurusan_id'], $where, "kelas_id, kelas");
	}
	$errors = $db->get_form_errors();
	$status_akhir = $db->get_one_status_akhir_semester($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id'])['status_akhir']??'';
	if($status_akhir) {
		$no_un = $dbS->get_one_siswa_detail($siswa_detail_id, 'masih_sekolah', 'no_un', $_SESSION['RAPORT']['kelas_id'])['no_un']??'';
	} else {
		$no_un = null;
	}
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default">
		<h1 class="judul marginBottom20px">Edit status akhir semester <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form" action="raport_siswa/proses.php?action=add_edit_status_akhir_semester" method="post">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<?= $db->pesan_edit_status_akhir_semester(); ?>
			<label class="label">Status akhir semester</label>
			<?= $errors['status_akhir_semester']??''; ?>
			<select name="status_akhir_semester">
				<option disabled="" selected="">...</option>
				<?php
					// jika lama belajar 3 tahun dan kelas >= XII atau lama belajar 4 tahun dan kelas >= XIII
					if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) :
						$arrStatus = ['lulus','tidak_lulus'];
						foreach($arrStatus as $s) :
				?>
				<option value="<?= $s; ?>"
				<?= $s==$status_akhir?'selected':''; ?>
				><?= strtoupper(str_replace("_", " ", $s)); ?></option>
				<?php
					endforeach;  
					else :

					if($kelas) :
					$kelas = array_merge($kelas, [[ 'kelas'=>$_SESSION['RAPORT']['kelas'],'kelas_id'=>$_SESSION['RAPORT']['kelas_id'] ]]);
					foreach($kelas as $k) :
					$arrKelas = explode(".", $k['kelas']);
					// jika kelas sama dengan kelas sekarang
					if($k['kelas'] == $_SESSION['RAPORT']['kelas']) :
				?>
				<option value="tinggal_di_kelas_<?= $k['kelas_id']; ?>"
				<?= 'tinggal_di_kelas_'.$k['kelas_id']==$status_akhir?'selected':''; ?>
				>Tinggal di kelas <?= $arrKelas[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelas[1]??''); ?></option>
				<?php else : ?>
				<option value="naik_ke_kelas_<?= $k['kelas_id']; ?>"
				<?= 'naik_ke_kelas_'.$k['kelas_id']==$status_akhir?'selected':''; ?>
				>Naik ke kelas <?= $arrKelas[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelas[1]??''); ?></option>
				<?php endif; ?>
				<?php endforeach; endif; endif; ?>
			</select>
			<?php  
				if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')) :
				echo $errors['no_un']??'';
			?>
			<label class="label">Nomor UN</label>
			<input type="text" name="no_un" placeholder="..." value="<?= $no_un; ?>">
			<?php endif; ?>

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>