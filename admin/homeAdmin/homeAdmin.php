<?php if(!class_exists("config")) { die; }

	$db = new login;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbS = new siswa;
	$dbIS = new identitas_sekolah;
	$dbK = new kelas;

	$is = $dbIS->tampil_identitas_sekolah();
	$jml_siswa = $dbS->count_jml_siswa("masih_sekolah");
	$jml_kelas = $dbK->count_jml_kelas();

?>
<div class="col-12 nopadding-all">
	<div class="col-7">
		<div class="home default overflowXAuto">
			<h1 class="judul">Identitas Sekolah</h1>
			<table class="table marginTop20px">
				<tr class="abu">
					<th width="200">Nama Sekolah</th>
					<td><?= $is['nama_sekolah']??''; ?></td>
				</tr>
				<tr class="abu">
					<th>Alamat</th>
					<td><?= $is['alamat']??''; ?></td>
				</tr>
				<tr class="abu">
					<th>Kepala Sekolah</th>
					<td><?= $is['nama_kepala_sekolah']??''; ?></td>
				</tr>
				<tr class="abu">
					<th>NIP</th>
					<td><?= $is['nip_kepala_sekolah']??''; ?></td>
				</tr>
				<tr class="abu">
					<th>Lama Belajar</th>
					<td><?= $is['lama_belajar']??''; ?> Tahun</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="col-5">
		<div class="persentase_pengisian_raport">
			<h1 align="left" class="judul">Persentase pengisian raport</h1>
			<div id="cont"><span class="bigger">0</span><span class="smaller">%</span></div>
			<svg id="svg" viewPort="0 0 100 100" version="1.1">
				<circle id="bg" r="100" cx="110" cy="110" fill="transparent" stroke-dasharray="628.31"></circle>
				<circle id="bar" r="100" cx="110" cy="110" fill="transparent" stroke-dasharray="628.31"></circle>
			</svg>
		</div>
	</div>
</div>

<div class="col-4">
	<div class="home default cart green">
		<h1 class="judul marginBottom10px">Siswa Aktif</h1>
		<img src="<?= config::base_url('assets/img/icon/siswa icon.svg'); ?>">
		<h3 class="judul">Jumlah - <?= $jml_siswa; ?></h3>
	</div>
</div>
<div class="col-4">
	<div class="home default cart blue">
		<h1 class="judul marginBottom10px">Kelas</h1>
		<img src="<?= config::base_url('assets/img/icon/kelas icon.svg'); ?>">
		<h3 class="judul">Jumlah - <?= $jml_kelas; ?></h3>
	</div>
</div>
<div class="col-4">
	<div class="home default cart red">
		<h1 class="judul marginBottom10px">Semester - Tahun Ajaran</h1>
		<img src="<?= config::base_url('assets/img/icon/pendidikan icon.svg'); ?>">
		<h3 class="judul"><?= ($_SESSION['RAPORT']['semester']??'').' - '.($_SESSION['RAPORT']['tahun_ajaran']??''); ?></h3>
	</div>
</div>
<script type="text/javascript">
$(function(){
	if(window.EventSource) {
		var source = new EventSource('homeAdmin/proses.php?action=count_persentase_insert_raport');
		source.addEventListener('message', function(e) {
			// check origin
			if(e.origin == '<?= config::protocol().$_SERVER['HTTP_HOST']; ?>') {
				let data;
				try {
					data = JSON.parse(e.data);
				} catch(e){}

				if(data != undefined) {
					const $circle = $('#svg #bar');
				    const r = $circle.attr('r');
				    const c = Math.PI*(r*2);
				    const val = parseFloat(data.jml_persentase_insert_raport);
				   
				    if (val < 0) { val = 0;}
				    if (val > 100) { val = 100;}
				    
				    const pct = c-((c*val)/100);
				    
				    $circle.css({ strokeDashoffset: pct});
				    
				    $('#cont .bigger').text(val.toString().replace(".",","));
				}
			}
		}, false);
	}
})
</script>