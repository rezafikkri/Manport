<?php  if(!class_exists("config")) { die; }

	$db = new wali_kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$j = new jurusan;
	$errors = $db->get_form_errors();
	$old = $db->get_old_value();
?>
<div class="col-6 offset-left-3 offset-right-3 marginBottom100px">
	<div class="home default">
		<h1 class="judul marginBottom20px">Tambah Wali Kelas</h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<p id="pesan_jurusan"></p>
			<label class="label">Jurusan</label>
			<select id="jurusan" name="jurusan" url_tampil_kelas="wali_kelas/proses.php?action=tampil_kelas">
				<option disabled="" selected="">...</option>
				<?php  
					$jurusan = $j->tampil_jurusan();
					if($jurusan) :
					foreach($jurusan as $r) :
				?>
				<option value="<?= $r['jurusan_id']; ?>"><?= $r['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Kelas</label>
			<p id="pesan_kelas"></p>
			<select id="kelas" name="kelas">
				<option disabled="" selected="">...</option>
			</select>
			<label class="label">Nama wali kelas</label>
			<p id="pesan_nama"></p>
			<input type="text" id="nama" placeholder="...">
			<label class="label">Nip wali kelas</label>
			<p id="pesan_nip"></p>
			<input type="text" id="nip" placeholder="...">
			<label class="label">Password</label>
			<p id="pesan_password"></p>
			<div class="inputCon">
				<a id="generate_password_wali_kelas"><span class="fa fa-pencil"></span></a>
				<a id="see_not_see_password"><span class="fa fa-lock"></span></a>
				<input type="password" id="password" placeholder="..." class="paddingRight120px">
			</div>

			<a href="<?= config::base_url('admin/index.php?ref=wali_kelas'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	// tambah wali kelas
	$("button#simpan").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {

			const tokenCSRF = $("input#tokenCSRF").val();
			const jurusan_id = $("select#jurusan").val();
			const kelas_id = $("select#kelas").val();
			const nama = $("input#nama").val();
			const nip = $("input#nip").val();
			const password = $("input#password").val();
			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");
			$.ajax({
				type:"POST",
				url:"wali_kelas/proses.php?action=add_wali_kelas",
				data:{tokenCSRF:tokenCSRF, jurusan_id:jurusan_id, kelas_id:kelas_id, nama:nama, nip:nip, password:password},
				beforeSend:function() {
					$(".progress_bar_back").show();
					$(".progress_bar").css({"width":"90%","transition":"3s"});
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon) {
					statusAjax.setAttribute("value","yes");
					$(".progress_bar").css({"width":"100%","transition":"1s"});
					$(".progress_bar_back").fadeOut();

					let data;
					try {
						data = JSON.parse(respon);
					} catch(e){}

					if(data != undefined && data.success != undefined) {
						swal("Selamat", "Wali kelas berhasil ditambahkan!");
						$("form#form")[0].reset();
						$('select[name="kelas"]').html('<option disabled="" selected="">...</option>');

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.kelas_id != undefined) {
							$("p#pesan_kelas").addClass("pesan warning");
							$("p#pesan_kelas").text(data.errors.kelas_id);
						}
						if(data.errors.nama != undefined) {
							$("p#pesan_nama").addClass("pesan warning");
							$("p#pesan_nama").text(data.errors.nama);
						}
						if(data.errors.nip != undefined) {
							$("p#pesan_nip").addClass("pesan warning");
							$("p#pesan_nip").text(data.errors.nip);
						}
						if(data.errors.password != undefined) {
							$("p#pesan_password").addClass("pesan warning");
							$("p#pesan_password").text(data.errors.password);
						}
					} else {
						$("p#pesan_jurusan").addClass("pesan warning");
						$("p#pesan_jurusan").text("Wali kelas gagal ditambahkan!");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	});

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