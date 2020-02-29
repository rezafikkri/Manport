<?php  if(!class_exists("config")) { die; }

	$db = new kelas;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
?>
<div class="col-8 offset-right-2 offset-left-2">
	<div class="home default overflowXAuto">
		<h1 class="judul marginBottom30px">Kelas</h1>
		<div class="col-4 nopadding-t nopadding-r-l">
			<a class="button red" id="delete"><span class="fa fa-trash-o fa-lg"></span></a>
			<a class="button green" id="add_kelas" href="<?= config::base_url('admin/index.php?ref=add_kelas'); ?>"><span class="fa fa-database"></span></a>
		</div>
		<div class="col-8 nopadding-r-l nopadding-t">
			<select id="jurusan" class="inputFilter">
				<option disabled="" selected="">Jurusan ...</option>
				<?php 
					$dataJurusan = $dbJ->tampil_jurusan();
					if($dataJurusan) :
					foreach($dataJurusan as $r) :
				?>
				<option value="<?= $r['jurusan_id']; ?>"><?= $r['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
		</div>
		<table class="table marginTop60px">
			<tr class="silver">
				<th width="10" colspan="2" align="center">Aksi</th>
				<th>Kelas</th>
			</tr>
			<tbody id="tampil_kelas">
				<tr><td width="10"></td><td width="10"></td><td></td></tr>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){

	$("#delete").click(function(){
		let i = 0;
		let id_kelas = [];
		$("input.hapus:checked").each(function(){
			id_kelas[i] = $(this).val();i++;
		})
		const statusAjax = document.querySelector("statusAjax");
		const tokenCSRF = $("#tokenCSRF").val();

		if(statusAjax.getAttribute('value') == "yes") {
			$.ajax({
				type:"POST",
				url:"kelas/proses.php?action=delete_kelas",
				data:"tokenCSRF="+tokenCSRF+"&kelas_id="+id_kelas,
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
						swal('Oops','Mohon pilih kelas yang ingin dihapus!');	
					} else {
						swal("Ooops", "Kelas gagal dihapus!");
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
		}
	});

	// tampil kelas
	const jurusan = document.querySelector("select#jurusan");
	if(jurusan != undefined) {
		jurusan.addEventListener('change', function(e){
			const statusAjax = document.querySelector("statusAjax");
			const jurusan_id = e.target.value;

			if(statusAjax.getAttribute("value") == "yes") {
			$.ajax({
				type:"POST",
				url:"kelas/proses.php?action=tampil_kelas",
				data:{jurusan_id:jurusan_id},
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

					if(data != undefined) {
						let hasil = '';
						data.forEach(function(d, i){
							hasil+= '<tr class="jmlTr">';
							hasil+= '<td width="10"><div class="inputCheckbox"><input type="checkbox" name="hapus[]" class="hapus" id="hapus'+i+'" value="'+d.kelas_id+'"><label for="hapus'+i+'"></label></div></td>';
							hasil+= '<td align="center" width="10"><a href="index.php?ref=edit_kelas&kelas_id='+d.kelas_id+'"><span class="fa fa-edit fa-lg"></span></a></td>';

							hasil+= '<td>'+d.kelas+'</td>';
							hasil+= '</tr>';
						});
						$("tbody#tampil_kelas").html(hasil);
					} else {
						$("tbody#tampil_kelas").html('<tr><td colspan="3" class="color_data_kosong">Data kosong</td></tr>');
					}

					setTimeout(function(){
						$(".progress_bar").css({"width":"0%","transition":"0s"});
					}, 200);
				}
			})
			}
		});
	}

	// set jurusan_id to cookie for add jurusan
	$("a#add_kelas").click(function(){
		const jurusan_id = $("select#jurusan").val();
		if(jurusan_id) {
			document.cookie = "jurusan_id="+jurusan_id;
		}
	})
})
</script>