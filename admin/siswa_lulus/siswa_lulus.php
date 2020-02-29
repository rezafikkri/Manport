<?php  if(!class_exists("config")) { die; }

	$db = new siswa;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
	$dbTA = new tahun_ajaran;
?>
<div class="col-12">
	<div class="home default cf overflowXAuto marginBottom100px">
		<div class="col-12 nopadding-all marginBottom20px">
			<h1 class="judul">Siswa lulus</h1>
		</div>
		<div class="col-12 nopadding-all">
			<div class="col-3 nopadding-b nopadding-l md-nopadding-r-l md-nopadding-b">
				<select name="jurusan" id="jurusan" class="inputFilter" url_tampil_kelas="siswa_lulus/proses.php?action=tampil_kelas">
					<option disabled="" selected="">Jurusan ...</option>
					<?php  
						$dataJurusan = $dbJ->tampil_jurusan();
						if($dataJurusan) :
						foreach($dataJurusan as $r) :
					?>
					<option value="<?= $r['jurusan_id']; ?>"><?= $r['nama_jurusan']; ?></option>
					<?php endforeach; endif; ?>
				</select>
			</div>
			<div class="col-3 nopadding-b nopadding-l md-nopadding-r-l md-nopadding-b">
				<select name="kelas" id="kelas" class="inputFilter">
					<option disabled="" selected="">Kelas ...</option>
				</select>
			</div>
			<div class="col-3 nopadding-b nopadding-l md-nopadding-r-l">
				<select id="tahun" class="inputFilter">
					<option disabled="" selected="">Tahun Kelulusan ...</option>
					<?php
						$tahun_ajaran = $dbTA->tampil_tahun_ajaran(null, null, "tahun");
						if($tahun_ajaran) :
						$arrTahun_awal = explode("-", $tahun_ajaran[0]['tahun']);
						$arrTahun_akhir = explode("-", end($tahun_ajaran)['tahun']);
						$tahun_awal = end($arrTahun_awal);
						$tahun_akhir = end($arrTahun_akhir);
						for($i=$tahun_awal; $i >= $tahun_akhir; $i--) :
					?>
					<option value="<?= ($i-1).'-'.$i; ?>"><?= $i; ?></option>
					<?php endfor; endif; ?>
				</select>
			</div>
			<div class="col-2 nopadding-all marginTop20px">
				<span class="badge m-l-0" id="jmlSiswa">0 Siswa</span>
			</div>
		</div>

		<div class="col-2 col-m-4 nopadding-b nopadding-r-l">
			<div class="inputCheckboxBtn marginRight15px">
				<div class="inputCheckbox">
					<input type="checkbox" name="checkall" id="checkall">
					<label for="checkall"></label>
				</div>
				<span>Ceklist Semua</span>
			</div>
		</div>
		<div class="col-3 nopadding-b nopadding-r-l">
			<a id="btnExportPdfSuratKetLulus" class="button no_hover"><span class="fa fa-print"></span> Surat lulus</a>
		</div>

		<div class="col-12 nopadding-all">
			<table class="table marginTop20px">
				<tr class="silver">
					<th colspan="4" align="center">Aksi</th>
					<th>Nama</th>
					<th>NISN</th>
					<th width="10">Status</th>
				</tr>
				<tbody id="tampil_siswa_lulus">
					<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal_bg"></div>
<div class="modal siswa_lulus">
	<h1></h1>
	<form method="get" target="_blank" id="form" action="siswa_lulus/surat_lulus.php">
		<label class="label">Nomor surat</label>
		<input type="hidden" name="siswa_detail_id">
		<input type="text" name="no_surat" placeholder="..." value="<?= $_SESSION['RAPORT']['no_surat']??''; ?>">

		<a id="closeModal" class="button no_hover">Batal</a>
		<button type="submit" class="button green">Ok</button>
	</form>
</div>

<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	$("#kelas").change(function(){
		let kelas_id = $("#kelas").val();
		let tahun = $("#tahun").val();
		tampil_siswa_lulus(kelas_id,tahun);
	})
	$("#tahun").change(function(){
		let kelas_id = $("#kelas").val();
		let tahun = $("#tahun").val();
		tampil_siswa_lulus(kelas_id,tahun);
	})

	function tampil_siswa_lulus(kelas_id, tahun){
		const statusAjax = document.querySelector('statusAjax');
		if(kelas_id != null && tahun != null && statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"POST",
				url:"siswa_lulus/proses.php?action=tampil_siswa_lulus",
				data:{kelas_id:kelas_id, tahun_ajaran:tahun},
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
						let hasil = '';
						data.forEach(function(e, i){
							hasil+='<tr>';
							hasil+='<td width="10"><a id="deleteSiswaLulus" siswa_detail_id="'+e.siswa_detail_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
							hasil+='<td width="10"><a href="index.php?ref=edit_siswa_lulus&siswa_detail_id='+e.siswa_detail_id+'"><span class="fa fa-edit fa-lg"></span></a></td>';
							hasil+='<td width="150"><a target="_blank" href="siswa_lulus/transkip_nilai.php?siswa_detail_id='+e.siswa_detail_id+'"><span class="fa fa-print fa-lg"></span> Transkip nilai</a></td>';
							hasil+='<td width="10"><div class="inputCheckbox"><input type="checkbox" id="export'+i+'" class="export" value="'+e.siswa_detail_id+'"><label class="export" for="export'+i+'"></label></div></td>';
							hasil+='<td>'+e.nama_siswa+'</td>';
							hasil+='<td>'+e.nisn+'</td>';
							hasil+='<td>'+e.status.replace('_',' ').toUpperCase()+'</td>';
							hasil+='</tr>';
						});
						$("tbody#tampil_siswa_lulus").html(hasil);
						$("span#jmlSiswa").text(data.length+' Siswa');
					// lebih tepat jika opsi jika data kosong diletakkan pada else bukan di catch
					} else {
						$("span#jmlSiswa").text(0+' Siswa');
						$("tbody#tampil_siswa_lulus").html('<tr><td colspan="7" class="color_data_kosong">Data kosong</td></tr>');
					}
					// reset checkAll
					$("input#checkall")[0].checked = false;

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	}

	const tbody = document.querySelector("tbody#tampil_siswa_lulus");
	// delete siswa lulus
	tbody.addEventListener('click', function(e){
		let target = e.target;
		const statusAjax = document.querySelector("statusAjax");
		if(target.getAttribute('id') != 'deleteSiswaLulus') {
			target = e.target.parentElement;
		}
		if(target.getAttribute('id') == 'deleteSiswaLulus' && statusAjax.getAttribute('value') == 'yes') {
			swal({
				title: "Apakah kamu yakin?",
				text: "Siswa akan dihapus!",
				showCancelButton: true,
				cancelButtonText:"Batal",
				confirmButtonText: "Ok",
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			},
			function(isConfirm){
				if(isConfirm) {
					const siswa_detail_id = target.getAttribute('siswa_detail_id');
					const tokenCSRF = document.querySelector('input#tokenCSRF').value;
					$.ajax({
						type:"POST",
						url:"siswa_lulus/proses.php?action=delete_siswa_lulus",
						data:{tokenCSRF:tokenCSRF, siswa_detail_id:siswa_detail_id},
						beforeSend:function() {
							statusAjax.setAttribute("value","ajax");
						},
						success:function(respon) {
							statusAjax.setAttribute("value","yes");

							let data;
							try {
								data = JSON.parse(respon);
							} catch(e){}

							if(data != undefined && data.success != undefined) {
								swal('Selamat','Siswa berhasil dihapus!');
								$(target.parentElement.parentElement).remove();
								$("span#jmlSiswa").text(parseInt($("span#jmlSiswa").text())-1+' Siswa');

							} else {
								swal('Oops','Siswa gagal dihapus!');
							}
						}
					})
				}
			});
		}
	})

	// hide show modal
	$("a#closeModal").click(function(){
		$("div.modal_bg").removeClass("muncul");
		$("div.modal").removeClass("muncul");
	})
	$('button[type="submit"]').click(function(){
		$("div.modal_bg").removeClass("muncul");
		$("div.modal").removeClass("muncul");
	})

	// export surat keterangan lulus
	const btnExportPdfSuratKetLulus = document.querySelector("a#btnExportPdfSuratKetLulus");
	btnExportPdfSuratKetLulus.addEventListener('click', function(){
		const input = document.querySelectorAll('input:checked.export');
		let arr = [];
		input.forEach(function(e) {
			arr.push(e.value);
		})
		if(arr.length > 0) {
			$("input[name='siswa_detail_id']").val(arr);
			$("div.modal_bg").addClass("muncul");
			$("div.modal").addClass("muncul");
		} else {
			swal('Oops','Mohon pilih siswa yang ingin diexport atau diprint surat keterangan lulusnya!');
		}
	})

	// checkall
	$("input#checkall").click(function(e){
		const checkExport = document.querySelectorAll("input[type='checkbox'].export");
		if(e.currentTarget.checked == true) {
			checkExport.forEach(function(e){
				e.checked = true;
			})
		} else {
			checkExport.forEach(function(e){
				e.checked = false;
			})
		}
	})
})
</script>