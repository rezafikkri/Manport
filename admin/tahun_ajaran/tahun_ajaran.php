<?php  if(!class_exists("config")) { die; }

	$db = new tahun_ajaran;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$tahun_ajaran = $db->tampil_tahun_ajaran(10);
?>
<div class="col-5">
	<div class="home default keterangan_tahun_ajaran">
		<h1>Tahun Ajaran</h1>
		<p><span>Jika kenaikan kelas sudah dijalankan semua, Maka tahun ajaran harus segera di-ubah ke tahun ajaran berikutnya!</span>.</p> 
		<p>Caranya adalah dengan meng-klik angka tahun ajaran(2019-2020, ...) pada table tahun ajaran.</p>
		<p>Bagaimana mengetahui bahwa kenaikan kelas telah dijalankan semua? Adalah dengan melihat data wali kelas dihalaman wali kelas, Jika sudah tidak ada lagi wali kelas maka artinya kenaikan kelas sudah dijalankan semua.</p>
	</div>
</div>
<div class="col-7">
	<div class="home default marginBottom100px overflowXAuto">
		<div class="col-4 col-m-4 nopadding-all">
			<a id="delete" class="button red"><span class="fa fa-trash-o"></span></a>
			<a class="button green" href="<?= config::base_url('admin/index.php?ref=add_tahun_ajaran'); ?>"><span class="fa fa-database"></span></a>
		</div>
		<div class="col-8 col-m-8 nopadding-all">
			<div class="keterangan_check">
				<span class="fa fa-check checkGreen"></span>
				<label class="green">Sedang digunakan</label>
			</div>
			<div class="keterangan_check">
				<span class="fa fa-check checkBlue"></span>
				<label class="blue">Sudah digunakan</label>
			</div>
		</div>

		<table class="table marginBottom20px marginTop60px">
			<tr class="silver">
				<th width="10">Aksi</th>
				<th>Tahun ajaran</th>
				<th width="10" align="center">Status</th>
			</tr>
			<tbody id="tampil_tahun_ajaran" class="makeSessionTahunAjaran">
			<?php  
				if($tahun_ajaran) :
				$no = 1;
				foreach($tahun_ajaran as $r) :
			?>
				<tr class="jmlTr">
					<td align="center">
						<div class="inputCheckbox">
							<input type="checkbox" name="hapus[]" class="hapus" id="hapus<?= $no; ?>" value="<?= $r['tahun_ajaran_id']; ?>">
							<label class="checkboxHapus <?php if($r['status']=='run') : ?>display_none<?php endif; ?>" for="hapus<?= $no; ?>"></label>
						</div>
					</td>
					<td><a tahun-ajaran-id="<?= $r['tahun_ajaran_id']; ?>" class="make_session_tahun_ajaran"><?= $r['tahun']; ?></a></td>

					<?php if($r['status'] == 'run') : ?>
					<td align="center"><span class="fa fa-check checkGreen"></span></td>
					<?php elseif($r['status'] == 'yes') : ?>
					<td align="center"><span class="fa fa-check checkBlue"></span></td>
					<?php else : ?>
					<td align="center"></td>
					<?php endif; ?>
				</tr>
			<?php $no++; endforeach; else : ?>
				<tr><td></td><td></td><td></td></tr>
			<?php endif; ?>
			</tbody>
		</table>

		<center id="dataEmpty"></center>
		<center><a id="readMore" class="button no_hover"><span class="fa fa-arrow-down"></span></a></center>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){

	$("#delete").click(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			let tahun_ajaran_id = [],
			i = 0;
			$("input.hapus:checked").each(function(){
				tahun_ajaran_id[i] = $(this).val(); i++;
			})
			const tokenCSRF = document.querySelector("input#tokenCSRF").value;

			$.ajax({
				type:"POST",
				url:"tahun_ajaran/proses.php?action=delete_tahun_ajaran",
				data:"tokenCSRF="+tokenCSRF+"&tahun_ajaran_id="+tahun_ajaran_id,
				beforeSend:function(){
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
					} catch(e){}

					if(data != undefined && data.success != undefined) {
						$("input.hapus:checked").each(function(){
							$(this).parent().parent().parent().remove();
						})
					} else if(data != undefined && data.dataNull != undefined) {
						swal('Oops','Mohon pilih tahun ajaran yang ingin dihapus!');
					} else {
						swal('Oops','Tahun ajaran gagal dihapus!');
					}
					
					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})

	/* make session tahun ajaran */
	const button = document.querySelector(".makeSessionTahunAjaran");
	if(button != null) {
		button.addEventListener('click', function(e){
			if(e.target.classList.contains("make_session_tahun_ajaran")) {
				swal({
					title: "Apakah kamu yakin?",
					text: "Tahun ajaran akan ditetapkan!",
					showCancelButton: true,
					cancelButtonText:"Batal",
					confirmButtonText: "Ok",
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				},
				function(isConfirm){
					if (isConfirm) {
						const tahun_ajaran_id = e.target.getAttribute('tahun-ajaran-id');
						const statusAjax = document.querySelector("statusAjax");
						const tokenCSRF = document.querySelector("input#tokenCSRF").value;

						if(statusAjax.getAttribute("value") == "yes") {
							$.ajax({
								type:"POST",
								url:"tahun_ajaran/proses.php?action=makeSession_TahunAjaran",
								data:{tokenCSRF:tokenCSRF,tahun_ajaran_id:tahun_ajaran_id},
								beforeSend:function(){
									statusAjax.setAttribute("value","ajax");
								},
								success:function(respon){
									statusAjax.setAttribute("value","yes");

									let data;
									try {
										data = JSON.parse(respon);
									} catch(e) {}

									if(data != undefined && data.success != undefined) {
										if(data.success.tahun_sebelum == 'dataNull') {
											swal('Selamat','Tahun ajaran berhasil ditetapkan!');
										} else {
											swal('Selamat','Tahun ajaran berhasil diubah dari '+data.success.tahun_sebelum+' ke '+data.success.tahun_sekarang+'!');
										}
										if(data.success.pesan != undefined && data.success.pesan == "yes") {
											$("div.alert").remove();
										} else if(data.success.pesan != undefined && data.success.pesan == "no_yet_session_semester") {
											$("div.alert p").text("Semester belum diplih!");
										}
										// menghapus checkGreen, membuat baru checkGreen dan set pada tahun ajaran yang dipilih
										$("table span.checkGreen").replaceWith('<span class="fa fa-check checkBlue"></span>');
										$("table label.checkboxHapus").removeClass("display_none");
										$(e.target.parentElement.nextElementSibling).html('<span class="fa fa-check checkGreen"></span>');
										e.target.parentElement.previousElementSibling.querySelector("label.checkboxHapus").classList.add("display_none");
									} else {
										swal('Oops','Tahun ajaran gagal ditetapkan!');
									}
								}
							})
						}
					}
				});
			}
		})
	}

	/* readMore */
	const readMore = document.querySelector("a#readMore");
	if(readMore != null) {
		readMore.addEventListener("click", function(){
			const statusAjax = document.querySelector("statusAjax");
			if(statusAjax.getAttribute("value") == "yes" && $("#dataEmpty").text().length == 0) {
				const offset = document.querySelectorAll("table tr.jmlTr").length;
				const pesan_dataEmpty = document.querySelector("h3#pesan_dataEmpty");

				$.ajax({
					type:"POST",
					url:"tahun_ajaran/proses.php?action=tampil_tahun_ajaran",
					data:{offset:offset},
					beforeSend:function(){
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
						} catch(e){}

						if(data != undefined) {
							let hasil = '';
							data.forEach(function(e, i){
								hasil += '<tr class="jmlTr">';
									// jika tahun ajaran sedang digunakan
									if(e.status == 'run') {
										hasil += '<td align="center"><div class="inputCheckbox"><input type="checkbox" name="hapus[]" class="hapus" id="hapus'+(offset+i+1)+'" value="'+e.tahun_ajaran_id+'"><label class="checkboxHapus display_none" for="hapus'+(offset+i+1)+'"></label></div></td>';
									} else {
										hasil += '<td align="center"><div class="inputCheckbox"><input type="checkbox" name="hapus[]" class="hapus" id="hapus'+(offset+i+1)+'" value="'+e.tahun_ajaran_id+'"><label class="checkboxHapus" for="hapus'+(offset+i+1)+'"></label></div></td>';
									}
									hasil += '<td><a tahun-ajaran-id="'+e.tahun_ajaran_id+'" tahun="'+e.tahun+'" class="make_session_tahun_ajaran">'+e.tahun+'</a></td>';
									if(e.status == 'run') {
										hasil += '<td align="center"><span class="fa fa-check checkGreen"></span></td>';
									} else if(e.status == 'yes') {
										hasil += '<td align="center"><span class="fa fa-check checkBlue"></span></td>';	
									} else {
										hasil += '<td align="center"></td>';
									}
								hasil += '</tr>';
							})
							$("tbody#tampil_tahun_ajaran").append(hasil);

						} else {
							$("center#dataEmpty").html('<h3 id="pesan_dataEmpty" class="color_data_kosong marginBottom20px">Data habis!</h3>');
							setTimeout(function(){
								$("center#dataEmpty").html("");
							}, 2000);
						}

						setTimeout(function(){
							$(".progress_bar").css({"width":"0%","transition":"0s"});
						}, 200);
					}
				})
			}
		});
	}
})
</script>