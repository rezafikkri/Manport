<?php  if(!class_exists("config")) { die; }

	$db = new kkm;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-12">
	<div class="home default overflowXAuto">
		<h1 class="judul marginBottom30px">Interval KKM</h1>
		<div class="col-4 nopadding-all marginBottom20px">
			<a class="button red" id="delete"><span class="fa fa-trash-o fa-lg"></span></a>
			<a id="add_kkm" href="<?= config::base_url('admin/index.php?ref=add_kkm'); ?>" class="button green"><span class="fa fa-database"></span></a>
		</div>

		<table class="table">
			<tr class="green">
				<th rowspan="2" width="10" align="center" colspan="2">Aksi</th>
				<th rowspan="2" width="200" align="center">KKM</th>
				<th colspan="4" align="center">Predikat</th>				
			</tr>
			<tr class="green">
				<th width="250" align="center">D = Kurang</th>
				<th width="250" align="center">C = Cukup</th>
				<th width="250" align="center">B = Baik</th>
				<th width="250" align="center">A = Sangat Baik</th>
			</tr>
			<tbody id="tampil_kkm">
				<?php
					$kkm = $db->tampil_kkm();
					if($kkm) :
					$no=1;
					foreach($kkm as $r) :
				?>
				<tr class="jmlTr">
					<td>
						<div class="inputCheckbox">
							<input type="checkbox" name="hapus[]" id="hapus<?= $no; ?>" class="hapus" value="<?= $r['kkm_id']; ?>">
							<label for="hapus<?= $no; ?>"></label>
						</div>
					</td>
					<td align="center"><a href="index.php?ref=edit_kkm&kkm_id=<?= $r['kkm_id']; ?>"><span class="fa fa-edit fa-lg"></span></a></td>

					<td align="center"><?= $r['kkm']; ?></td>
					<td align="center"><?= $r['predikat_d']; ?></td>
					<td align="center"><?= $r['predikat_c']; ?></td>
					<td align="center"><?= $r['predikat_b']; ?></td>
					<td align="center"><?= $r['predikat_a']; ?></td>
				</tr>
				<?php $no++; endforeach; else :?>
				<tr class="color_data_kosong"><td colspan='7'>Data kosong</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div><!-- col-9 -->
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	//delete
	$("a#delete").click(function(){
		let id_arr = [];
		let i = 0;
		$("input.hapus:checked").each(function(){
			id_arr[i] = $(this).val();i++;
		})
		const statusAjax = document.querySelector("statusAjax");
		const tokenCSRF = document.querySelector("input#tokenCSRF").value;

		if(statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"POST",
				url:"kkm/proses.php?action=delete_kkm",
				data:"tokenCSRF="+tokenCSRF+"&kkm_id="+id_arr,
				beforeSend:function(){
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

					if(data != undefined && data.success != undefined){
						$("input.hapus:checked").each(function(){
							$(this).parent().parent().parent().remove();
						})
					} else if(data != undefined && data.dataNull != undefined) {
						swal('Oops','Mohon pilih kkm yang ingin dihapus!');
					} else {
						swal('Oops','Kkm gagal dihapus!');
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	})
})
</script>