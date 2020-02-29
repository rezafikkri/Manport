<?php  if(!class_exists("config")) { die; }

	$db = new juara_umum;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbK = new kelas;
?>
<div class="col-12 marginBottom100px">
	<div class="ket_juara_umum">
		<p>Sebelum menentukan juara umum harap pastikan <b>Persentase pengisian raport</b>, sudah mencapai angka 100%.</p>
	</div>
	<div class="home default cf overflowXAuto">
		<h1 class="judul marginBottom5px">Juara Umum</h1>
		<h3 class="judul marginBottom40px"><?= ($_SESSION['RAPORT']['tahun_ajaran']??''); ?>, semester <?= ($_SESSION['RAPORT']['semester']??''); ?></h3>
		<div class="col-2 col-m-4 nopadding-t nopadding-r-l" id="divButtonJuara">
			<a id="tentukan_juara_umum" class="button green"><span class="fa fa-trophy"></span> Juara</a>
		</div>
		<div class="col-3 nopadding-t nopadding-r">
			<div class="inputRadio">
				<input type="radio" name="tipeJuara" value="all" id="all">
				<label for="all">Semua</label>
			</div>
			<div class="inputRadio">
				<input type="radio" name="tipeJuara" value="perjenjang" id="perjenjang">
				<label for="perjenjang">Perjenjang</label>
			</div>
		</div>
		<div class="col-3 nopadding-l nopadding-t display_none" id="kelas">
			<select name="kelas" class="inputFilter">
				<option disabled="" selected="">Kelas ...</option>
				<?php  
					$kelas = $dbK->tampil_kelas(null, null, "kelas");
					if($kelas) :
					$kelasSebelum = '';
					foreach($kelas as $k) :
					$kelas = explode(".", $k['kelas'])[0];
					if($kelas != $kelasSebelum) :
					$kelasSebelum = $kelas;
				?>
				<option value="<?= $k['kelas']; ?>"><?= $k['kelas']; ?></option>
				<?php endif; endforeach; endif; ?>
			</select>
		</div>
		<div class="col-4 nopadding-t nopadding-r-l display_none" id="export_juara_umum">
			<a id="btnExport_juara_umum" target="_blank" href="juara_umum/export_juara_umum.php" class="button no_hover"><span class="fa fa-file-pdf-o"></span></a>
		</div>

		<table class="table marginTop100px">
			<tr class="silver">
				<th width="10">No</th>
				<th width="50">NISN</th>
				<th>Nama</th>
				<th>Kelas</th>
				<th width="100">Jumlah</th>
				<th width="110">Rata-rata</th>
				<th width="10">Juara</th>
			</tr>
			<tbody id="tampil_juara_umum">
				<tr>
					<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" name="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	// tentukan juara umum
	function tentukan_juara_umum(action){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			let tipeJuara = document.querySelector('input[name="tipeJuara"]:checked');
			if(tipeJuara != null) tipeJuara = tipeJuara.value;
			if(tipeJuara == "all") {
				// reset select kelas
				const optionsKelas = document.querySelectorAll('select[name="kelas"] option');
				for(let i=0; i < optionsKelas.length; i++) {
					optionsKelas[i].selected = optionsKelas[i].defaultSelected;
				}
			}
			let kelas = document.querySelector('select[name="kelas"]');			
			if(kelas.selectedOptions[0].disabled == false) {
				kelas = kelas.selectedOptions[0].value;
			} else {
				kelas = null;
			}
			const tokenCSRF = document.querySelector('input[name="tokenCSRF"]').value;
			$.ajax({
				type:"POST",
				url:"juara_umum/proses.php?action="+action,
				data:{tokenCSRF:tokenCSRF, tipeJuara:tipeJuara, kelas:kelas},
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
						let hasil = '';
						data.success.forEach(function(e,i){
							let arrKelas = e.kelas.split(".");
							hasil+='<tr class="data_juara_umum" tipeJuara="'+tipeJuara+'" kelas="'+kelas+'">';
							hasil+='<td align="center">'+(i+1)+'</td>';
							hasil+='<td>'+e.nisn+'</td>';
							hasil+='<td>'+e.nama_siswa+'</td>';
							hasil+='<td>'+arrKelas[0]+' '+e.nama_jurusan+' '+(arrKelas[1]!=null?arrKelas[1]:'')+'</td>';
							hasil+='<td>'+e.jml_nilai+'</td>';
							hasil+='<td>'+e.rata_rata_nilai+'</td>';
							hasil+='<td>'+e.juara+'</td>';
							hasil+='</tr>';
						})
						$("tbody#tampil_juara_umum").html(hasil);
						$("a#tentukan_juara_umum").replaceWith('<a id="reload_juara_umum" class="button green"><span class="fa fa-repeat"></span> Juara</a>');
						if(document.querySelector('div#export_juara_umum').classList.contains('display_none')) {
							$("div#export_juara_umum").removeClass('display_none');
						}
						// change href export juara umum
						$("a#btnExport_juara_umum").attr('href','juara_umum/export_juara_umum.php?tipeJuara='+tipeJuara+'&kelas='+kelas);

					} else if(data != undefined && data.errors != undefined && data.errors.tipeJuara) {
						swal('Oops',data.errors.tipeJuara);

					} else if(data != undefined && data.errors != undefined && data.errors.kelas != undefined) {
						swal('Oops',data.errors.kelas);

					} else if(data != undefined && data != undefined && data.kelas_salah != undefined) {
						swal('Oops','Kelas salah!');

					} else {
						$("tbody#tampil_juara_umum").html('<tr><td colspan="7" class="color_data_kosong">Data kosong</td></tr>');
						$("a#reload_juara_umum").replaceWith('<a id="tentukan_juara_umum" class="button green"><span class="fa fa-trophy"></span> Juara Umum</a>');
						if(!document.querySelector('div#export_juara_umum').classList.contains('display_none')) {
							$("div#export_juara_umum").addClass('display_none');
						}
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	}
	
	const divButtonJuara = document.querySelector("#divButtonJuara");
	divButtonJuara.addEventListener('click', function(e){
		let target = e.target;
		if(target.getAttribute('id') != 'reload_juara_umum' && target.getAttribute('id') != 'tentukan_juara_umum') {
			target = e.target.parentElement;
		}
		
		if(e.target.getAttribute('id') == 'tentukan_juara_umum') {
			tentukan_juara_umum('tentukan_juara_umum');
		} else if(e.target.getAttribute('id') == 'reload_juara_umum') {
			tentukan_juara_umum('reload_juara_umum');
		}
	})

	$('input[name="tipeJuara"]').change(function(e){
		// show hide input kelas
		if(e.currentTarget.value == "perjenjang") {
			$("div#kelas").removeClass("display_none");
		} else {
			$("div#kelas").addClass("display_none");
		}
		
		if($("tr.data_juara_umum").length > 0 && $("tr.data_juara_umum").attr('tipeJuara') == e.currentTarget.value){
			// change button tentukan atau reload juara umum
			$("a#tentukan_juara_umum").replaceWith('<a id="reload_juara_umum" class="button green"><span class="fa fa-repeat"></span> Juara</a>');
			// show hide export juara umum
			if(document.querySelector('div#export_juara_umum').classList.contains('display_none')) {
				$("div#export_juara_umum").removeClass('display_none');
			}
		} else {
			// change button tentukan atau reload juara umum
			$("a#reload_juara_umum").replaceWith('<a id="tentukan_juara_umum" class="button green"><span class="fa fa-trophy"></span> Juara</a>');
			// show hide export juara umum
			if(!document.querySelector('div#export_juara_umum').classList.contains('display_none')) {
				$("div#export_juara_umum").addClass('display_none');
			}
		}
	})

	$('select[name="kelas"]').change(function(e){
		// change button tentukan atau reload juara umum
		if($("tr.data_juara_umum").length > 0 && $("tr.data_juara_umum").attr('kelas') == e.currentTarget.value){
			$("a#tentukan_juara_umum").replaceWith('<a id="reload_juara_umum" class="button green"><span class="fa fa-repeat"></span> Juara</a>');
			// show hide export juara umum
			if(document.querySelector('div#export_juara_umum').classList.contains('display_none')) {
				$("div#export_juara_umum").removeClass('display_none');
			}
		} else {
			$("a#reload_juara_umum").replaceWith('<a id="tentukan_juara_umum" class="button green"><span class="fa fa-trophy"></span> Juara</a>');
			// show hide export juara umum
			if(!document.querySelector('div#export_juara_umum').classList.contains('display_none')) {
				$("div#export_juara_umum").addClass('display_none');
			}
		}
	})
})
</script>