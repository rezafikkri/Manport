<?php  if(!class_exists("config")) { die; }

	$db = new identitas_sekolah;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$r = $db->tampil_identitas_sekolah();
	$errors = $db->get_form_errors();
?>
<div class="col-6 offset-right-3 offset-left-3 marginBottom100px">
	<div class="home default">
		<h1 class="judul marginBottom20px">Edit identitas sekolah</h1>
		<form id="form" action="identitas_sekolah/proses.php?action=edit_identitas_sekolah" method="post" enctype="multipart/form-data">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">

			<?= $db->pesan_edit_identitas_sekolah(); ?>
			<label class="label">Nama sekolah</label>
			<?= $errors['nama_sekolah']??''; ?>
			<input type="text" name="nama_sekolah" placeholder="..." value="<?= $r['nama_sekolah']??''; ?>">
			<label class="label">Alamat</label>
			<?= $errors['alamat']??''; ?>
			<textarea spellcheck="false" name="alamat" placeholder="..."><?= $r['alamat']??''; ?></textarea>
			<label class="label">Lama belajar</label>
			<?= $errors['lama_belajar']??''; ?>
			<select name="lama_belajar">
				<option selected="" disabled="">...</option>
				<?php  
					$arrLamaBelajar = ['3','4'];
					foreach($arrLamaBelajar as $lb) :
				?>
				<option value="<?= $lb; ?>"
				<?= $lb==($r['lama_belajar']??'')?'selected':''; ?>
				><?= $lb; ?> Tahun</option>
				<?php endforeach; ?>
			</select>
			<label class="label">Provinsi</label>
			<?= $errors['provinsi']??''; ?>
			<input type="text" name="provinsi" placeholder="..." value="<?= $r['provinsi']??''; ?>">
			<label class="label">Logo provinsi</label>
			<?= $errors['logoProvinsi']??''; ?>
			<div class="inputFile">
				<input type="file" name="logoProvinsi" accept="image/jpeg,image/png">
				<a class="btnFile"><span class="fa fa-search"></span></a>
				<span class="ketNameFile <?php if($r['logo_prov']??'') echo 'contain'; ?>"><?= (trim(empty($r['logo_prov'])))?'Pilih gambar ...':($r['logo_prov']??''); ?></span>
			</div>
			<img id="img" src="<?= config::base_url('assets/img/logo/'.($r['logo_prov']??'')); ?>" class="reviewImg width50 marginBottom20px <?php if($r['logo_prov']??'') echo 'muncul'; ?>">

			<label class="label">Kabupaten</label>
			<?= $errors['kabupaten']??''; ?>
			<input type="text" name="kabupaten" placeholder="..." value="<?= $r['kabupaten']??''; ?>">
			<label class="label">Nama kepala sekolah</label>
			<?= $errors['nama_kepala_sekolah']??''; ?>
			<input type="text" name="nama_kepala_sekolah" placeholder="..." value="<?= $r['nama_kepala_sekolah']??''; ?>">
			<label class="label">Nip kepala sekolah</label>
			<?= $errors['nip_kepala_sekolah']??''; ?>
			<input type="text" name="nip_kepala_sekolah" placeholder="..." value="<?= $r['nip_kepala_sekolah']??''; ?>">

			<a href="<?= config::base_url('admin/index.php?ref=identitas_sekolah'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('input[type="file').change(function(e){
		$("#img").addClass("muncul");
		$("#img").attr('src', URL.createObjectURL(e.currentTarget.files[0]));
		$("span.ketNameFile").text(e.currentTarget.files[0].name);
		$("span.ketNameFile").addClass("contain");
	});

	$("a.btnFile").click(function(){
		$("input[name='logoProvinsi']").click();
	})
})
</script>