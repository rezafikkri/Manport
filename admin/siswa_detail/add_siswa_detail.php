<?php  if(!class_exists("config")) { die; }

	$dbJ = new jurusan;
	if($dbJ->cekLoginNo_halamanAdmin() === true) die;
	
	$dbSem = new semester;
	$dbK = new kelas;
	$jurusan_id = filter_var($_COOKIE['jurusan_id']??'', FILTER_SANITIZE_STRING);
	$kelas_id = filter_var($_COOKIE['kelas_id']??'', FILTER_SANITIZE_STRING);
	// delete cookie
	setcookie('jurusan_id', '', time()-3600);
	setcookie('kelas_id', '', time()-3600);
?>
<div class="col-8 offset-right-2 offset-left-2 marginBottom100px">
	<div class="home default cf">
		<h1 class="judul marginBottom20px">Tambah Siswa</h1>
		<form id="form">
			<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<p id="pesan_jurusan"></p>
			<label class="label">Jurusan</label>
			<select name="jurusan" id="jurusan" url_tampil_kelas="siswa_detail/proses.php?action=tampil_kelas">
				<option disabled="" selected="">...</option>
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
			<p id="pesan_kelas"></p>
			<select name="kelas" id="kelas">
				<option disabled="" selected="">...</option>
				<?php 
					if(!empty(trim($jurusan_id))) :
					$kelas = $dbK->tampil_kelas($jurusan_id);
					if($kelas) :
					foreach($kelas as $k) :
				?>
				<option value="<?= $k['kelas_id']; ?>"
				<?= $k['kelas_id']==($kelas_id??'')?'selected':''; ?>
				><?= $k['kelas']; ?></option>
				<?php endforeach; endif; endif; ?>
			</select>

			<label class="label">Nama siswa</label>
			<!-- pesan nama siswa -->
			<p id="pesan_nama_siswa"></p>
			<input type="text" id="nama_siswa" placeholder="...">

			<label class="label">NISN</label>
			<!-- pesan nisn -->
			<p id="pesan_nisn"></p>
			<input type="text" id="nisn" placeholder="..." maxlength="10">
			<label class="label">NIS</label>
			<!-- pesan no induk -->
			<p id="pesan_no_induk"></p>
			<input type="text" id="no_induk" placeholder="..." maxlength="6">

			<p class="keterangan_input marginTop20px">Tempat Tanggal Lahir :</p>
			<label class="label">Tempat lahir</label>
			<!-- pesan tempat lahir -->
			<p id="pesan_tempat_lahir"></p>
			<input type="text" id="tempat_lahir" placeholder="...">
			<label class="label">Tanggal lahir</label>
			<!-- pesan tanggal lahir -->
			<p id="pesan_tanggal_lahir"></p>
			<select id="tanggal_lahir">
				<option disabled="" selected="">...</option>
				<?php for($tgl=1;$tgl<=31;$tgl++) : ?>
				<option value="<?= $tgl; ?>"><?= $tgl; ?></option>
				<?php endfor; ?>
			</select>
			<label class="label">Bulan lahir</label>
			<!-- pesan bulan lahir -->
			<p id="pesan_bulan_lahir"></p>
			<select id="bulan_lahir">
				<option disabled="" selected="">...</option>
				<?php for($bln=1;$bln<=12;$bln++) : ?>
				<option value="<?= $bln; ?>"><?= $bln; ?></option>
				<?php endfor; ?>
			</select>
			<label class="label">Tahun lahir</label>
			<!-- pesan tahun lahir -->
			<p id="pesan_tahun_lahir"></p>
			<select id="tahun_lahir">
				<option disabled="" selected="">...</option>
				<?php for($thn=date('Y', time())-24;$thn<=date('Y', time());$thn++) : ?>
				<option value="<?= $thn; ?>"><?= $thn; ?></option>
				<?php endfor; ?>
			</select>

			<label class="label marginTop20px">Jenis kelamin</label>
			<p id="pesan_jenis_kelamin"></p>
			<select id="jenis_kelamin">
				<option selected="" disabled="">...</option>
				<option>Laki-laki</option>
				<option>Perempuan</option>
			</select>

			<label class="label">Agama</label>
			<!-- pesan agama -->
			<p id="pesan_agama"></p>
			<input type="text" id="agama" placeholder="...">
			<label class="label">Status dalam keluarga</label>
			<!-- pesan status dalam keluarga -->
			<p id="pesan_status_dalam_keluarga"></p>
			<input type="text" id="status_dalam_keluarga" placeholder="...">
			<label class="label">Anak ke</label>
			<!-- pesan anak ke -->
			<p id="pesan_anak_ke"></p>
			<input type="text" id="anak_ke" placeholder="...">
			<label class="label">Alamat peserta didik</label>
			<!-- pesan alamat peserta didik -->
			<p id="pesan_alamat_peserta_didik"></p>
			<textarea spellcheck="false" id="alamat_peserta_didik" placeholder="..."></textarea>
			<label class="label">Nomor telpon rumah</label>
			<p id="pesan_nomor_telpon_rumah"></p>
			<input type="text" id="nomor_telpon_rumah" placeholder="...">
			<label class="label">Sekolah asal</label>
			<!-- pesan asal sekolah -->
			<p id="pesan_sekolah_asal"></p>
			<input type="text" id="sekolah_asal" placeholder="...">

			<p class="keterangan_input marginTop20px">Diterima disekolah ini :</p>
			<label class="label">Dikelas</label>
			<!-- pesan di kelas -->
			<p id="pesan_dikelas"></p>
			<input type="text" id="dikelas" placeholder="...">
			<label class="label">Pada tanggal</label>
			<!-- pesan pada tanggal -->
			<p id="pesan_pada_tanggal"></p>
			<input type="text" id="pada_tanggal" placeholder="...">
			<label class="label">Semester</label>
			<!-- pesan semester -->
			<p id="pesan_semester"></p>
			<select id="semester">
				<option disabled="" selected="">...</option>
				<?php  
					$dataSemester = $dbSem->tampil_semester();
					if($dataSemester) :
					foreach($dataSemester as $sem) :
				?>
				<option value="<?= $sem['semester']; ?>"><?= $sem['semester']; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<p class="keterangan_input marginTop20px">Data orang tua :</p>
			<label class="label">Nama ayah</label>
			<!-- pesan nama ayah -->
			<p id="pesan_nama_ayah"></p>
			<input type="text" id="nama_ayah" placeholder="...">
			<label class="label">Nama ibu</label>
			<!-- pesan nama ibu -->
			<p id="pesan_nama_ibu"></p>
			<input type="text" id="nama_ibu" placeholder="...">
			<label class="label">Alamat orang tua</label>
			<!-- pesan alamat orang tua -->
			<p id="pesan_alamat_orang_tua"></p>
			<textarea spellcheck="false" id="alamat_orang_tua" placeholder="..."></textarea>
			<label class="label">Pekerjaan ayah</label>
			<!-- pesan pekerjaan ayah -->
			<p id="pesan_pekerjaan_ayah"></p>
			<input type="text" id="pekerjaan_ayah" placeholder="...">		
			<label class="label">Pekerjaan ibu</label>
			<!-- pesan pekerjaan ibu -->
			<p id="pesan_pekerjaan_ibu"></p>
			<input type="text" id="pekerjaan_ibu" placeholder="...">

			<p class="keterangan_input marginTop20px">Data Wali :</p>
			<label class="label">Nama wali</label>
			<input type="text" id="nama_wali" placeholder="...">
			<label class="label">Alamat wali</label>
			<textarea spellcheck="false" id="alamat_wali" placeholder="..."></textarea>
			<label class="label">Pekerjaan wali</label>
			<input type="text" id="pekerjaan_wali" placeholder="...">

			<a href="index.php?ref=siswa_detail" class="button no_hover"><span class="fa fa-arrow-left"></span></a>	
			<button id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	$("#simpan").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		$("p.pesan").text("");
		$("p.pesan").removeClass("warning pesan");

		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $("input#tokenCSRF").val();
			const jurusan_id = $("select#jurusan").val();
			const kelas_id = $("select#kelas").val();
			const nama_siswa = $("input#nama_siswa").val();
			const nisn = $("input#nisn").val();
			const no_induk = $("input#no_induk").val();
			const tempat_lahir = $("input#tempat_lahir").val();
			const tanggal_lahir = $("select#tanggal_lahir").val();
			const bulan_lahir = $("select#bulan_lahir").val();
			const tahun_lahir = $("select#tahun_lahir").val();
			const jenis_kelamin = $("select#jenis_kelamin").val();
			const agama = $("input#agama").val();
			const status_dalam_keluarga = $("input#status_dalam_keluarga").val();
			const anak_ke = $("input#anak_ke").val();
			const alamat_peserta_didik = $("textarea#alamat_peserta_didik").val();
			const nomor_telpon_rumah = $("input#nomor_telpon_rumah").val();
			const sekolah_asal = $("input#sekolah_asal").val();
			const dikelas = $("input#dikelas").val();
			const pada_tanggal = $("input#pada_tanggal").val();
			const semester = $("select#semester").val();
			const nama_ayah = $("input#nama_ayah").val();
			const nama_ibu = $("input#nama_ibu").val();
			const alamat_orang_tua = $("textarea#alamat_orang_tua").val();
			const pekerjaan_ayah = $("input#pekerjaan_ayah").val();
			const pekerjaan_ibu = $("input#pekerjaan_ibu").val();
			const nama_wali = $("input#nama_wali").val();
			const alamat_wali = $("textarea#alamat_wali").val();
			const pekerjaan_wali = $("input#pekerjaan_wali").val();

			$.ajax({
				type:"POST",
				url:"siswa_detail/proses.php?action=add_siswa_detail",
				data: { tokenCSRF:tokenCSRF, jurusan_id:jurusan_id, kelas_id:kelas_id, nama_siswa:nama_siswa, nisn:nisn, no_induk:no_induk, tempat_lahir:tempat_lahir, tanggal_lahir:tanggal_lahir, bulan_lahir:bulan_lahir, tahun_lahir:tahun_lahir, jenis_kelamin:jenis_kelamin, agama:agama, status_dalam_keluarga:status_dalam_keluarga, anak_ke:anak_ke, alamat_peserta_didik:alamat_peserta_didik, nomor_telpon_rumah:nomor_telpon_rumah, sekolah_asal:sekolah_asal, dikelas:dikelas, pada_tanggal:pada_tanggal, semester:semester, nama_ayah:nama_ayah, nama_ibu:nama_ibu, alamat_orang_tua:alamat_orang_tua, pekerjaan_ayah:pekerjaan_ayah, pekerjaan_ibu:pekerjaan_ibu, nama_wali:nama_wali, alamat_wali:alamat_wali, pekerjaan_wali:pekerjaan_wali },
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
						swal('Selamat','Siswa berhasil ditambahkan!');
						$("#form")[0].reset();
						// cek value jurusan
						if(!$('select[name="jurusan"]').val()) {
							$('select[name="kelas"]').html('<option disabled="" selected="">...</option>');
						}

					} else if(data != undefined && data.errors != undefined) {
						$("p#pesan_jurusan").addClass("pesan warning");
						$("p#pesan_jurusan").text("Siswa gagal ditambahkan!");

						if(data.errors.kelas_id != undefined) {
							$("p#pesan_kelas").addClass("pesan warning");
							$("p#pesan_kelas").text(data.errors.kelas_id);
						}
						if(data.errors.nama_siswa != undefined) {
							$("p#pesan_nama_siswa").addClass("pesan warning");
							$("p#pesan_nama_siswa").text(data.errors.nama_siswa);
						}
						if(data.errors.nisn != undefined) {
							$("p#pesan_nisn").addClass("pesan warning");
							$("p#pesan_nisn").text(data.errors.nisn);
						}
						if(data.errors.no_induk != undefined) {
							$("p#pesan_no_induk").addClass("pesan warning");
							$("p#pesan_no_induk").text(data.errors.no_induk);
						}
						if(data.errors.tempat_lahir != undefined) {
							$("p#pesan_tempat_lahir").addClass("pesan warning");
							$("p#pesan_tempat_lahir").text(data.errors.tempat_lahir);
						}
						if(data.errors.tanggal_lahir != undefined) {
							$("p#pesan_tanggal_lahir").addClass("pesan warning");
							$("p#pesan_tanggal_lahir").text(data.errors.tanggal_lahir);
						}
						if(data.errors.bulan_lahir != undefined) {
							$("p#pesan_bulan_lahir").addClass("pesan warning");
							$("p#pesan_bulan_lahir").text(data.errors.bulan_lahir);
						}
						if(data.errors.tahun_lahir != undefined) {
							$("p#pesan_tahun_lahir").addClass("pesan warning");
							$("p#pesan_tahun_lahir").text(data.errors.tahun_lahir);
						}
						if(data.errors.jenis_kelamin != undefined) {
							$("p#pesan_jenis_kelamin").addClass("pesan warning");
							$("p#pesan_jenis_kelamin").text(data.errors.jenis_kelamin);
						}
						if(data.errors.agama != undefined) {
							$("p#pesan_agama").addClass("pesan warning");
							$("p#pesan_agama").text(data.errors.agama);
						}
						if(data.errors.status_dalam_keluarga != undefined) {
							$("p#pesan_status_dalam_keluarga").addClass("pesan warning");
							$("p#pesan_status_dalam_keluarga").text(data.errors.status_dalam_keluarga);
						}
						if(data.errors.anak_ke != undefined) {
							$("p#pesan_anak_ke").addClass("pesan warning");
							$("p#pesan_anak_ke").text(data.errors.anak_ke);
						}
						if(data.errors.alamat_peserta_didik != undefined) {
							$("p#pesan_alamat_peserta_didik").addClass("pesan warning");
							$("p#pesan_alamat_peserta_didik").text(data.errors.alamat_peserta_didik);
						}
						if(data.errors.nomor_telpon_rumah != undefined) {
							$("p#pesan_nomor_telpon_rumah").addClass("pesan warning");
							$("p#pesan_nomor_telpon_rumah").text(data.errors.nomor_telpon_rumah);
						}
						if(data.errors.sekolah_asal != undefined) {
							$("p#pesan_sekolah_asal").addClass("pesan warning");
							$("p#pesan_sekolah_asal").text(data.errors.sekolah_asal);
						}
						if(data.errors.dikelas != undefined) {
							$("p#pesan_dikelas").addClass("pesan warning");
							$("p#pesan_dikelas").text(data.errors.dikelas);
						}
						if(data.errors.pada_tanggal != undefined) {
							$("p#pesan_pada_tanggal").addClass("pesan warning");
							$("p#pesan_pada_tanggal").text(data.errors.pada_tanggal);
						}
						if(data.errors.semester != undefined) {
							$("p#pesan_semester").addClass("pesan warning");
							$("p#pesan_semester").text(data.errors.semester);
						}
						if(data.errors.nama_ayah != undefined) {
							$("p#pesan_nama_ayah").addClass("pesan warning");
							$("p#pesan_nama_ayah").text(data.errors.nama_ayah);
						}
						if(data.errors.nama_ibu != undefined) {
							$("p#pesan_nama_ibu").addClass("pesan warning");
							$("p#pesan_nama_ibu").text(data.errors.nama_ibu);
						}
						if(data.errors.alamat_orang_tua != undefined) {
							$("p#pesan_alamat_orang_tua").addClass("pesan warning");
							$("p#pesan_alamat_orang_tua").text(data.errors.alamat_orang_tua);
						}
						if(data.errors.pekerjaan_ayah != undefined) {
							$("p#pesan_pekerjaan_ayah").addClass("pesan warning");
							$("p#pesan_pekerjaan_ayah").text(data.errors.pekerjaan_ayah);
						}
						if(data.errors.pekerjaan_ibu != undefined) {
							$("p#pesan_pekerjaan_ibu").addClass("pesan warning");
							$("p#pesan_pekerjaan_ibu").text(data.errors.pekerjaan_ibu);
						}
					} else {
						$("p#pesan_jurusan").addClass("pesan warning");
						$("p#pesan_jurusan").text("Siswa gagal ditambahkan!");
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