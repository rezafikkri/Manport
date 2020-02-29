<?php  if(!class_exists("config")) { die; }

	$db = new kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
	$errors = $db->get_form_errors();
	$jurusan_id = filter_var($_COOKIE['jurusan_id']??'', FILTER_SANITIZE_STRING);
	// delete cookie
	setcookie('jurusan_id', '', time()-3600);
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default cf kelas">
		<h1 class="judul marginBottom20px">Tambah Kelas</h1>
		<ol>
			<li>Kelas harus berupa bilangan romawi!, <br>contoh: X, XI, XII.</li>
			<li>Jika kelas lebih dari satu maka pisahkan dengan titik!, <br>contoh: X.1 atau X.a dst.</li>
		</ol>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<label class="label">Jurusan</label>
			<p class="pesan_jurusan"></p>
			<select id="jurusan">
				<option selected="" disabled="">...</option>
				<?php  
					$jurusan = $dbJ->tampil_jurusan();
					if($jurusan) :
					foreach($jurusan as $r) :
				?>
				<option value="<?= $r['jurusan_id']; ?>"
				<?= $r['jurusan_id']==$jurusan_id?'selected':''; ?>
				><?= $r['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Kelas</label>
			<p class="pesan_kelas"></p>
			<input type="text" id="kelas" placeholder="...">

			<a href="<?= config::base_url('admin/index.php?ref=kelas'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	$("#simpan").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");

		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $('input#tokenCSRF').val();
			const jurusan_id = $('select#jurusan').val();
			const kelas = $('input#kelas').val();

			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");
			$.ajax({
				type:"POST",
				url:"kelas/proses.php?action=add_kelas",
				data:{tokenCSRF:tokenCSRF, jurusan_id:jurusan_id, kelas:kelas},
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
					}catch(e){}

					if(data != undefined && data.success != undefined) {
						swal("Selamat","Kelas berhasil ditambahkan!");
						$("#form")[0].reset();

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.jurusan_id != undefined) {
							$("p.pesan_jurusan").addClass("pesan warning");
							$("p.pesan_jurusan").text(data.errors.jurusan_id);
						}
						if(data.errors.kelas != undefined) {
							$("p.pesan_kelas").addClass("pesan warning");
							$("p.pesan_kelas").text(data.errors.kelas);
						}

					} else if(data != undefined && data.kelas_invalid != undefined) {
						$("p.pesan_kelas").addClass("pesan warning");
						$("p.pesan_kelas").text("Kelas salah!");
					} else {
						$("p.pesan_jurusan").addClass("pesan warning");
						$("p.pesan_jurusan").text("Kelas gagal ditambahkan!");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	});
})
</script>