<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbS = new siswa;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
?>
<div class="col-6 offset-left-3 offset-right-3">
	<div class="home default">
		<h1 class="judul marginBottom20px">Tambah Prestasi <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" id="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<p id="pesan_add_prestasi"></p>
			<label class="label">Jenis prestasi</label>
			<p id="pesan_jenis_prestasi"></p>
			<input type="text" id="jenis_prestasi" placeholder="...">
			<label class="label">Keterangan</label>
			<p id="pesan_keterangan"></p>
			<textarea spellcheck="false" placeholder="..." rows="5" id="keterangan"></textarea>

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button type="submit" id="add_prestasi" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	// tambah ekstrakurikuler
	$("button#add_prestasi").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $("input#tokenCSRF").val();
			const jenis_prestasi = $("input#jenis_prestasi").val();
			const keterangan = $("textarea#keterangan").val();
			const siswa_detail_id = $("input#siswa_detail_id").val();
			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");
			$.ajax({
				type:"POST",
				url:"raport_siswa/proses.php?action=add_prestasi",
				data:{tokenCSRF:tokenCSRF, siswa_detail_id:siswa_detail_id, jenis_prestasi:jenis_prestasi, keterangan:keterangan},
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
						swal("Selamat","Prestasi berhasil ditambahkan!");
						$("#form")[0].reset();

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.jenis_prestasi != undefined) {
							$("p#pesan_jenis_prestasi").addClass("pesan warning");
							$("p#pesan_jenis_prestasi").text(data.errors.jenis_prestasi);
						}
						if(data.errors.keterangan != undefined) {
							$("p#pesan_keterangan").addClass("pesan warning");
							$("p#pesan_keterangan").text(data.errors.keterangan);
						}
					} else {
						$("p#pesan_add_prestasi").addClass("pesan warning");
						$("p#pesan_add_prestasi").text("Prestasi gagal ditambahkan!");
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