<?php  if(!class_exists("config")) { die; }

	$db = new identitas_sekolah;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$errors = $db->get_form_errors();
	$old = $db->get_old_value();
?>
<div class="col-6 offset-right-3 offset-left-3 marginBottom100px">
	<div class="home default">
		<h1 class="judul marginBottom20px">Tambah identitas sekolah</h1>
		<form id="form" action="identitas_sekolah/proses.php?action=add_identitas_sekolah" method="post" enctype="multipart/form-data">
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">

			<?= $db->pesan_add_identitas_sekolah(); ?>
			<label class="label">Nama sekolah</label>
			<?= $errors['nama_sekolah']??''; ?>
			<input type="text" name="nama_sekolah" placeholder="..." value="<?= $old['nama_sekolah']??''; ?>">
			<?= $errors['alamat']??''; ?>
			<label class="label">Alamat</label>
			<textarea spellcheck="false" name="alamat" placeholder="..."><?= $old['alamat']??''; ?></textarea>
			<?= $errors['lama_belajar']??''; ?>
			<label class="label">Lama belajar</label>
			<select name="lama_belajar">
				<option selected="" disabled="">...</option>
				<?php  
					$arrLamaBelajar = ['3','4'];
					foreach($arrLamaBelajar as $lb) :
				?>
				<option value="<?= $lb; ?>"
				<?= $lb==($old['lama_belajar']??'')?'selected':''; ?>
				><?= $lb; ?> Tahun</option>
				<?php endforeach; ?>
			</select>
			<label class="label">Provinsi</label>
			<?= $errors['provinsi']??''; ?>
			<input type="text" name="provinsi" placeholder="..." value="<?= $old['provinsi']??''; ?>">

			<label class="label">Logo provinsi</label>
			<?= $errors['logoProvinsi']??''; ?>
			<div class="inputFile">
				<input type="file" name="logoProvinsi" accept="image/jpeg,image/png">
				<a class="btnFile"><span class="fa fa-search"></span></a>
				<span class="ketNameFile">...</span>
			</div>
			<img id="img" class="reviewImg width50 marginBottom20px">

			<label class="label">Kabupaten</label>
			<?= $errors['kabupaten']??''; ?>
			<input type="text" name="kabupaten" placeholder="..." value="<?= $old['kabupaten']??''; ?>">
			<label class="label">Nama kepala sekolah</label>
			<?= $errors['nama_kepala_sekolah']??''; ?>
			<input type="text" name="nama_kepala_sekolah" placeholder="..." value="<?= $old['nama_kepala_sekolah']??''; ?>">
			<label class="label">Nip kepala sekolah</label>
			<?= $errors['nip_kepala_sekolah']??''; ?>
			<input type="text" name="nip_kepala_sekolah" placeholder="..." value="<?= $old['nip_kepala_sekolah']??''; ?>">
			
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