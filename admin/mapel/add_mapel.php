<?php  if(!class_exists("config")) { die; }

	$db = new mapel;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbTA = new tahun_ajaran;
	$dbJ = new jurusan;
	$dbKm = new kkm;
	$dbK = new kelas;
	$jurusan_id = filter_var($_COOKIE['jurusan_id']??'', FILTER_SANITIZE_STRING);
	$kelas_id = filter_var($_COOKIE['kelas_id']??'', FILTER_SANITIZE_STRING);
	// delete cookie
	setcookie('jurusan_id', '', time()-3600);
	setcookie('kelas_id', '', time()-3600);
?>
<div class="col-6 offset-right-3 offset-left-3 marginBottom100px">
	<div class="home default mapel cf">
		<h1 class="judul marginBottom20px">Tambah Mata Pelajaran</h1>
		<form id="form">
			<p id="pesan_jurusan"></p>
			<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
			<label class="label">Jurusan</label>
			<select id="jurusan" name="jurusan" url_tampil_kelas="mapel/proses.php?action=tampil_kelas">
				<option selected="" disabled="">...</option>
				<?php  
					$dataJurusan = $dbJ->tampil_jurusan();
					if($dataJurusan) :
					foreach($dataJurusan as $j) :
				?>
				<option value="<?= $j['jurusan_id']; ?>"
				<?= $j['jurusan_id']==$jurusan_id?'selected':''; ?>
				><?= $j['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Kelas</label>
			<p id="pesan_kelas"></p>
			<select id="kelas" name="kelas">
				<option disabled="" selected="">...</option>
				<?php
					if($jurusan_id) :
					$dataKelas = $dbK->tampil_kelas($jurusan_id);
					if($dataKelas) :
					foreach($dataKelas as $k) :
				?>
				<option value="<?= $k['kelas_id']; ?>"
				<?= $k['kelas_id']==$kelas_id?'selected':''; ?>
				><?= $k['kelas']; ?></option>
				<?php endforeach; endif; endif; ?>
			</select>

			<label class="label">Awal tahun ajaran</label>
			<p id="pesan_awal_tahun_ajaran"></p>
			<select id="awal_tahun_ajaran">
				<option disabled="" selected="">...</option>
				<?php 
					$dataTahun_ajaran = $dbTA->tampil_tahun_ajaran();
					if($dataTahun_ajaran) :
					foreach($dataTahun_ajaran as $ta) :
				?>
				<option value="<?= $ta['tahun_ajaran_id']; ?>"
				<?= $ta['tahun_ajaran_id']==($_SESSION['RAPORT']['tahun_ajaran_id']??'')?'selected':''; ?>
				><?= $ta['tahun']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Akhir tahun ajaran</label>
			<p id="pesan_akhir_tahun_ajaran"></p>
			<select id="akhir_tahun_ajaran">
				<option disabled="" selected="">...</option>
				<?php
					if($dataTahun_ajaran) :
					foreach($dataTahun_ajaran as $ta) :
				?>
				<option value="<?= $ta['tahun_ajaran_id']; ?>"><?= $ta['tahun']; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<a class="btn_keterangan marginBottom10px" href="dropdownAuto">Apa itu Awal dan Akhir Tahun ajaran?</a>
			<div class="keterangan" target-menu="auto">
				Awal tahun ajaran dan Akhir tahun ajaran berguna untuk menentukan masa berlaku suatu mata pelajaran, jadi Awal tahun ajaran adalah awal berlakunya Mata pelajaran dan Akhir tahun ajaran adalah akhir berlakunya Mata pelajaran.
			</div>

			<label class="label marginTop20px">Nama mapel</label>
			<p id="pesan_nama_mapel"></p>
			<input type="text" id="nama_mapel" placeholder="...">
			<label class="label">Kelompok mapel</label>
			<p id="pesan_kelompok_mapel"></p>
			<input type="text" id="kelompok_mapel" placeholder="...">

			<label class="label">Kkm</label>
			<p id="pesan_kkm"></p>
			<select id="kkm">
				<option disabled="" selected="">...</option>
				<?php  
					$dataKkm = $dbKm->tampil_kkm();
					if($dataKkm) :
					foreach($dataKkm as $km) :
				?>
				<option value="<?= $km['kkm_id']; ?>"><?= $km['kkm']; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<a href="<?= config::base_url('admin/index.php?ref=mapel'); ?>" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
			<button class="button green" id="add_mapel"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	//insert
	$("button#add_mapel").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		$("p.pesan").text("");
		$("p.pesan").removeClass("pesan warning");
		
		if(statusAjax.getAttribute("value") == "yes") {
			const jurusan_id = $("select#jurusan").val();
			const kelas_id = $("select#kelas").val();
			const nama_mapel = $("input#nama_mapel").val();
			const kelompok_mapel = $("input#kelompok_mapel").val();
			const kkm_id = $("select#kkm").val();
			const awal_tahun_ajaran = $("select#awal_tahun_ajaran").val();
			const akhir_tahun_ajaran = $("select#akhir_tahun_ajaran").val();
			const tokenCSRF = $("input#tokenCSRF").val();

			$.ajax({
				type:"POST",
				url:"mapel/proses.php?action=add_mapel",
				data:{tokenCSRF:tokenCSRF, jurusan_id:jurusan_id, kelas_id:kelas_id, nama_mapel:nama_mapel, kelompok_mapel:kelompok_mapel, kkm_id:kkm_id, awal_tahun_ajaran_id:awal_tahun_ajaran, akhir_tahun_ajaran_id:akhir_tahun_ajaran },
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
						swal("Selamat","Mapel berhasil ditambahkan!");
						$("form#form")[0].reset();
						// cek value jurusan
						if(!$('select[name="jurusan"]').val()) {
							$('select[name="kelas"]').html('<option disabled="" selected="">...</option>');
						}

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.kelas_id != undefined) {
							$("p#pesan_kelas").addClass("pesan warning");
							$("p#pesan_kelas").text(data.errors.kelas_id);
						}
						if(data.errors.nama_mapel != undefined) {
							$("p#pesan_nama_mapel").addClass("pesan warning");
							$("p#pesan_nama_mapel").text(data.errors.nama_mapel);
						}
						if(data.errors.kelompok_mapel != undefined) {
							$("p#pesan_kelompok_mapel").addClass("pesan warning");
							$("p#pesan_kelompok_mapel").text(data.errors.kelompok_mapel);
						}
						if(data.errors.kkm_id != undefined) {
							$("p#pesan_kkm").addClass("pesan warning");
							$("p#pesan_kkm").text(data.errors.kkm_id);
						}
						if(data.errors.awal_tahun_ajaran_id != undefined) {
							$("p#pesan_awal_tahun_ajaran").addClass("pesan warning");
							$("p#pesan_awal_tahun_ajaran").text(data.errors.awal_tahun_ajaran_id);
						}
						if(data.errors.akhir_tahun_ajaran_id != undefined) {
							$("p#pesan_akhir_tahun_ajaran").addClass("pesan warning");
							$("p#pesan_akhir_tahun_ajaran").text(data.errors.akhir_tahun_ajaran_id);
						}
					} else {
						$("p#pesan_jurusan").addClass("pesan warning");
						$("p#pesan_jurusan").text("Mapel gagal ditambahkan!");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 1000);
				}
			});
		}
	});
})
</script>