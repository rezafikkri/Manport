<?php  if(!class_exists("config")) { die; }

	$db = new semester;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-5">
	<div class="home default keterangan_tahun_ajaran">
		<h1>Semester</h1>
		<p><span>Jika semester = 1</span>, dan persentase pengisian raport sudah mencapai 100, maka segera set semester ke semester 2.</p>
		<p><span>Jika semester = 2</span>, dan kenaikan kelas sudah dijalankan semua, Maka semester harus segera di-set ke semester awal, yaitu semester 1!.</p> 
		<p>Caranya adalah dengan meng-klik angka(1 atau 2) pada table semester.</p>
		<p>Bagaimana mengetahui bahwa kenaikan kelas telah dijalankan semua? Adalah dengan melihat data wali kelas dihalaman wali kelas, Jika sudah tidak ada lagi wali kelas maka artinya kenaikan kelas sudah dijalankan semua</p>
	</div>
</div>
<div class="col-7">
	<div class="home default overflowXAuto">
		<h1 class="judul marginBottom20px">Semester</h1>
		<div class="keterangan_check">
			<span class="fa fa-check checkGreen"></span>
			<label class="green">Sedang digunakan</label>
		</div>
		<table class="table">
			<tr class="silver">
				<th>Semester</th>
				<th>Status</th>
			</tr>
			<?php  
				$semester = $db->tampil_semester();
				if($semester) :
				foreach($semester as $r) :
			?>
			<tr>
				<td><a id="make_session_semester" semester_id="<?= $r['semester_id']; ?>"><?= $r['semester']; ?></a></td>
				<td align="center">
					<!-- jika semester sedang digunakan -->
					<?php if($r['status'] == 'yes') : ?>
					<span class="fa fa-check checkGreen"></span>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach; endif; ?>
		</table>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	// make session semester
	$("a#make_session_semester").click(function(e){
		swal({
			title: "Apakah kamu yakin?",
			text: "Semester akan ditetapkan!",
			showCancelButton: true,
			cancelButtonText:"Batal",
			confirmButtonText: "Ok",
			closeOnConfirm: false,
			showLoaderOnConfirm: true
		},
		function(isConfirm){
			if(isConfirm) {
				const semester_id = e.currentTarget.getAttribute("semester_id");
				const tokenCSRF = document.querySelector("input#tokenCSRF").value;
				const statusAjax = document.querySelector("statusAjax");
				if(statusAjax.getAttribute("value") == "yes") {
					$.ajax({
						type:"POST",
						url:"semester/proses.php?action=make_session_semester",
						data:{tokenCSRF:tokenCSRF, semester_id:semester_id},
						beforeSend:function(){
							statusAjax.setAttribute("value","ajax");
						},
						success:function(respon){
							statusAjax.setAttribute("value","yes");

							let data;
							try {
								data = JSON.parse(respon);
							} catch(e){}

							if(data != undefined && data.success != undefined) {
								swal('Selamat', 'Semester berhasil ditetapkan!');
								// menghapus checkGreen, membuat dan set pada semester yang dipilih
								$("table span.checkGreen").remove();
								$(e.currentTarget.parentElement.nextElementSibling).html('<span class="fa fa-check checkGreen"></span>');
								if(data.success.pesan != undefined && data.success.pesan == "yes"){
									$("div.alert").remove();
								} else if(data.success.pesan != undefined && data.success.pesan == "no_yet_session_tahun_ajaran") {
									$("div.alert p").text("Tahun ajaran belum dipilih!");
								}
							} else {
								swal('Oops', 'Semester gagal ditetapkan!');
							}
						}
					})
				}
			}
		});
	})
})
</script>