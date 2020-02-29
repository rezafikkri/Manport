<?php  if(!class_exists("config")) { die; }

	$db = new mapel;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
	$dbK = new kelas;
	// delete cookie
	setcookie('data_mapel', '', time()-3600);
?>

<div class="col-3">
	<div class="home menu_jurusan_mapel cf" id="actionJurusanSiswaDetail">
		<ul>
		<?php
			$dataJurusan = $dbJ->tampil_jurusan();
			if($dataJurusan) :
			foreach($dataJurusan as $j) :
		?>
			<li><a href="dropdownAuto"><?= $j['nama_jurusan']; ?></a>
				<ul class="dropDownJurusanMapel" target-menu="auto">
			<?php  
				$dataKelas = $dbK->tampil_kelas($j['jurusan_id']);
				if($dataKelas) :
				foreach($dataKelas as $k) :
				$arrKelas = explode(".", $k['kelas']);
			?>
				<li><a class="tampilDataMapel" jurusan-id="<?= $j['jurusan_id']; ?>" titleDoc="<?= $arrKelas[0].' '.$j['nama_jurusan'].' '.($arrKelas[1]??''); ?>" kelas-id="<?= $k['kelas_id']; ?>"><?= $k['kelas']; ?></a></li>
				
			<?php endforeach; endif; ?>
				</ul>
			</li>
		<?php endforeach; endif; ?>
		</ul>
	</div><!-- jurusan -->
</div>
<div class="col-9 marginBottom100px">
	<div class="home default cf overflowXAuto">
		<h1 class="judul marginBottom10px">Mata Pelajaran</h1>
		<div class="col-12 nopadding-r-l nopadding-t">
			<p id="nama_jurusan">...</p>
		</div>
		<div class="col-2 nopadding-all marginTop20px">
			<a target="_blank" href="<?= config::base_url('admin/index.php?ref=add_mapel'); ?>" id="add_mapel" class="button green"><span class="fa fa-database"></span></a>
		</div>

		<div class="col-12 nopadding-all">
			<table class="table marginTop15px">
				<tr class="silver">
					<th width="10" colspan="2" align="center">Aksi</th>
					<th>Mata pelajaran</th>
					<th width="10">Kkm</th>					
					<th><a id="sort_mapel_by_berlaku" class="sort">Berlaku <span class="fa fa-sort"></span></a></th>
				</tr>
				<tbody id="tampil_mapel">
				<tr>
					<td width="10"></td>
					<td width="10"></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){

	function tampil_mapel(kelas_id, statusAjax, titleDoc) {
		$.ajax({
			type:"POST",
			url:"mapel/proses.php?action=tampil_mapel",
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

				// set respon in cookie for sorting mapel by berlaku
				document.cookie = "data_mapel="+respon;
				// hide icon sort
				$('a#sort_mapel_by_berlaku span.sorted').removeClass("sorted");

				let data;
				try {
					data = JSON.parse(respon);
				}catch(e){}

				if(data != undefined) {
					let hasil = '';
					let kelompok_sebelum = '';
					data.forEach(function(e){
						if(e.kelompok_mapel != kelompok_sebelum) {
							hasil += '<tr><th colspan="5">Kelompok '+e.kelompok_mapel+'</th></tr>';
							kelompok_sebelum = e.kelompok_mapel;
						}

						hasil += '<tr>';
						hasil += '<td width="10"><a class="delete_mapel" mapel_id="'+e.mata_pelajaran_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
						hasil += '<td width="10"><a href="index.php?ref=edit_mapel&mapel_id='+e.mata_pelajaran_id+'"><span class="fa fa-edit fa-lg"></span></a></td>';
						hasil += '<td>'+e.nama_mapel+'</td>';
						hasil += '<td align="center">'+e.kkm+'</td>';
						hasil += '<td>'+e.berlaku+'</td>';
						hasil += '</tr>';
					})
					$("tbody#tampil_mapel").html(hasil);
				} else {
					$("tbody#tampil_mapel").html('<tr><td class="color_data_kosong" colspan="6">Data kosong</td></tr>');
				}
				// set keterangan di p#nama_jurusan
				$("p#nama_jurusan").text(titleDoc);

				setTimeout(function(){
					$(".progress_bar").css({"width":"0%","transition":"0s"});
				}, 200);				
			}
		})
	}

	// tampil mapel
	$("a.tampilDataMapel").click(function(e){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const jurusan_id = e.currentTarget.getAttribute("jurusan-id");
			const kelas_id = e.currentTarget.getAttribute("kelas-id");
			const titleDoc = e.currentTarget.getAttribute("titleDoc");
			tampil_mapel(kelas_id, statusAjax, titleDoc);
			// set jurusan_id dan kelas_id di p#nama_jurusan
			const pNama_jurusan = document.querySelector("p#nama_jurusan");
			pNama_jurusan.setAttribute("jurusan-id",jurusan_id);
			pNama_jurusan.setAttribute("kelas-id",kelas_id);
			
		}
	});

	// set cookie kelas_id and jurusan_id for add_mapel
	$("a#add_mapel").click(function(){
		const pNama_jurusan = document.querySelector("p#nama_jurusan");
		const jurusan_id = pNama_jurusan.getAttribute("jurusan-id");
		const kelas_id = pNama_jurusan.getAttribute("kelas-id");
		if(jurusan_id && kelas_id) {
			document.cookie ="jurusan_id="+jurusan_id;
			document.cookie ="kelas_id="+kelas_id;
		}
	})

	// delete mapel
	const dataMapel = document.querySelector("tbody#tampil_mapel");
	dataMapel.addEventListener("click", function(e){
		let target = e.target;
		if(!e.target.classList.contains('delete_mapel')) {
			target = e.target.parentElement;
		}

		if(target.classList.contains('delete_mapel')) {
			swal({
				title: "Apakah kamu yakin?",
				text: "Mata pelajaran akan dihapus!",
				showCancelButton: true,
				cancelButtonText:"Batal",
				confirmButtonText: "Ok",
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			},
			function(isConfirm){
				if(isConfirm) {
				    let mapel_id = target.getAttribute("mapel_id");
				    const statusAjax = document.querySelector("statusAjax");
				    const tokenCSRF = document.querySelector("input#tokenCSRF").value;

					if(statusAjax.getAttribute("value") == "yes") {
						$.ajax({
							type:"POST",
							url:"mapel/proses.php?action=delete_mapel",
							data:"tokenCSRF="+tokenCSRF+"&mapel_id="+mapel_id,
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
									// delete data from cookie
									const data_mapel = JSON.parse(get_cookie('data_mapel'));
									data_mapel.forEach(function(e, i){
										if(e.mata_pelajaran_id == mapel_id) {
											data_mapel.splice(i,1);
											return true;
										}
									})
									// update cookie data_mapel
									document.cookie = "data_mapel="+JSON.stringify(data_mapel);
									
									$(target.parentElement.parentElement).remove();
									swal("Selamat", "Mata pelajaran berhasil dihapus!");
								} else {
									swal('Oops','Mata pelajaran gagal dihapus!');
								}
								
								setTimeout(function(){
									$(".progress_bar").css({"width":"0%","transition":"0s"});
								}, 200);
							}
						})
					}
				}
			});
		}
	})

	// get cookie
	function get_cookie(key) {
		let arrCookie = document.cookie.split(';');
		let arr = [];
		arrCookie.forEach(function(e){
			let arrKeyVal = e.split('=');
			arr[arrKeyVal[0]] = arrKeyVal[1];
		});
		return arr[key];
	}

	// sort mapel by berlaku dengan algoritma sorting insertion sort
	$("a#sort_mapel_by_berlaku").click(function(e){
		// generate Operator logic for sorting
		const statusSorted = e.currentTarget.getAttribute("sorted");
		let opLogic;
		if(statusSorted == null || statusSorted == 'a-z') {
			opLogic = '>';
			e.currentTarget.setAttribute("sorted","z-a");
		} else if(statusSorted == 'z-a') {
			opLogic = '<';
			e.currentTarget.setAttribute("sorted","a-z");
		} else {
			return false;
		}

		// parse data_mapel from cookie
		let data_mapel;
		try {
			data_mapel = JSON.parse(get_cookie('data_mapel'));
		} catch(e){}
		
		if(data_mapel != undefined && data_mapel.length > 1) {
			// show icon sort
			$(e.currentTarget.children[0]).addClass("sorted");
			// sort
			for(let i = 0; i < data_mapel.length-1; i++) {
				for(let j = i+1; j > 0; j--) {
					if(opLogic == '>') {
						if(data_mapel[j].berlaku > data_mapel[j-1].berlaku) {
							let dummy = data_mapel[j];
							data_mapel[j] = data_mapel[j-1];
							data_mapel[j-1] = dummy;
						}
					} else if(opLogic == '<') {
						if(data_mapel[j].berlaku < data_mapel[j-1].berlaku) {
							let dummy = data_mapel[j];
							data_mapel[j] = data_mapel[j-1];
							data_mapel[j-1] = dummy;
						}
					}
				}
			}
			// set hasil
			let hasil = '';
			let kelompok_sebelum = '';
			data_mapel.forEach(function(e){
				if(e.kelompok_mapel != kelompok_sebelum) {
					hasil += '<tr><th colspan="5">Kelompok '+e.kelompok_mapel+'</th></tr>';
					kelompok_sebelum = e.kelompok_mapel;
				}

				hasil += '<tr>';
				hasil += '<td width="10"><a class="delete_mapel" mapel_id="'+e.mata_pelajaran_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
				hasil += '<td width="10"><a href="index.php?ref=edit_mapel&mapel_id='+e.mata_pelajaran_id+'"><span class="fa fa-edit fa-lg"></span></a></td>';
				hasil += '<td>'+e.nama_mapel+'</td>';
				hasil += '<td align="center">'+e.kkm+'</td>';
				hasil += '<td>'+e.berlaku+'</td>';
				hasil += '</tr>';
			})
			$("tbody#tampil_mapel").html(hasil);
		} else if(data_mapel.length == 0) {
			$("tbody#tampil_mapel").html('<tr><td width="10"></td><td width="10"></td><td></td><td></td><td></td></tr>');
		}
	})
	
})
</script>