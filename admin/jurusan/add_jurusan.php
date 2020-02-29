<?php  if(!class_exists("config")) { die; }

	$db = new jurusan;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-6 offset-right-3 offset-left-3">
	<div class="home jurusan">
		<h1 class="judul marginBottom20px">Tambah Jurusan</h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">

			<label class="label">Nama jurusan</label>
			<p class="pesan_nama_jurusan"></p>
			<input type="text" id="nama_jurusan" placeholder="...">

			<a href="<?= config::base_url('admin/index.php?ref=jurusan'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	$("button#simpan").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");

		if(statusAjax.getAttribute("value") == "yes") {
			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");

			const nama_jurusan = document.querySelector("input#nama_jurusan").value;
			const tokenCSRF = document.querySelector("input#tokenCSRF").value;
			$.ajax({
				type:"POST",
				url:"jurusan/proses.php?action=add_jurusan",
				data:{tokenCSRF:tokenCSRF, nama_jurusan:nama_jurusan},
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
						$("#form")[0].reset();
						swal("Selamat", "Jurusan berhasil ditambahkan!");

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.nama_jurusan != undefined) {
							$("p.pesan_nama_jurusan").addClass("pesan warning");
							$("p.pesan_nama_jurusan").text(data.errors.nama_jurusan);
						}
					} else {
						$("p.pesan_nama_jurusan").addClass("pesan warning");
						$("p.pesan_nama_jurusan").text("Jurusan gagal ditambahkan!");
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