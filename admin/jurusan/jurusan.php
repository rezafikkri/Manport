<?php  if(!class_exists("config")) { die; }

	$db = new jurusan;
	if($db->cekLoginNo_halamanAdmin() === true) die;
?>
<div class="col-10 offset-right-1 offset-left-1">
	<div class="home default jurusan overflowXAuto">
		<h1 class="judul marginBottom30px">Jurusan</h1>
		<a href="<?= config::base_url('admin/index.php?ref=add_jurusan'); ?>" class="button green "><span class="fa fa-database"></span></a>
		
		<table class="table marginTop20px">
			<tr class="silver">
				<th align="center" colspan="2">Aksi</th>
				<th>Nama Jurusan</th>				
			</tr>
			<?php 
				$data = $db->tampil_jurusan(); 
				if($data) :
				foreach($data as $r) :
			?>
			<tr>
				<td width="10"><a class="del" jurusan_id="<?= $r['jurusan_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
				<td width="10"><a href="<?= config::base_url('admin/index.php?ref=edit_jurusan&jurusan_id='.$r['jurusan_id']); ?>"><span class="fa fa-edit fa-lg"></span></a></td>				

				<td><?= $r['nama_jurusan']; ?></td>
			</tr>
			<?php endforeach; else : ?>
			<tr>
				<td></td><td></td><td></td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
</div>
<statusAjax value="yes">
<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
<script type="text/javascript">
$(function(){
	// delete jurusan
	const table = document.querySelector("table");
	table.addEventListener("click", function(e){
		let target = e.target;
		const statusAjax = document.querySelector("statusAjax");
		if(!target.classList.contains('del')) target = e.target.parentElement;

		if(target.classList.contains('del')&&statusAjax.getAttribute("value") == "yes") {
			const jurusan_id = target.getAttribute("jurusan_id");
			const tokenCSRF = document.querySelector("input#tokenCSRF").value;
			$.ajax({
				type:"POST",
				url:"jurusan/proses.php?action=delete_jurusan",
				data:{tokenCSRF:tokenCSRF,jurusan_id:jurusan_id},
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
					try{
						data = JSON.parse(respon);
					} catch(e){}

					if(data != undefined && data.success != undefined) {
						$(target.parentElement.parentElement).remove();
					} else {
						swal("Ooops", "Jurusan gagal dihapus!");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	});

})
</script>