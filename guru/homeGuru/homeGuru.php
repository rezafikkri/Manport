<?php  if(!class_exists("config")) { die; }

	$db = new siswa;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbK = new kelas;
	$dbJ = new jurusan;
	$dbJK = new juara_kelas;
	$dbIS = new identitas_sekolah;
	$db->cekLoginNo_halamanGuru();

	$kelas = $dbK->get_one_kelas($_SESSION['RAPORT']['kelas_id']);
	$jurusan = $dbJ->get_one_jurusan($_SESSION['RAPORT']['jurusan_id']);
	$lama_belajar = $dbIS->tampil_identitas_sekolah('lama_belajar')['lama_belajar']??'';
?>
<div class="conHomeGuru marginBottom100px">
	<div class="homeGuru">
		<h1><?php 
			$arrKelas = explode(".", $_SESSION['RAPORT']['kelas']);
			echo $arrKelas[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelas[1]??''); 
		?></h1>
		<h2 class="marginTop10px" id="selamatDatang">Selamat datang <span><?= $_SESSION['RAPORT']['nama']; ?></span></h2>
		<h2 id="selamatDatang" class="marginTop5px marginBottom40px">Semester <span><?= $_SESSION['RAPORT']['semester']; ?></span>, Tahun ajaran <span><?= $_SESSION['RAPORT']['tahun_ajaran']; ?></span> </h2>

		<ul>
			<li><a id="cek_nilai_belum_dimasukkan" class="aActiveorNo"><span class="fa fa-check-square-o"></span> Cek nilai belum dimasukkan</a></li>
			<li><a id="juara_kelas" class="aActiveorNo"><span class="fa fa-trophy"></span> Juara kelas</a></li>
			
			<?php if($_SESSION['RAPORT']['semester'] == 2) : ?>
			<li><a id="cek_status_akhir_semester" class="aActiveorNo"><span class="fa fa-check-square-o"></span> cek status akhir belum dimasukkan</a></li>
			<?php endif; ?>

			<li><a target="blank" href="homeGuru/export_serah_terima_raport.php" class="aActiveorNo"><span class="fa fa-print"></span> Serah terima raport</a></li>
			
			<?php if($_SESSION['RAPORT']['semester'] == 2) : ?>
			<li><a id="run_kenaikan_kelas" class="aActiveorNo"><span class="fa fa-send"></span> Jalankan Kenaikan</a></li>
			<li><a id="tampil_siswa_tidak_naik_kelas_tidak_lulus" class="aActiveorNo"><span class="fa fa-group"></span> Siswa <?php if(($lama_belajar == 3 && $_SESSION['RAPORT']['kelas'] >= 'XII') || ($lama_belajar == 4 && $_SESSION['RAPORT']['kelas'] >= 'XIII')): ?>tidak lulus<?php else: ?>tinggal kelas<?php endif; ?></a></li>
			<?php endif; ?>	
		</ul>
	</div>
	<div class="homeGuru tampil_data overflowXAuto">
		<table class="table nohoverTR"></table>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	// cek nilai belum dimasukkan
	$("a#cek_nilai_belum_dimasukkan").click(function(btn){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"GET",
				url:"homeGuru/proses.php?action=cek_nilai_belum_dimasukkan",
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
					} catch(e) {}

					if(data != undefined && data.nilai_belum_dimasukkan != null) {
						let hasil = '<tr class="silver"><th width="10">No</th><th>Nama siswa</th></tr>';
						data.nilai_belum_dimasukkan.forEach(function(e, i){
							hasil+='<tr>';
								hasil+='<td align="center">'+(i+1)+'</td>';
								hasil+='<td><a target="_blank" href="index.php?ref=raport_siswa&siswa_detail_id='+e.siswa_detail_id+'">'+e.nama_siswa+'</a></td>';
							hasil+='</tr>';
						})
						$("table.table").html(hasil);
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");

					} else if(data != undefined && data.mapel_null != undefined) {
						swal('Oops','Data Mata Pelajaran masih kosong, harap beritahu admin!');

					} else {
						$("table.table").html('<tr class="silver"><th width="10">No</th><th>Nama siswa</th></tr><tr><td colspan="2" class="color_data_kosong">Data kosong</td></tr>');
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");
					}
					$("a.reload_juara_kelas").remove();

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
	// tentukan juara kelas
	$("a#juara_kelas").click(function(btn){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $("input#tokenCSRF").val();
			$.ajax({
				type:"POST",
				url:"homeGuru/proses.php?action=juara_kelas",
				data:{tokenCSRF:tokenCSRF},
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
					} catch(e) {}

					if(data != undefined && data.nilai_belum_dimasukkan != undefined) {
						swal('Oops','Nilai siswa belum dimasukkan semua, mohon dicek kembali!');

					} else if(data != undefined && data.juara_kelas != undefined) {
						let hasil = '<tr class="silver"><th width="10">No</th><th>Nama siswa</th><th width="150">Jumlah nilai</th><th width="150">Rata-rata nilai</th><th width="10">Juara</th></tr>';
						data.juara_kelas.forEach(function(e, i) {
							hasil+='<tr>';
							hasil+='<td align="center">'+(i+1)+'</td>';
							hasil+='<td>'+e.nama_siswa+'</td>';
							hasil+='<td>'+e.jml_nilai+'</td>';
							hasil+='<td>'+e.rata_rata_nilai+'</td>';
							hasil+='<td align="center">'+e.juara+'</td>';
							hasil+='</tr>';
						})
						if(document.querySelector("a#reload_juara_kelas") == null) {
							$("table.table").before('<a id="reload_juara_kelas" class="reload_juara_kelas button green"><span class="fa fa-repeat"></span></a>');
							$("table.table").removeClass("marginTop40px");
							$("table.table").addClass("marginTop20px");
						}
						$("table.table").html(hasil);
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");

					} else if(data != undefined && data.mapel_null != undefined) {
						swal('Oops','Data Mata Pelajaran masih kosong, harap beritahu admin!');

					} else {
						$("a#reload_juara_kelas").remove();
						$("table.table").html('<tr class="silver"><th width="10">No</th><th>Nama siswa</th><th width="150">Jumlah nilai</th><th width="150">Rata-rata nilai</th><th width="10">Juara</th></tr><tr><td colspan="5" class="color_data_kosong">Data kosong</td></tr>');
						$("table.table").addClass("marginTop40px");
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
	// reload juara kelas
	const homeGuru = document.querySelector("div.homeGuru.tampil_data");
	homeGuru.addEventListener('click', function(div){
		let target = div.target;
		if(div.target.classList.contains("reload_juara_kelas") == false) {
			target = div.target.parentElement;
		}
		if(target.classList.contains("reload_juara_kelas") == true) {
			const statusAjax = document.querySelector("statusAjax");
			if(statusAjax.getAttribute("value") == "yes") {
				const tokenCSRF = $("input#tokenCSRF").val();
				$.ajax({
					type:"POST",
					url:"homeGuru/proses.php?action=reload_juara_kelas",
					data:{tokenCSRF:tokenCSRF},
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
						} catch(e) {}

						if(data != undefined && data.nilai_belum_dimasukkan != undefined) {
							swal('Oops','Nilai siswa belum dimasukkan semua, mohon dicek kembali!');

						} else if(data != undefined && data.juara_kelas != undefined) {
							let hasil = '<tr class="silver"><th width="10">No</th><th>Nama siswa</th><th width="150">Jumlah nilai</th><th width="150">Rata-rata nilai</th><th width="10">Juara</th></tr>';
							data.juara_kelas.forEach(function(e, i) {
								hasil+='<tr>';
								hasil+='<td align="center">'+(i+1)+'</td>';
								hasil+='<td>'+e.nama_siswa+'</td>';
								hasil+='<td>'+e.jml_nilai+'</td>';
								hasil+='<td>'+e.rata_rata_nilai+'</td>';
								hasil+='<td align="center">'+e.juara+'</td>';
								hasil+='</tr>';
							})
							$("table.table").html(hasil);

						} else if(data != undefined && data.mapel_null != undefined) {
							swal('Oops','Data Mata Pelajaran masih kosong, harap beritahu admin!');

						} else {
							$("table.table").html('<tr class="silver"><th width="10">No</th><th>Nama siswa</th><th width="150">Jumlah nilai</th><th width="150">Rata-rata nilai</th><th width="10">Juara</th></tr><tr><td colspan="5" class="color_data_kosong">Data kosong</td></tr>');
						}

						setTimeout(function(){
							$(".progress_bar").css({"width":"0%","transition":"0s"});
						}, 200);
					}
				})
			}
		}
	})
	// cek_status_akhir_semester
	$("a#cek_status_akhir_semester").click(function(btn){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"GET",
				url:"homeGuru/proses.php?action=cek_status_akhir_belum_dimasukkan",
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
					} catch(e) {}

					if(data != undefined && data != null) {
						let hasil = '<tr class="silver"><th width="10">No</th><th>Nama siswa</th></tr>';
						data.forEach(function(e, i){
							hasil+='<tr>';
								hasil+='<td align="center">'+(i+1)+'</td>';
								hasil+='<td><a target="_blank" href="index.php?ref=raport_siswa&siswa_detail_id='+e.siswa_detail_id+'">'+e.nama_siswa+'</a></td>';
							hasil+='</tr>';
						})
						$("table.table").html(hasil);
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");

					} else {
						$("table.table").html('<tr class="silver"><th width="10">No</th><th>Nama siswa</th></tr><tr><td colspan="2" class="color_data_kosong">Data kosong</td></tr>');
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");
					}
					$("a.reload_juara_kelas").remove();

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
	// run_kenaikan_kelas
	$("a#run_kenaikan_kelas").click(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			swal({
				title: "Apakah kamu yakin?",
				text: "Kelas siswa akan dirubah sesuai status kenaikan kelas yang dimasukkan! <?php if($_SESSION['RAPORT']['level'] != "admin") echo "dan account ini akan dihapus permanen."; ?>",
				type: "input",
				showCancelButton: true,
				cancelButtonText:"Batal",
				closeOnConfirm: false,
				animation: "slide-from-top",
				inputPlaceholder: "Masukkan password ...",
				inputType: "password",
				showLoaderOnConfirm: true,
			},
			function(inputValue){
				if (inputValue === false) return false;
				const tokenCSRF = $("input#tokenCSRF").val();
				statusAjax.setAttribute("value","ajax");
				$.ajax({
					type:"POST",
					url:"homeGuru/proses.php?action=run_kenaikan_kelas",
					data:{tokenCSRF:tokenCSRF, password:inputValue},
					success:function(respon) {
						statusAjax.setAttribute("value","yes");

						let data;
						try {
							data = JSON.parse(respon);
						}catch(e) {}

						if(data != undefined && data.success != undefined) {
							swal('Selamat', 'Kenaikan kelas berhasil dijalankan!');
							$("div.sweet-alert button").remove();
							$("ul.menu_siswa li.daftar_siswa").html("");
							$(".homeGuru.tampil_data table").html("");
							$("h2#selamatDatang span").remove();
							// timer for close
							$('<br><p id="timer">8</p>').insertAfter('div.sweet-alert p[style="display: block;"]');
							const interval = setInterval(function(){
								const angka = $("div.sweet-alert p#timer").text();
								if(angka > 0) {
									$("div.sweet-alert p#timer").text(angka-1);
								} else {
									clearInterval(interval);
									$("div.sweet-alert p#timer").remove();
									window.location = "../index.php";
								}
							}, 1000);

						} else if(data != undefined && data.izin_kenaikan_off != undefined) {
							swal('Oops','Belum diizinkan untuk menjalankan kenaikan kelas!'); } else if(data != undefined && data.password_salah != undefined) {
							swal.showInputError("Password salah!");
						} else if(data != undefined && data.password_null != undefined) {
							swal.showInputError("Password tidak boleh kosong!");
						} else if(data != undefined && data.nilai_belum_dimasukkan != undefined) {
							swal('Oops', 'Nilai siswa belum dimasukkan semua, mohon dicek kembali!');
						} else if(data != undefined && data.status_akhir_belum_dimasukkan != undefined) {
							swal('Oops', 'Status akhir semester siswa belum dimasukkan semua, mohon dicek kembali!');
						} else if(data != undefined && data.mapel_null != undefined) {
							swal('Oops','Data Mata Pelajaran masih kosong, harap beritahu admin!');
						} else {
							swal('Oops','Gagal menjalankan kenaikan kelas!');
						}
					}
				})
			});
		}
	})
	// siswa tidak naik kelas
	$("a#tampil_siswa_tidak_naik_kelas_tidak_lulus").click(function(btn){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute('value') == "yes") {
			$.ajax({
				type:"GET",
				url:"homeGuru/proses.php?action=tampil_siswa_tidak_naik_kelas_tidak_lulus",
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

					if(data != undefined) {
						let hasil = '<tr class="silver"><th width="10">No</th><th>Nama</th><th>Rata-rata nilai</th></tr>';
						data.forEach(function(e,i){
							hasil += '<tr>';
							hasil += '<td>'+(i+1)+'</td>';
							hasil += '<td><a target="_blank" href="index.php?ref=raport_siswa&siswa_detail_id='+e.siswa_detail_id+'">'+e.nama_siswa+'</a></td>';
							hasil += '<td>'+e.rata_rata_nilai+'</td>';
							hasil += '</tr>';
						})
						$("table.table").html(hasil);
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");	
						
					} else {
						$("table.table").html('<tr class="silver"><th width="10">No</th><th>Nama</th><th>Rata-rata nilai</th></tr><tr><td colspan="3" class="color_data_kosong">Data kosong</td></tr>');
						// a active
						$("a.aActiveorNo").removeClass("active");
						btn.currentTarget.classList.toggle("active");
					}
					$("a#reload_juara_kelas").remove();

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
})
</script>