<?php  if(!class_exists("config")) { die; }

	$dbLog = new login;
	$dbTA = new tahun_ajaran;
	$dbSem = new semester;
	$dbIS = new identitas_sekolah;
	$identitas_sekolah = $dbIS->tampil_identitas_sekolah("nama_sekolah, alamat");
?>
<div class="conHomeFront <?php if(($_GET['harusLogin']??'') == 'yes') echo 'defaultAfterAnimateHide'; ?>">
	<div class="homeFront homeFrontWhite" id="text-align-center">
		<?php if(!$dbLog->cekLoginYes_forHalamanLoginAdmin()) : ?>
		<a class="btn_show_login"><span class="fa fa-sign-in"></span></a>
		<?php endif; ?>

		<h1 class="marginBottom40px"><?= $identitas_sekolah['nama_sekolah']??'...'; ?></h1>
		<img class="marginBottom20px" src="<?= config::base_url('assets/img/icon/manport.png'); ?>">
		<p class="alamat"><?= $identitas_sekolah['alamat']??'...'; ?></p>
	</div>
	<div class="homeFront homeFrontSilver cf">
		<p class="marginBottom40px ket">Ingin melihat rapot-mu? Isi <span>Nama</span>, <span>NISN</span>, pilih <span>Tahun ajaran</span>, <span>Semester</span> & klik <span>LIHAT!</span>.</p>
		<form id="form">
			<p id="pesan_siswa_detail_id"></p>
			<label class="label">Nama siswa</label>
			<p id="pesan_nama_siswa"></p>
			<input type="text" id="nama_siswa" placeholder="Nama lengkap kamu ..." class="inputFilter">
			<label class="label">NISN</label>
			<p id="pesan_nisn"></p>
			<input type="text" id="nisn" placeholder="NISN kamu ..." class="inputFilter">
			<label class="label">Tahun ajaran</label>
			<p id="pesan_tahun_ajaran"></p>
			<select id="tahun_ajaran" class="inputFilter null_first">
				<option selected="" disabled="">...</option>
				<?php  
					$tahun_ajaran = $dbTA->tampil_tahun_ajaran();
					if($tahun_ajaran) :
					foreach($tahun_ajaran as $ta) :
				?>
				<option value="<?= $ta['tahun_ajaran_id']; ?>"><?= $ta['tahun']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Semester</label>
			<p id="pesan_semester"></p>
			<select id="semester" class="inputFilter null_first">
				<option selected="" disabled="">...</option>
				<?php  
					$semester = $dbSem->tampil_semester();
					if($semester) :
					foreach($semester as $s) :
				?>
				<option value="<?= $s['semester_id'].'.'.$s['semester']; ?>"><?= $s['semester']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<button id="tampil_raport_siswa" class="button green marginTop20px"><span class="fa fa-send"></span> Lihat</button>
		</form>
	</div>
	<div class="homeFront homeFrontWhite no_border_radius no_border_top raport_siswa display_none overflowXAuto">
		<h1 class="judul_divisi">A. Sikap</h1>
		<table class="table table__text-align-justify">
			<tbody id="tampil_sikap">
				<tr>
					<td class="sikap"></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px">B. Capaian Pengetahuan dan Keterampilan</h1>
		<table class="table">
			<tr class="green">
				<th rowspan="2" width="10">No</th>
				<th rowspan="2" width="400">Mata pelajaran</th>
				<th rowspan="2" width="10">KKM</th>
				<th colspan="2">Pengetahuan</th>
				<th colspan="2">Keterampilan</th>
			</tr>
			<tr class="green">
				<th width="100" align="center">Angka</th>
				<th align="center">Predikat</th>

				<th width="100" align="center">Angka</th>
				<th align="center">Predikat</th>
			</tr>
			<tbody id="tampil_nilai">
				<tr>
					<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px">C. Deskripsi pencapaian kompetensi</h1>
		<table class="table table__text-align-justify">
			<tr class="green">
				<th width="10">No</th>
				<th width="400">Mata pelajaran</th>
				<th width="100">Ranah</th>
				<th>Deskripsi</th>
			</tr>
			<tbody id="tampil_deskripsi">
				<tr>
					<td></td><td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px display_none" id="jdlPrakerin">D. Praktik Kerja Industri</h1>
		<table class="table display_none" id="tblPrakerin">
			<tr class="green">
				<th width="10">No</th>
				<th>Mitra DU/DI</th>
				<th>Lokasi</th>
				<th>Lamanya (Bulan)</th>
				<th>Keterangan</th>
			</tr>
			<tbody id="tampil_praktik_kerja_industri">
				<tr>
					<td></td><td></td><td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px">E. Ekstrakurikuler</h1>
		<table class="table">
			<tr class="green">
				<th width="10">No</th>
				<th width="400">Ekstrakurikuler</th>
				<th width="10">Nilai</th>
				<th>Keterangan</th>
			</tr>
			<tbody id="tampil_ekstrakurikuler">
				<tr>
					<td></td><td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px">F. Prestasi</h1>
		<table class="table">
			<tr class="green">
				<th width="10">No</th>
				<th width="400">Jenis prestasi</th>
				<th>Keterangan</th>
			</tr>
			<tbody id="tampil_prestasi">
				<tr>
					<td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px">G. Ketidakhadiran</h1>
		<table class="table">
			<tr class="green">
				<th>Sakit</th>
				<th>Izin</th>
				<th>Tanpa keterangan</th>
				<th>Bolos</th>
			</tr>
			<tbody id="tampil_ketidakhadiran">
				<tr>
					<td></td><td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop20px">H. Catatan Wali kelas</h1>
		<table class="table">
			<tbody id="tampil_catatan_wali_kelas">
				<tr>
					<td class="catatan_wali_kelas"></td>
				</tr>
			</tbody>
		</table>

		<div id="tampil_status_akhir_semester" class="display_none">
			<p class="marginTop40px">Keputusan :</p>
			<p class="marginTop10px">Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>
		</div>
	</div>
	<p class="copyRight marginTop20px">Copyright &copy; <?= date('Y'); ?> Reza Sariful Fikri. All Rights Reserved</p>
</div>

<?php if(!$dbLog->cekLoginYes_forHalamanLoginAdmin()) : ?>
<div class="loginGuru <?php if(($_GET['harusLogin']??'') == 'yes') echo 'defaultAfterAnimateShow display_block'; ?>">
	<form id="form" method="post">
		<div class="loginTop">
			<a class="btn_hide_login"><span class="fa fa-arrow-left"></span></a>

			<h2>MANPORT</h2>
			<p class="command marginTop20px">Silahkan login untuk melakukan pengisian raport siswa!</p>
			<p class="note marginBottom40px">Masukkan <span class="green">Nama</span>, lengkap dengan <span class="green">gelar!</span></p>

			<input type="hidden" id="tokenCSRF" value="<?= $dbLog->generate_tokenCSRF(); ?>">
			<p id="pesan_nama"></p>
			<input type="text" id="nama" placeholder="Nama ...">
			<p id="pesan_password"></p>
			<input type="password" id="password" placeholder="Password ...">

			<button class="button green marginTop20px" id="login"><span class="fa fa-sign-in"></span> Masuk</button>
		</div>
		<p class="copyRight marginTop20px">Copyright &copy; <?= date('Y'); ?> Reza Sariful Fikri. All Rights Reserved</p>
	</form>
</div>
<?php endif; ?>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	<?php if(!$dbLog->cekLoginYes_forHalamanLoginAdmin()) : ?>
	// show hide conHomeFront and loginGuru
	const btn_show_login = document.querySelector("a.btn_show_login");
	const btn_hide_login = document.querySelector("a.btn_hide_login");
	const conHomeFront = document.querySelector("div.conHomeFront");
	const loginGuru = document.querySelector("div.loginGuru");
	const body = document.querySelector("body");

	if(loginGuru.classList.contains('defaultAfterAnimateShow')) {
		body.classList.add("noOverflowY");
	}

	btn_show_login.addEventListener('click', function(){
		conHomeFront.classList.add("hide");
		loginGuru.classList.add("show");
		loginGuru.classList.add("display_block");
		setTimeout(function(){
			loginGuru.classList.replace("show","defaultAfterAnimateShow");
			conHomeFront.classList.replace("hide","defaultAfterAnimateHide");
		},1000);
		body.classList.add("noOverflowY");
	});
	btn_hide_login.addEventListener('click', function(){
		conHomeFront.classList.replace("defaultAfterAnimateHide","show");
		loginGuru.classList.replace("defaultAfterAnimateShow","hide");
		setTimeout(function(){
			loginGuru.classList.remove("hide");
			conHomeFront.classList.remove("show");
			body.classList.remove("noOverflowY");
			loginGuru.classList.remove("display_block");
		},1000);
	});
	<?php endif; ?>

	// proses login
	$("button#login").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $("input#tokenCSRF").val();
			const nama = $("input#nama").val();
			const password = $("input#password").val();
			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");

			$.ajax({
				type:"POST",
				url:"home/proses.php?action=login_guru",
				data:{tokenCSRF:tokenCSRF, nama:nama, password:password},
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
						window.location = "guru/index.php";

					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.nama != undefined) {
							$("p#pesan_nama").addClass("pesan warning");
							$("p#pesan_nama").text(data.errors.nama);
						}
						if(data.errors.password != undefined) {
							$("p#pesan_password").addClass("pesan warning");
							$("p#pesan_password").text(data.errors.password);
						}
						if(data.errors.izin != undefined) {
							$("p#pesan_nama").addClass("pesan warning");
							$("p#pesan_nama").text(data.errors.izin);
						}
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})

	// tampil raport
	$("button#tampil_raport_siswa").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute('value') == "yes") {
			const nama_siswa = document.querySelector('input#nama_siswa').value;
			const nisn = document.querySelector('input#nisn').value;
			const tahun_ajaran_id = $('select#tahun_ajaran').val();
			const semester_id = $("select#semester").val();
			$("p.pesan").text("");
			$("p.pesan").removeClass("pesan warning");
			$.ajax({
				type:"POST",
				url:"home/proses.php?action=tampil_raport",
				data:{nama_siswa:nama_siswa, nisn:nisn, tahun_ajaran_id:tahun_ajaran_id, semester_id:semester_id},
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
					if(data != undefined && data.errors == undefined){
						$("div.raport_siswa").removeClass("display_none");
						$("div.homeFrontSilver").removeClass("marginBottom100px");
					}

					// errors
					if(data != undefined && data.errors != undefined) {
						if(data.errors.nama_siswa != undefined) {
							$("p#pesan_nama_siswa").addClass("pesan warning");
							$("p#pesan_nama_siswa").text(data.errors.nama_siswa);
						}
						if(data.errors.nisn != undefined) {
							$("p#pesan_nisn").addClass("pesan warning");
							$("p#pesan_nisn").text(data.errors.nisn);
						}
						if(data.errors.tahun_ajaran_id != undefined) {
							$("p#pesan_tahun_ajaran").addClass("pesan warning");
							$("p#pesan_tahun_ajaran").text(data.errors.tahun_ajaran_id);
						}
						if(data.errors.semester_id != undefined) {
							$("p#pesan_semester").addClass("pesan warning");
							$("p#pesan_semester").text(data.errors.semester_id);
						}
						if(data.errors.siswa_detail_id != undefined) {
							$("p#pesan_siswa_detail_id").addClass("pesan warning");
							$("p#pesan_siswa_detail_id").text(data.errors.siswa_detail_id);
						}
					}
					// sikap
					if(data != undefined && data.sikap != undefined) {
						$("tbody#tampil_sikap").html('<tr><td class="sikap">'+data.sikap+'</td></tr>');
					} else {
						$("tbody#tampil_sikap").html('<tr><td class="sikap"></td></tr>');
					}
					// nilai
					if(data != undefined && data.nilai_deskripsi != undefined) {
						let hasil = '';
						let kelompok_sebelum = '';
						data.nilai_deskripsi.forEach(function(e, i){
							if(kelompok_sebelum != e.kelompok_mapel) {
								kelompok_sebelum = e.kelompok_mapel;
								hasil+='<tr><th colspan="7">Kelompok '+e.kelompok_mapel+'</th></tr>';
							}
							hasil+='<tr>';
							hasil+='<td align="center">'+(i+1)+'</td>';
							hasil+='<td>'+e.nama_mapel+'</td>';
							hasil+='<td align="center">'+e.kkm+'</td>';
							hasil+='<td align="center">'+e.nilai_p+'</td>';
							hasil+='<td align="center">'+e.predikat_p+'</td>';
							hasil+='<td align="center">'+e.nilai_k+'</td>';
							hasil+='<td align="center">'+e.predikat_k+'</td>';
						})
						$("tbody#tampil_nilai").html(hasil);
					} else {
						$("tbody#tampil_nilai").html('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
					}
					// deskripsi
					if(data != undefined && data.nilai_deskripsi != undefined) {
						let hasil = '';
						let kelompok_sebelum = '';
						data.nilai_deskripsi.forEach(function(e, i){
							if(kelompok_sebelum != e.kelompok_mapel) {
								kelompok_sebelum = e.kelompok_mapel;
								hasil+='<tr><th colspan="7">Kelompok '+e.kelompok_mapel+'</th></tr>';
							}
							hasil+='<tr>';
							hasil+='<td rowspan="2" align="center">'+(i+1)+'</td>';
							hasil+='<td rowspan="2">'+e.nama_mapel+'</td>';
							hasil+='<td>Pengetahuan</td>';
							hasil+='<td>'+e.deskripsi_p+'</td>';
							hasil+='</tr>';
							hasil+='<tr>';
							hasil+='<td>Keterampilan</td>';
							hasil+='<td>'+e.deskripsi_k+'</td>';
							hasil+='</tr>';
						})
						$("tbody#tampil_deskripsi").html(hasil);
					} else {
						$("tbody#tampil_deskripsi").html("<tr><td></td><td></td><td></td><td></td></tr>");
					}
					// praktik kerja industri
					if(data != undefined && data.praktik_kerja_industri != undefined) {
						$("h1#jdlPrakerin").removeClass("display_none");
						$("table#tblPrakerin").removeClass("display_none");
						let hasil = '';
						data.praktik_kerja_industri.forEach(function(e, i){
							hasil+='<tr>';
							hasil+='<td align="center">'+(i+1)+'</td>';
							hasil+='<td>'+e.mitra_du_di+'</td>';
							hasil+='<td>'+e.lokasi+'</td>';
							hasil+='<td>'+e.lamanya+'</td>';
							hasil+='<td>'+e.keterangan+'</td>';
							hasil+='</tr>';
						})
						$("tbody#tampil_praktik_kerja_industri").html(hasil);
					} else {
						$("h1#jdlPrakerin").addClass("display_none");
						$("table#tblPrakerin").addClass("display_none");
					}
					// ekstrakurikuler
					if(data != undefined && data.ekstrakurikuler != undefined) {
						let hasil = '';
						data.ekstrakurikuler.forEach(function(e, i){
							hasil+='<tr>';
							hasil+='<td align="center">'+(i+1)+'</td>';
							hasil+='<td>'+e.nama_ekstrakurikuler+'</td>';
							hasil+='<td align="center">'+e.nilai+'</td>';
							hasil+='<td>'+e.keterangan+'</td>';
							hasil+='</tr>';
						})
						$("tbody#tampil_ekstrakurikuler").html(hasil);
					} else {
						$("tbody#tampil_ekstrakurikuler").html("<tr><td></td><td></td><td></td><td></td></tr>");
					}
					// prestasi
					if(data != undefined && data.prestasi != undefined) {
						let hasil = '';
						data.prestasi.forEach(function(e, i){
							hasil+='<tr>';
							hasil+='<td align="center">'+(i+1)+'</td>';
							hasil+='<td>'+e.jenis_prestasi+'</td>';
							hasil+='<td>'+e.keterangan+'</td>';
							hasil+='</tr>';
						})
						$("tbody#tampil_prestasi").html(hasil);
					} else {
						$("tbody#tampil_prestasi").html("<tr><td></td><td></td><td></td></tr>");
					}
					// ketidakhadiran
					if(data != undefined && data.ketidakhadiran != undefined) {
						let hasil='<tr>';
						hasil+='<td>'+data.ketidakhadiran.sakit+'</td>';
						hasil+='<td>'+data.ketidakhadiran.izin+'</td>';
						hasil+='<td>'+data.ketidakhadiran.tanpa_keterangan+'</td>';
						hasil+='<td>'+data.ketidakhadiran.bolos+'</td>';
						hasil+='</tr>';
						$("tbody#tampil_ketidakhadiran").html(hasil);
					} else {
						$("tbody#tampil_ketidakhadiran").html("<tr><td></td><td></td><td></td><td></td></tr>");
					}
					// catatan wali kelas
					if(data != undefined && data.catatan_wali_kelas != undefined) {
						let hasil='<tr>';
						hasil+='<td class="catatan_wali_kelas">'+data.catatan_wali_kelas+'</td>';
						hasil+='</tr>';
						$("tbody#tampil_catatan_wali_kelas").html(hasil);
					} else {
						$("tbody#tampil_catatan_wali_kelas").html('<tr><td class="catatan_wali_kelas"></td></tr>');
					}
					// status akhir semester
					if(data != undefined && data.status_akhir != undefined) {
						$("div#tampil_status_akhir_semester").removeClass("display_none");
						let hasil = '<p class="marginTop40px">Keputusan :</p><p class="marginTop10px">Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>';
						hasil+=data.status_akhir;
						$("div#tampil_status_akhir_semester").html(hasil);

					} else if(data != undefined && data.no_status_akhir != undefined) {
						$("div#tampil_status_akhir_semester").addClass("display_none");

					} else {
						if(semester_id != null && semester_id.split('.')[1] == 2) {
							$("div#tampil_status_akhir_semester").removeClass("display_none");
							$("div#tampil_status_akhir_semester").html('<p class="marginTop40px">Keputusan :</p><p class="marginTop10px">Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>');
						} else {
							$("div#tampil_status_akhir_semester").addClass("display_none");
						}
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
});
</script>