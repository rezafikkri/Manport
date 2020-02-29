<?php  if(!class_exists("config")) { die; }

	$db = new tahun_ajaran;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-4 offset-left-4 offset-right-4">
	<div class="home default">
		<h1 class="judul marginBottom20px">Tambah tahun ajaran</h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<label class="label">Tahun ajaran</label>
			<p id="pesan_tahun_ajaran"></p>
			<input type="text" id="tahun_ajaran" placeholder="<?= (date('Y')-1).'-'.date('Y'); ?> ...">

			<a href="<?= config::base_url('admin/index.php?ref=tahun_ajaran'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
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
			const tahun_ajaran = $("#tahun_ajaran").val();
			const tokenCSRF = $("input#tokenCSRF").val();
			$("p.pesan").text("");
			$("p.pesan").removeClass("warning pesan");

			$.ajax({
				type:"POST",
				url:"tahun_ajaran/proses.php?action=add_tahun_ajaran",
				data:{tokenCSRF:tokenCSRF,tahun_ajaran:tahun_ajaran},
				beforeSend:function(){
					$(".progress_bar_back").show();
					$(".progress_bar").css({"width":"90%","transition":"3s"});
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon){
					statusAjax.setAttribute("value","yes");
					$(".progress_bar").css({"width":"100%","transition":"1s"});
					$(".progress_bar_back").fadeOut();

					let data;
					try {
						data  = JSON.parse(respon);
					} catch(e){}

					if(data != undefined && data.success != undefined) {
						swal("Selamat", "Tahun ajaran berhasil ditambahkan!");
						$("#form")[0].reset();

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.tahun_ajaran != undefined) {
							$("p#pesan_tahun_ajaran").addClass("pesan warning");
							$("p#pesan_tahun_ajaran").text(data.errors.tahun_ajaran);
						}

					} else if(data != undefined && data.tahun_ajaran_invalid != undefined) {
						$("p#pesan_tahun_ajaran").addClass("pesan warning");
						$("p#pesan_tahun_ajaran").text("Tahun ajaran salah!");
					} else {
						$("p#pesan_tahun_ajaran").addClass("pesan warning");
						$("p#pesan_tahun_ajaran").text("Tahun ajaran gagal ditambahkan!");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
})
</script>