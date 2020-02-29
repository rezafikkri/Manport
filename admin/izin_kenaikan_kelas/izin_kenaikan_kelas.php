<?php  if(!class_exists("config")) { die; }

	$db = new izin_kenaikan_kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-6">
	<div class="home default keterangan_izin_kenaikan_kelas">
		<h1>Perizinan menjalankan kenaikan kelas</h1>
		<p>Sebagai patokan pemberian izin menjalankan kenaikan kelas, kamu bisa melihat di halaman home &raquo; Presentase pengisian raport, <span>sebelum memberi izin pastikan persentase sudah mencapai 100</span>.</p>
		<p>Pemberian izin kepada setiap wali kelas untuk menjalankan kenaikan kelas harus dimulai dari jenjang kelas paling atas, <span>dimulai jenjang kelas XII &raquo; XI &raquo; X</span>. Sarat pemberian izin untuk jenjang kelas selanjutnya adalah <span>jika jenjang kelas tersebut telah selesai semuanya menjalankan kenaikan kelas</span>.</p>
		<p>Bagaimana cara mengetahui semua jenjang kelas tersebut telah menjalankan kenaikan kelas? Adalah dengan melihat data wali kelas pada halaman wali kelas. Contoh jika data wali kelas, dijenjang kelas XII sudah tidak ada lagi, maka berarti semua jenjang dikelas XII telah menjalankan kenaikan kelas, dan sudah boleh untuk memberikan izin pada jenjang kelas XI untuk menjalankan kenaikan kelas dan begitu seterusnya.</p>
		<p>Jika semua sudah selesai, maka kembalikan status ke <span>OFF</span> semuanya!.</p>
	</div>
</div>
<div class="col-6">
	<div class="home default overflowXAuto">
		<a id="generate_data_izin_kenaikan_kelas" class="button green"><span class="fa fa-file-o"></span> Hasilkan data</a>
		<table class="table marginTop20px">
			<tr class="silver">
				<th width="10">Aksi</th>
				<th>Kelas</th>
				<th width="10">Status</th>
			</tr>
			<tbody id="tampil_data">
			<?php  
				$data = $db->tampil_izin_kenaikan_kelas();
				if($data) :
				foreach($data as $r) :
			?>
				<tr class="data">
					<td align="center"><a class="deleteIzinkenaikanKelas" izin-kenaikan-kelas-id="<?= $r['izin_kenaikan_kelas_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
					<td><?= $r['kelas']; ?></td>
					<td>
						<label class="switch">
							<input type="checkbox" class="change_status_izin_kenaikan_kelas" id="switchInput<?= $r['kelas']; ?>" value="<?= $r['izin_kenaikan_kelas_id']; ?>" kelas="<?= $r['kelas']; ?>" <?php if($r['status'] == 'on') echo 'checked'; ?>>
							<span class="slider round <?php if($r['status'] == 'on') echo 'checked'; ?>" id="switchSlider<?= $r['kelas']; ?>"></span>
						</label>
					</td>
				</tr>
			<?php endforeach; else : ?>
				<tr><td></td><td></td><td></td></tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	// generate data izin kenaikan_kelas
	$("a#generate_data_izin_kenaikan_kelas").click(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = $("input#tokenCSRF").val();
			$.ajax({
				type:"POST",
				data:{tokenCSRF:tokenCSRF},
				url:"izin_kenaikan_kelas/proses.php?action=generate_data_izin_kenaikan_kelas",
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

					if(data != undefined && data.success != undefined) {
						let hasil = '';
						data.success.forEach(function(e){
							hasil+='<tr class="data">';
							hasil+='<td align="center"><a class="deleteIzinkenaikanKelas" izin-kenaikan-kelas-id="'+e.izin_kenaikan_kelas_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>'
							hasil+='<td>'+e.kelas+'</td>';
							hasil+='<td><label class="switch"><input type="checkbox" id="switchInput'+e.kelas+'" class="change_status_izin_kenaikan_kelas" kelas="'+e.kelas+'" value="'+e.izin_kenaikan_kelas_id+'"><span class="slider round" id="switchSlider'+e.kelas+'"></span></label></td>';
							hasil+='</tr>';
						})
						if(document.querySelector("tr.data") == null) {
							$("tbody#tampil_data").html(hasil);
						} else {
							$("tbody#tampil_data").append(hasil);
						}
					} else {
						swal('Oops','Generate data gagal!');
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})

	const tbody = document.querySelector("tbody#tampil_data");
	tbody.addEventListener('click', function(e){
		let target = e.target;
		const statusAjax = document.querySelector('statusAjax');
		const tokenCSRF = $("input#tokenCSRF").val();
		// change status izin kenaikan kelas
		if(target.classList.contains('change_status_izin_kenaikan_kelas')) {
			if(statusAjax.getAttribute("value") == "yes") {
				let status;
				let text;
				const kelas = target.getAttribute("kelas");
				if(target.checked == true) {
					status = 'on';
					text = 'ON!';
				} else {
					status = 'off';
					text = 'OFF!';
				}
				swal({
					title: "Apakah kamu yakin?",
					text: "Status perizinan jenjang kelas "+kelas+" akan diubah menjadi "+text,
					showCancelButton: true,
					confirmButtonText: "Oke",
					cancelButtonText: "Batal",
					closeOnConfirm: false,
					showLoaderOnConfirm: true,
				},
				function(isConfirm){
					if (isConfirm) {
						const izin_kenaikan_kelas_id = $(target).val();
						$.ajax({
							type:"POST",
							url:"izin_kenaikan_kelas/proses.php?action=change_status_izin_kenaikan_kelas",
							data:{tokenCSRF:tokenCSRF, izin_kenaikan_kelas_id:izin_kenaikan_kelas_id, status:status},
							beforeSend:function() {
								statusAjax.setAttribute("value","ajax");
							},
							success:function(respon) {
								statusAjax.setAttribute("value","yes");

								let data;
								try {
									data = JSON.parse(respon);
								} catch(e) {}

								if(data != undefined && data.success != undefined) {
									swal('Selamat','Status perizinan kenaikan kelas berhasil diubah!');
									if(status == "on") {
										$("span#switchSlider"+kelas).addClass("checked");
									} else {
										$("span#switchSlider"+kelas).removeClass("checked");
									}
								} else {
									swal('Oops','Status perizinan kenaikan kelas gagal diubah!');
									if(status == "on") {
										document.querySelector("input#switchInput"+kelas).checked = false;
									} else {
										document.querySelector("input#switchInput"+kelas).checked = true;
									}
								}
							}
						})
					}
				});
			}
		}
		// delete izin kenaikan kelas
		let targetDelete = e.target;
		if(!targetDelete.classList.contains('deleteIzinkenaikanKelas')) targetDelete = e.target.parentElement;
		if(targetDelete.classList.contains('deleteIzinkenaikanKelas')){
			if(statusAjax.getAttribute("value") == "yes") {
				const izin_kenaikan_kelas_id = targetDelete.getAttribute("izin-kenaikan-kelas-id");
				$.ajax({
					type:"POST",
					url:"izin_kenaikan_kelas/proses.php?action=delete_izin_kenaikan_kelas",
					data:{tokenCSRF:tokenCSRF, izin_kenaikan_kelas_id:izin_kenaikan_kelas_id},
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
							targetDelete.parentElement.parentElement.remove();
						} else {
							swal('Oops','Izin kenaikan kelas gagal dihapus!');
						}

						setTimeout(function(){
							$(".progress_bar").css({"width":"0%","transition":"0s"});
						}, 200);
					}
				})
			}
		}
	})
})
</script>