<?php  if(!class_exists("config")) { die; }

	$db = new identitas_sekolah;
	if($db->cekLoginNo_halamanAdmin() === true) die;
	
	$r = $db->tampil_identitas_sekolah();
?>
<div class="col-12">
	<div class="home default identitas_sekolah overflowXAuto marginBottom100px">
		<h1 class="judul marginBottom30px">Identitas Sekolah</h1>

		<?php if($r) : ?>
			<a href="<?= config::base_url('admin/index.php?ref=edit_identitas_sekolah'); ?>" class="button green"><span class="fa fa-edit"></span></a>
		<?php else : ?>
			<a href="<?= config::base_url('admin/index.php?ref=add_identitas_sekolah'); ?>" class="button green"><span class="fa fa-database"></span></a>
		<?php endif; ?>
		<table class="table marginTop20px">
			<tr class="abu">
				<th width="300">Nama Sekolah</th>
				<td><?= $r['nama_sekolah']??''; ?></td>
			</tr>
			<tr class="abu">
				<th>Alamat</th>
				<td><?= $r['alamat']??''; ?></td>
			</tr>
			<tr class="abu">
				<th>Provinsi</th>
				<td><?= $r['provinsi']??''; ?></td>
			</tr>
			<tr class="abu">
				<th>Logo provinsi</th>
				<td><img src="<?= config::base_url('assets/img/logo/'.$r['logo_prov']??''); ?>" alt="logo provinsi"></td>
			</tr>
			<tr class="abu">
				<th>Kabupaten</th>
				<td><?= $r['kabupaten']??''; ?></td>
			</tr>
			<tr class="abu">
				<th>Lama Belajar</th>
				<td><?= $r['lama_belajar']??''; ?> Tahun</td>
			</tr>
			<tr class="abu">
				<th>Kepala Sekolah</th>
				<td><?= $r['nama_kepala_sekolah']??''; ?></td>
			</tr>
			<tr class="abu">
				<th>NIP</th>
				<td><?= $r['nip_kepala_sekolah']??''; ?></td>
			</tr>
		</table>
	</div>
</div>