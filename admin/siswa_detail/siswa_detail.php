<?php  if(!class_exists("config")) { die; }

	$dbJ = new jurusan;
	if($dbJ->cekLoginNo_halamanAdmin() === true) die;

	$dbK = new kelas;
	$dataJurusan = $dbJ->tampil_jurusan();
?>
<div class="col-3">
	<div class="home menu_jurusan_siswa_detail cf" id="actionJurusanSiswaDetail">
		<ul>
		<?php
			if($dataJurusan) :
			foreach($dataJurusan as $dj) :
		?>
			<li><a href="dropdownAuto"><?= $dj['nama_jurusan']; ?></a>
				<ul class="dropDownJurusanSiswaDetail" target-menu="auto">
			<?php  
				$dataKelas = $dbK->tampil_kelas($dj['jurusan_id']);
				if($dataKelas) :
				foreach($dataKelas as $dk) :
				$arrKelas = explode(".", $dk['kelas']);
			?>
				<li><a class="tampilDataSiswa" jurusan-id="<?= $dj['jurusan_id']; ?>" titleDoc="<?= $arrKelas[0].' '.$dj['nama_jurusan'].' '.($arrKelas[1]??''); ?>" kelas-id="<?= $dk['kelas_id']; ?>"><?= $dk['kelas']; ?></a></li>
				
			<?php endforeach; endif; ?>
				</ul>
			</li>
		<?php endforeach; endif; ?>
		</ul>
	</div><!-- jurusan -->
</div>

<div class="col-9">
	<div class="home default cf marginBottom100px overflowXAuto">
		<h1 class="judul marginBottom10px">Siswa Detail</h1>
		<div class="col-12 nopadding-all">
			<p id="nama_jurusan">...</p>
		</div>
		
		<div class="col-12 nopadding-b nopadding-r-l marginTop20px">
			<a target="_blank" href="index.php?ref=add_siswa_detail" id="add_siswa_detail" class="button green"><span class="fa fa-database"></span></a>
			<div class="inputCheckboxBtn">
				<div class="inputCheckbox">
					<input type="checkbox" name="checkall" id="checkall">
					<label for="checkall"></label>
				</div>
				<span>Centang Semua</span>
			</div>
			<a id="btnExportPdfSiswaDetail" class="button no_hover marginBottom5px"><span class="fa fa-print"></span></a>
			<span class="badge" id="jmlSiswa">0 Siswa</span>
		</div>

		<div class="col-12 nopadding-all">
			<table class="table marginTop15px">
				<tr class="silver">
					<th width="10" colspan="3" align="center">Aksi</th>
					<th>Nama siswa</th>
					<th>NISN</th>
				</tr>
				<tbody id="tampil_siswa">
				<tr>
					<td width="10"></td>
					<td width="10"></td>
					<td width="10"></td>

					<td></td>
					<td></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $dbJ->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function() {

	function tampil_data_siswa_detail(kelas_id,titleDoc,statusAjax) {
		$.ajax({
			type:"POST",
			url:"siswa_detail/proses.php?action=tampil_siswa_detail",
			data:{kelas_id:kelas_id},
			beforeSend:function() {
				$(".progress_bar_back").show();
				$(".progress_bar").css({"width":"90%","transition":"3s"});
				statusAjax.setAttribute("value","ajax");
			},
			success:function(respon){
				statusAjax.setAttribute("value","yes");
				$(".progress_bar").css({"width":"100%","transition":"1s"});
				$(".progress_bar_back").fadeOut();

				let data;
				try {
					data = JSON.parse(respon);
				}catch(e){}

				if(data != undefined) {
					let hasil = '';
					let jmlSiswa = 0;
					data.forEach(function(e, i){
						jmlSiswa = i+1;
						hasil+= '<tr>';
							hasil+= '<td width="10"><a class="deleteSiswaDetail" siswa-detail-id="'+e.siswa_detail_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
							hasil+= '<td width="10"><a href="index.php?ref=edit_siswa_detail&siswa_detail_id='+e.siswa_detail_id+'"><span class="fa fa-edit fa-lg"></span></a></td>';
							hasil+= '<td width="10"><div class="inputCheckbox"><input type="checkbox"  id="export'+i+'" class="export" value="'+e.siswa_detail_id+'"><label class="export" for="export'+i+'"></label></div></td>';

							hasil+= '<td>'+e.nama_siswa+'</td>';
							hasil+= '<td>'+e.nisn+'</td>';
						hasil+= '</tr>';
					})
					$("tbody#tampil_siswa").html(hasil);
					$("span#jmlSiswa").text(jmlSiswa+' siswa');

				} else {
					$("span#jmlSiswa").text(0+' siswa');
					$("tbody#tampil_siswa").html('<tr style="color: #c3c3c3"><td colspan="5">Data kosong</td></tr>');
				}
				// set keterangan di p#nama_jurusan
				$("p#nama_jurusan").text(titleDoc);
				// uncheck (ceklist semua)
				document.querySelector("input#checkall").checked = false;

				setTimeout(function(){
					$(".progress_bar").css({"width":"0%","transition":"0s"});
				}, 200);
			}
		})
	}

	// tampil data siswa
	const actionJurusan = document.querySelector('#actionJurusanSiswaDetail');
	actionJurusan.addEventListener('click', function(e){
		if(e.target.classList.contains('tampilDataSiswa') == true) {
			const statusAjax = document.querySelector('statusAjax');
			if(statusAjax.getAttribute("value") == "yes") {
				const jurusan_id = e.target.getAttribute('jurusan-id');
				const kelas_id = e.target.getAttribute('kelas-id');
				const titleDoc = e.target.getAttribute('titleDoc');
				tampil_data_siswa_detail(kelas_id,titleDoc,statusAjax);
				// set jurusan_id dan kelas_id di p#nama_jurusan
				const pNama_jurusan = document.querySelector("p#nama_jurusan");
				pNama_jurusan.setAttribute("jurusan-id",jurusan_id);
				pNama_jurusan.setAttribute("kelas-id",kelas_id);
			}
		}
	});

	// set cookie kelas_id and jurusan_id for add siswa detail
	$("a#add_siswa_detail").click(function(e){
		const pNama_jurusan = document.querySelector("p#nama_jurusan");
		const jurusan_id = pNama_jurusan.getAttribute("jurusan-id");
		const kelas_id = pNama_jurusan.getAttribute("kelas-id");
		if(jurusan_id.length != 0 && kelas_id.length != 0) {
			document.cookie ="jurusan_id="+jurusan_id;
			document.cookie ="kelas_id="+kelas_id;
		}
	})

	// delete siswa
	const dataSiswa = document.querySelector("tbody#tampil_siswa");
	dataSiswa.addEventListener('click', function(e){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			let targetDelete = e.target;
			if(targetDelete.classList.contains('deleteSiswaDetail') == false) {
				targetDelete = e.target.parentElement;
			}

			if(targetDelete.classList.contains('deleteSiswaDetail')) {
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
						const siswa_detail_id = targetDelete.getAttribute("siswa-detail-id");
						const tokenCSRF = $("input#tokenCSRF").val();
						$.ajax({
							type:"POST",
							url:"siswa_detail/proses.php?action=delete_siswa_detail",
							data:{tokenCSRF:tokenCSRF, siswa_detail_id:siswa_detail_id},
							beforeSend:function() {
								statusAjax.setAttribute("value","ajax");
							},
							success:function(respon){
								statusAjax.setAttribute("value","yes");

								let data;
								try {
									data = JSON.parse(respon);
								}catch(e){}

								if(data != undefined && data.success != undefined){
									$(targetDelete.parentElement.parentElement).remove();
									const jmlSiswa = $("span#jmlSiswa").text();
									$("span#jmlSiswa").text((parseInt(jmlSiswa)-1)+' siswa');
									swal("Selamat", "Siswa berhasil dihapus!");
								} else {
									swal("Oops", "Siswa gagal dihapus!");
								}
							}
						})
					}
				});
			}
		}
	});

	// print
	const btnExportPdfSiswaDetail = document.querySelector("a#btnExportPdfSiswaDetail");
	btnExportPdfSiswaDetail.addEventListener('click', function(){
		const input = document.querySelectorAll('input:checked.export');
		let arr = [];
		input.forEach(function(e) {
			arr.push(e.value);
		})
		if(arr.length > 0) {
			window.open("siswa_detail/export_siswa_detail.php?siswa_detail_id="+arr, '_blank');
		} else {
			swal('Oops','Mohon pilih siswa yang ingin diexport atau diprint!');
		}
	})

	// checkall
	$("input#checkall").click(function(e){
		const checkExport = document.querySelectorAll("input.export");
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

});
</script>