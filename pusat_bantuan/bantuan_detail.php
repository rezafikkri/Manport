<?php  if(!class_exists("config")) { die; }

	$db = new bantuan;
	if($db->cekLoginNo_halamanGuru() === true) die;
	
	$pusat_bantuan_id = filter_input(INPUT_GET, 'pusat_bantuan_id', FILTER_SANITIZE_STRING);
	$nama_bantuan = $db->get_one_pusat_bantuan($pusat_bantuan_id)['nama_bantuan']??'';
?>
<div class="col-8 offset-right-2 offset-left-2 marginBottom100px">
	<div class="home default no_box_shadow pusat_bantuan">
		<h1 class="judulPusatBantuan marginBottom20px"><?= str_replace("_", " ", $nama_bantuan); ?></h1>
		<?php
			if(file_exists('pusat_bantuan/'.str_replace(['?'], "", $nama_bantuan).'.php'))  {
				include 'pusat_bantuan/'.str_replace(['?'], "", $nama_bantuan).'.php';
			} 
		?>
	</div>
</div>