<?php  if(!class_exists("config")) { die; }

	$db = new wali_kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$wali_kelas = $db->tampil_wali_kelas();
?>
<div class="col-12">
	<div class="home default overflowXAuto">
		<h1 class="judul marginBottom30px">Wali Kelas</h1>
		<a href="<?= config::base_url('admin/index.php?ref=add_wali_kelas'); ?>" class="button green"><span class="fa fa-database"></span></a>
		<span class="badge" id="jmlSiswa"><?= count($wali_kelas??[]); ?> Wali kelas</span>
		
		<table class="table marginTop20px">
			<tr class="silver">
				<th align="center" colspan="2">Aksi</th>
				<th>Wali kelas</th>
				<th>Kelas</th>
				<th>Jurusan</th>				
			</tr>
			<?php  
				if($wali_kelas) :
				foreach($wali_kelas as $r) :
			?>
			<tr class="jmlTr">
				<td width="10"><a id="delete" wali_kelas_id="<?= $r['wali_kelas_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
				<td width="10"><a href="<?= config::base_url('admin/index.php?ref=edit_wali_kelas&wali_kelas_id='.$r['wali_kelas_id']); ?>"><span class="fa fa-edit fa-lg"></span></a></td>

				<td><?= $r['nama']; ?></td>
				<th><?= $r['kelas']; ?></th>
				<td><?= $r['nama_jurusan']; ?></td>
			</tr>
			<?php endforeach; else : ?>
			<tr>
				<td colspan="5" class="color_data_kosong">Data kosong</td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	$("a#delete").click(function(e){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const tokenCSRF = document.querySelector("input#tokenCSRF").value;
			const wali_kelas_id = e.currentTarget.getAttribute("wali_kelas_id");

			swal({
				title: "Apakah kamu yakin?",
				text: "Wali kelas akan dihapus!",
				showCancelButton: true,
				cancelButtonText:"Batal",
				confirmButtonText: "Ok",
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			},
			function(isConfirm){
				if (isConfirm) {
					$.ajax({
						type:"POST",
						url:"wali_kelas/proses.php?action=delete_wali_kelas",
						data:{tokenCSRF:tokenCSRF,wali_kelas_id:wali_kelas_id},
						beforeSend:function() {
							statusAjax.setAttribute("value","ajax");
						},
						success:function(respon) {
							statusAjax.setAttribute("value","yes");

							let data;
							try {
								data = JSON.parse(respon);
							}catch(e){}

							if(data != undefined && data.success != undefined) {
								swal('Selamat','Wali kelas berhasil dihapus!');
								$(e.currentTarget.parentElement.parentElement).remove();
								// change ket jml siswa
								$("span#jmlSiswa").text(document.querySelectorAll("tr.jmlTr").length+" Wali kelas");
							} else {
								swal("Oops", "Wali kelas gagal dihapus!");
							}
						}
					})

				}
			});
		}
	})
})
</script>