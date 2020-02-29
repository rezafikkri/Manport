<?php  if(!class_exists("config")) { die; }

	$db = new wali_kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
	$dbK = new kelas;

	$wali_kelas_id = filter_input(INPUT_GET, 'wali_kelas_id', FILTER_SANITIZE_STRING);
	$r = $db->get_one_wali_kelas($wali_kelas_id);
	$errors = $db->get_form_errors();
?>
<div class="col-6 offset-left-3 offset-right-3 marginBottom100px">
	<div class="home default">
		<h1 class="judul marginBottom20px">Edit Wali Kelas</h1>
		<form id="form" method="post" action="wali_kelas/proses.php?action=edit_wali_kelas">
			<?= $db->pesan_edit_wali_kelas(); ?>
			<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" name="wali_kelas_id" value="<?= $r['wali_kelas_id']??''; ?>">
			<label class="label">Jurusan</label>
			<select name="jurusan" url_tampil_kelas="wali_kelas/proses.php?action=tampil_kelas">
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
			<select name="kelas">
				<option disabled="" selected="">...</option>
				<?php
					if(!empty(trim($r['jurusan_id']))) :
					$kelas = $dbK->tampil_kelas($r['jurusan_id']);
					if($kelas) :
					foreach($kelas as $k) :
				?>
				<option value="<?= $k['kelas_id']; ?>"
				<?= $r['kelas_id']==$k['kelas_id']?'selected':''; ?>
				><?= $k['kelas']; ?></option>
				<?php endforeach; endif; endif; ?>
			</select>
			<label class="label">Nama wali kelas</label>
			<?= $errors['nama']??''; ?>
			<input type="text" name="nama" value="<?= $r['nama']??''; ?>" placeholder="...">
			<label class="label">Nip wali kelas</label>
			<?= $errors['nip']??''; ?>
			<input type="text" name="nip" value="<?= $r['nip']??''; ?>" placeholder="...">
			<label class="label">Password</label>
			<div class="inputCon">
				<a id="see_not_see_password"><span class="fa fa-lock"></span></a>
				<input type="password" id="password" name="password" placeholder="...">
			</div>

			<a href="<?= config::base_url('admin/index.php?ref=wali_kelas'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	// generate password wali kelas
	$("a#generate_password_wali_kelas").click(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"POST",
				url:"wali_kelas/proses.php?action=generate_password_wali_kelas",
				beforeSend:function() {
					$(".progress_bar_back").show();
					$(".progress_bar").css({"width":"90%","transition":"3s"});
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon) {
					statusAjax.setAttribute("value","yes");
					$(".progress_bar").css({"width":"100%","transition":"1s"});
					$(".progress_bar_back").fadeOut();

					$("input#password").val(respon);

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})

	// show hide 
	$("a#see_not_see_password").click(function(){
		const input = document.querySelector("input#password");
		const type = input.getAttribute('type');
		const span_see_not_see_password = document.querySelector("a#see_not_see_password span");
		if(type == "password") {
			input.setAttribute("type","text");
			span_see_not_see_password.classList.replace("fa-lock","fa-unlock-alt");
		} else {
			input.setAttribute("type","password");
			span_see_not_see_password.classList.replace("fa-unlock-alt","fa-lock");
		}
	})
})
</script>