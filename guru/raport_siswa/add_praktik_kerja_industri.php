<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbS = new siswa;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
?>
<div class="col-8 offset-left-2 offset-left-2 marginBottom100px">
	<div class="home default">
		<h1 class="judul marginBottom20px">Tambah praktik kerja industri <span><?= $dbS->get_one_siswa_detail($siswa_detail_id, "masih_sekolah", 'sd.nama_siswa', $_SESSION['RAPORT']['kelas_id'])['nama_siswa']??''; ?></span></h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<input type="hidden" id="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<p id="pesan_add_prakerin"></p>
			<label class="label">Mitra Du/DI</label>
			<p id="pesan_mitra_du_di"></p>
			<input type="text" id="mitra_du_di" placeholder="...">
			<label class="label">Lokasi</label>
			<p id="pesan_lokasi"></p>
			<textarea spellcheck="false" id="lokasi" placeholder="..." rows="3"></textarea>
			<label class="label">Lamanya</label>
			<p id="pesan_lamanya"></p>
			<input type="text" id="lamanya" placeholder="...">
			<label class="label">Keterangan</label>
			<p id="pesan_Keterangan"></p>
			<textarea spellcheck="false" id="keterangan" placeholder="..." rows="3"></textarea>

			<a href="<?= config::base_url('guru/index.php?ref=raport_siswa&siswa_detail_id='.$siswa_detail_id); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button id="add_praktik_kerja_industri" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	$("button#add_praktik_kerja_industri").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $("input#tokenCSRF").val();
			const siswa_detail_id = $("input#siswa_detail_id").val();
			const mitra_du_di = $("input#mitra_du_di").val();
			const lokasi = $("textarea#lokasi").val();
			const lamanya = $("input#lamanya").val();
			const keterangan = $("textarea#keterangan").val();
			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");
			$.ajax({
				type:"POST",
				url:"raport_siswa/proses.php?action=add_praktik_kerja_industri",
				data:{tokenCSRF:tokenCSRF, siswa_detail_id:siswa_detail_id, mitra_du_di:mitra_du_di, lokasi:lokasi, lamanya:lamanya, keterangan:keterangan},
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
						swal("Selamat", "Praktik kerja industri berhasil ditambahkan!");
						$("form#form")[0].reset();
					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.mitra_du_di != undefined) {
							$("p#pesan_mitra_du_di").addClass("pesan warning");
							$("p#pesan_mitra_du_di").text(data.errors.mitra_du_di);
						}
						if(data.errors.lokasi != undefined) {
							$("p#pesan_lokasi").addClass("pesan warning");
							$("p#pesan_lokasi").text(data.errors.lokasi);
						}
						if(data.errors.lamanya != undefined) {
							$("p#pesan_lamanya").addClass("pesan warning");
							$("p#pesan_lamanya").text(data.errors.lamanya);
						}
						if(data.errors.keterangan != undefined) {
							$("p#pesan_keterangan").addClass("pesan warning");
							$("p#pesan_keterangan").text(data.errors.keterangan);
						}
					} else {
						$("p#pesan_add_prakerin").addClass("pesan warning");
						$("p#pesan_add_prakerin").text("Praktik Kerja Industri gagal ditambahkan!");
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