<?php  if(!class_exists("config")) { die; }

	$db = new siswa;  
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
	$dbK = new kelas;
	$siswa_keluar = $db->tampil_siswa_detail(null,'keluar','s.siswa_detail_id, s.nama_siswa, s.nisn, k.kelas, j.nama_jurusan','JOIN kelas as k ON k.kelas_id=s.kelas_id JOIN jurusan as j ON j.jurusan_id=k.jurusan_id');
?>
<div class="col-12">
	<div class="home default cf marginBottom100px overflowXAuto">
		<div class="col-12 nopadding-all marginBottom20px">
			<h1 class="judul">Siswa keluar</h1>
		</div>
		
		<div class="col-2 nopadding-all marginTop20px">
			<span class="badge m-l-0" id="jmlSiswa"><?= count($siswa_keluar??[]); ?> Siswa</span>
		</div>
		<div class="col-5 nopadding-r-l nopadding-b">
			<select name="jurusan" class="inputFilter" url_tampil_kelas="siswa_keluar/proses.php?action=tampil_kelas">
				<option selected="" disabled="">Jurusan ...</option>
				<?php  
					$dataJurusan = $dbJ->tampil_jurusan();
					if($dataJurusan) :
					foreach($dataJurusan as $dj) :
				?>
				<option value="<?= $dj['jurusan_id']; ?>" nama_jurusan="<?= $dj['nama_jurusan']; ?>"><?= $dj['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
		</div>
		<div class="col-5 nopadding-r nopadding-b md-nopadding-r-l md-nopadding-b">
			<select name="kelas" class="inputFilter">
				<option selected="" disabled="">Kelas ...</option>
			</select>
		</div>

		<div class="col-12 nopadding-all">
			<table class="table marginTop20px">
				<tr class="silver">
					<th colspan="3" align="center">Aksi</th>
					<th>Nama siswa</th>
					<th>NISN</th>
					<th>Kelas</th>
				</tr>
				<tbody id="tampil_siswa_keluar">
				<?php  
					if($siswa_keluar) :
					foreach($siswa_keluar as $r) :
					$arrKelas = explode(".", $r['kelas']);
				?>
				<tr>
					<td width="10"><a class="deleteSiswaKeluar" siswa-detail-id="<?= $r['siswa_detail_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
					<td width="10"><a href="index.php?ref=edit_siswa_keluar&siswa_detail_id=<?= $r['siswa_detail_id']; ?>"><span class="fa fa-edit fa-lg"></span></a></td>
					<td width="150"><a class="surat_keluar_masuk" siswa_detail_id="<?= $r['siswa_detail_id']; ?>"><span class="fa fa-print fa-lg"></span> Surat keluar</a></td>

					<td><?= $r['nama_siswa']; ?></td>
					<td><?= $r['nisn']; ?></td>
					<td><?= $arrKelas[0].' '.$r['nama_jurusan'].' '.($arrKelas[1]??''); ?></td>
				</tr>
				<?php endforeach; else: ?>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal_bg"></div>
<div class="modal siswa_keluar">
	<form id="form" action="siswa_keluar/surat_keluar_masuk.php" method="get" target="_blank">
		<input type="hidden" name="siswa_detail_id">
		<label class="label">Sebab keluar dan Atas permintaan</label>
		<textarea spellcheck="false" rows="5" name="sebab_keluar_atas_permintaan" placeholder="..."></textarea>

		<a id="closeModal" class="button no_hover">Batal</a>
		<button type="submit" id="btnSuratSiswaKeluarMasuk" class="button green">Ok</button>
	</form>
</div>

<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	// tampil siswa keluar
	$('select[name="kelas"]').change(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const kelas_id = $(this).val();
			$.ajax({
				type:"POST",
				url:"siswa_keluar/proses.php?action=tampil_siswa_keluar",
				data:{kelas_id:kelas_id},
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
					let jmlSiswa = 0;
					try {
						data = JSON.parse(respon);
						jmlSiswa = data.length;
					} catch(e){}

					if(data != undefined) {
						let hasil = '';
						data.forEach(function(e){
							let arrKelas = e.kelas.split('.');
							let kelasLast = (arrKelas[1]!=undefined)?arrKelas[1]:'';
							let titleDoc = arrKelas[0]+' '+e.nama_jurusan+' '+kelasLast;

							hasil+='<tr>';
							hasil+='<td width="10"><a class="deleteSiswaKeluar" siswa-detail-id="'+e.siswa_detail_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
							hasil+='<td width="10"><a href="index.php?ref=edit_siswa_keluar&siswa_detail_id='+e.siswa_detail_id+'"><span class="fa fa-edit fa-lg"></span></a></td>';
							hasil+='<td width="150"><a class="surat_keluar_masuk" siswa_detail_id="'+e.siswa_detail_id+'"><span class="fa fa-print fa-lg"></span> Surat keluar</a></td>';
							hasil+='<td>'+e.nama_siswa+'</td>';
							hasil+='<td>'+e.nisn+'</td>';
							hasil+='<td>'+titleDoc+'</td>';
							hasil+='</tr>';
						})
						$("tbody#tampil_siswa_keluar").html(hasil);
					} else {
						$("tbody#tampil_siswa_keluar").html('<tr><td colspan="6" class="color_data_kosong">Data kosong</td></tr>');
					}

					$("span#jmlSiswa").html(jmlSiswa+' Siswa');

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})

	const tbody = document.querySelector("tbody#tampil_siswa_keluar");
	tbody.addEventListener('click', function(e){
		// show modal
		let targetShow = e.target;
		if(targetShow.classList.contains('surat_keluar_masuk') == false) {
			targetShow = e.target.parentElement;
		}
		if(targetShow.classList.contains('surat_keluar_masuk') == true) {
			$('input[name="kelas_id"]').val(targetShow.getAttribute("kelas_id"));
			$('input[name="siswa_detail_id"]').val(targetShow.getAttribute("siswa_detail_id"));
			$(".modal_bg").addClass("muncul");
			$(".modal").addClass("muncul");
			$("form#form")[0].reset();
		}

		// delete siswa
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			let targetDel = e.target;
			if(targetDel.classList.contains('deleteSiswaKeluar') == false) {
				targetDel = e.target.parentElement;
			}
			if(targetDel.classList.contains('deleteSiswaKeluar') == true) {
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
					if (isConfirm) {
						const siswa_detail_id = targetDel.getAttribute("siswa-detail-id");
						const tokenCSRF = $("input#tokenCSRF").val();
						$.ajax({
							type:"POST",
							url:"siswa_keluar/proses.php?action=deleteSiswaKeluar",
							data:{tokenCSRF, siswa_detail_id},
							beforeSend:function() {
								statusAjax.setAttribute("value","ajax");
							},
							success:function(respon){
								statusAjax.setAttribute("value","yes");

								let data;
								try {
									data = JSON.parse(respon);
								} catch(e){}

								if(data != undefined && data.success != undefined) {
									swal('Selamat','Siswa berhasil dihapus!');
									const jmlSiswa = document.querySelector("span#jmlSiswa");
									const jmlSiswaNew = parseInt(jmlSiswa.textContent.split('')[0])-1;
									jmlSiswa.textContent = jmlSiswaNew+' Siswa';
									$(targetDel.parentElement.parentElement).remove();
									
								} else {
									swal('Oops','Siswa gagal dihapus!');
								}
							}
						})
					}
				});
			}
		}
	})

	// hide modal
	$("button#btnSuratSiswaKeluarMasuk").click(function(){
		$(".modal_bg").removeClass("muncul");
		$(".modal").removeClass("muncul");
	})
	$("a#closeModal").click(function(e){
		$(".modal_bg").removeClass("muncul");
		$(".modal").removeClass("muncul");
		$("form#form")[0].reset();
	})
})
</script>