<?php  if(!class_exists("config")) { die; }

	$db = new kkm;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-4 offset-left-4 offset-right-4 marginBottom100px">
	<div class="home default cf">
		<h1 class="judul marginBottom20px">Tambah Kkm</h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<!-- kkm -->
			<label class="label">kkm</label>
			<p id="pesan_kkm"></p>
			<input type="text" id="kkm" placeholder="72 ...">
			<!-- kurang -->
			<label class="label">Kurang</label>
			<p id="pesan_kurang"></p>
			<input type="text" id="kurang" placeholder="0-71 ...">
			<!-- cukup -->
			<label class="label">Cukup</label>
			<p id="pesan_cukup"></p>
			<input type="text" id="cukup" placeholder="71-83 ...">
			<!-- baik -->
			<label class="label">Baik</label>
			<p id="pesan_baik"></p>
			<input type="text" id="baik" placeholder="83-92 ...">
			<!-- sangat baik -->
			<label class="label">Sangat Baik</label>
			<p id="pesan_sangat_baik"></p>
			<input type="text" id="sangat_baik" placeholder="92-100 ...">

			<a href="<?= config::base_url('admin/index.php?ref=kkm'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	//simpan
	$("button#simpan").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");		

		$("p.pesan").text("");
		$("p.pesan").removeClass("warning pesan");
		if(statusAjax.getAttribute('value') == "yes") {
			const tokenCSRF = document.querySelector("input#tokenCSRF").value;
			const kkm = $("input#kkm").val();
			const kurang = $("input#kurang").val();
			const cukup = $("input#cukup").val();
			const baik = $("input#baik").val();
			const sangat_baik = $("input#sangat_baik").val();
			const tahun_ajaran_id = $("select#tahun_ajaran").val();

			$.ajax({
				type:"POST",
				url:"kkm/proses.php?action=add_kkm",
				data:{tokenCSRF:tokenCSRF, kkm:kkm, kurang:kurang, cukup:cukup, baik:baik, sangat_baik:sangat_baik, tahun_ajaran_id:tahun_ajaran_id},
				beforeSend:function(){
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
						swal("Selamat", "Kkm berhasil ditambahkan!");
						$("form#form")[0].reset();

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.kkm != undefined) {
							$("p#pesan_kkm").addClass("pesan warning");
							$("p#pesan_kkm").text(data.errors.kkm);
						}
						if(data.errors.kurang != undefined) {
							$("p#pesan_kurang").addClass("pesan warning");
							$("p#pesan_kurang").text(data.errors.kurang);
						}
						if(data.errors.cukup != undefined) {
							$("p#pesan_cukup").addClass("pesan warning");
							$("p#pesan_cukup").text(data.errors.cukup);
						}
						if(data.errors.baik != undefined) {
							$("p#pesan_baik").addClass("pesan warning");
							$("p#pesan_baik").text(data.errors.baik);
						}
						if(data.errors.sangat_baik != undefined) {
							$("p#pesan_sangat_baik").addClass("pesan warning");
							$("p#pesan_sangat_baik").text(data.errors.sangat_baik);
						}
						if(data.errors.tahun_ajaran_id != undefined) {
							$("p#pesan_tahun_ajaran").addClass("pesan warning");
							$("p#pesan_tahun_ajaran").text(data.errors.tahun_ajaran_id);
						}
					} else {
						$("p#pesan_kkm").addClass("pesan warning");
						$("p#pesan_kkm").text("Kkm gagal ditambahkan!");
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