<?php  if(!class_exists("config")) { die; }

	$db = new mapel;
	if($db->cekLoginNo_halamanAdmin() === true) die;
	
	$dbTA = new tahun_ajaran;
	$dbJ = new jurusan;
	$dbKm = new kkm;
	$dbK = new kelas;
	$mapel_id = filter_input(INPUT_GET, 'mapel_id', FILTER_SANITIZE_STRING);
	$r = $db->get_one_mapel($mapel_id);
	$errors = $db->get_form_errors();
?>
<div class="col-6 offset-right-3 offset-left-3 marginBottom100px">
	<div class="home default mapel cf">
		<h1 class="judul marginBottom20px">Edit Mata Pelajaran</h1>
		<form id="form" action="mapel/proses.php?action=edit_mapel" method="post">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="mapel_id" value="<?= $r['mata_pelajaran_id']??''; ?>">

			<?= $db->pesan_edit_mapel()??''; ?>
			<label class="label">Jurusan</label>
			<select name="jurusan" url_tampil_kelas="mapel/proses.php?action=tampil_kelas">
				<option selected="" disabled="">...</option>
				<?php  
					$dataJurusan = $dbJ->tampil_jurusan();
					if($dataJurusan) :
					foreach($dataJurusan as $j) :
				?>
				<option value="<?= $j['jurusan_id']; ?>"
				<?= $j['jurusan_id']==$r['jurusan_id']?'selected':''; ?>
				><?= $j['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Kelas</label>
			<?= $errors['kelas']??''; ?>
			<select name="kelas">
				<option disabled="" selected="">...</option>
				<?php  
					if($r) :
					$dataK = $dbK->tampil_kelas($r['jurusan_id']);
					if($dataK) :
					foreach($dataK as $k) :
				?>
				<option value="<?= $k['kelas_id']; ?>"
				<?= $k['kelas_id']==$r['kelas_id']?'selected':''; ?>
				><?= $k['kelas']; ?></option>
				<?php endforeach; endif; endif; ?>
			</select>

			<label class="label">Awal tahun ajaran</label>
			<?= $errors['awal_tahun_ajaran_id']??''; ?>
			<select name="awal_tahun_ajaran_id">
				<option disabled="" selected="">...</option>
				<?php 
					$dataTahun_ajaran = $dbTA->tampil_tahun_ajaran();
					if($dataTahun_ajaran) :
					foreach($dataTahun_ajaran as $taAW) :
				?>
				<option value="<?= $taAW['tahun_ajaran_id']; ?>"
				<?= $taAW['tahun_ajaran_id']==$r['awal_tahun_ajaran']?'selected':''; ?>
				><?= $taAW['tahun']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Akhir tahun ajaran</label>
			<?= $errors['akhir_tahun_ajaran_id']??''; ?>
			<select name="akhir_tahun_ajaran_id">
				<option disabled="" selected="">...</option>
				<?php
					if($dataTahun_ajaran) :
					foreach($dataTahun_ajaran as $taAK) :
				?>
				<option value="<?= $taAK['tahun_ajaran_id']; ?>"
				<?= $taAK['tahun_ajaran_id']==$r['akhir_tahun_ajaran']?'selected':''; ?>
				><?= $taAK['tahun']; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<a class="btn_keterangan marginBottom10px" href="dropdownAuto">Apa itu Awal dan Akhir Tahun ajaran?</a>
			<div class="keterangan" target-menu="auto">
				Awal tahun ajaran dan Akhir tahun ajaran berguna untuk menentukan masa berlaku suatu mata pelajaran, jadi Awal tahun ajaran adalah awal berlakunya Mata pelajaran dan Akhir tahun ajaran adalah akhir berlakunya Mata pelajaran.
			</div>

			<label class="label marginTop20px">Nama mapel</label>
			<?= $errors['nama_mapel']??''; ?>
			<input type="text" name="nama_mapel" placeholder="..." value="<?= $r['nama_mapel']??''; ?>">
			<label class="label">Kelompok mapel</label>
			<?= $errors['kelompok_mapel']??''; ?>
			<input type="text" name="kelompok_mapel" placeholder="..." value="<?= $r['kelompok_mapel']??''; ?>">

			<label class="label">Kkm</label>
			<?= $errors['kkm']??''; ?>
			<select name="kkm">
				<option disabled="" selected="">...</option>
				<?php  
					$dataKkm = $dbKm->tampil_kkm();
					if($dataKkm) :
					foreach($dataKkm as $km) :
				?>
				<option value="<?= $km['kkm_id']; ?>"
				<?= $km['kkm_id']==$r['kkm_id']?'selected':''; ?>
				><?= $km['kkm']; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<a href="<?= config::base_url('admin/index.php?ref=mapel'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button class="button green" id="add_mapel"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>