<?php  if(!class_exists("config")) { die; }

	$db = new kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;
	
	$dbJ = new jurusan;
	$kelas_id = filter_input(INPUT_GET, 'kelas_id', FILTER_SANITIZE_STRING);
	$r = $db->get_one_kelas($kelas_id);
	$errors = $db->get_form_errors();
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default cf kelas">
		<h1 class="judul marginBottom20px">Edit Kelas</h1>
		<ol>
			<li>Kelas harus berupa bilangan romawi!, <br>contoh: X, XI, XII.</li>
			<li>Jika kelas lebih dari satu maka pisahkan dengan titik!, <br>contoh: X.1 atau X.a dst.</li>
		</ol>
		<form id="form" action="kelas/proses.php?action=edit_kelas" method="post">
			<?= $db->pesan_edit_kelas(); ?>
			<input type="hidden" name="kelas_id" value="<?= $r['kelas_id']??''; ?>">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<label class="label">Jurusan</label>
			<?= $errors['jurusan']??''; ?>
			<select name="jurusan">
				<option disabled="" selected="">...</option>
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
			<input type="text" name="kelas" placeholder="..." value="<?= $r['kelas']??''; ?>">

			<a href="<?= config::base_url('admin/index.php?ref=kelas'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button id="simpan" type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>