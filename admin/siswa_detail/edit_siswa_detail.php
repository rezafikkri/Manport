<?php  if(!class_exists("config")) { die; }

	$db = new siswa;
	if($db->cekLoginNo_halamanAdmin() === true) die;

	$dbJ = new jurusan;
	$dbK = new kelas;
	$dbSem = new semester;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
	$r = $db->get_one_siswa_detail($siswa_detail_id,'masih_sekolah',null,null,'JOIN kelas as k USING(kelas_id)');
	$errors = $db->get_form_errors();
?>
<div class="col-8 offset-right-2 offset-left-2 marginBottom100px">
	<div class="home default cf">
		<h1 class="judul marginBottom20px">Edit Siswa</h1>
		<form id="form" method="post" action="siswa_detail/proses.php?action=edit_siswa_detail">
		<?= $db->pesan_edit_siswa_detail(); ?>
		<?php if($r) : ?>
			<input type="hidden" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">			
			<input type="hidden" name="siswa_detail_id" value="<?= $siswa_detail_id; ?>">
			<label class="label">Status</label>
			<?= $errors['status']??'';	?>
			<select name="status">
				<option selected="" disabled="">...</option>
				<?php 
					$arrStatus = ['masih_sekolah','keluar']; 
					foreach($arrStatus as $st) :
				?>
				<option value="<?= $st; ?>"
				<?= $r['status']==$st?'selected':''; ?>
				><?= strtoupper(str_replace("_", " ", $st)); ?></option>
				<?php endforeach; ?>
			</select>
			<label class="label">Jurusan</label>
			<select name="jurusan" id="jurusan" url_tampil_kelas="siswa_detail/proses.php?action=tampil_kelas">
				<option disabled="" selected="">...</option>
				<?php  
					$dataJ = $dbJ->tampil_jurusan();
					if($dataJ) :
					foreach($dataJ as $j) :
				?>
				<option value="<?= $j['jurusan_id']; ?>"
				<?= $j['jurusan_id']==$r['jurusan_id']?'selected':''; ?>
				><?= $j['nama_jurusan']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Kelas</label>
			<?= $errors['kelas']??''; ?>
			<select name="kelas" id="kelas">
				<option disabled="" selected="">...</option>
				<?php  
					$dataK = $dbK->tampil_kelas($r['jurusan_id']);
					if($dataK) :
					foreach($dataK as $k) :
				?>
				<option value="<?= $k['kelas_id']; ?>"
				<?= $k['kelas_id']==$r['kelas_id']?'selected':''; ?>
				><?= $k['kelas']; ?></option>
				<?php endforeach; endif; ?>
			</select>
			<label class="label">Nama siswa</label>
			<!-- pesan nama siswa -->
			<?= $errors['nama_siswa']??''; ?>			
			<input type="text" name="nama_siswa" placeholder="..." value="<?= $r['nama_siswa']; ?>">
			<label class="label">NISN</label>
			<!-- pesan nisn -->
			<?= $errors['nisn']??''; ?>			
			<input type="text" name="nisn" placeholder="..." maxlength="10" value="<?= $r['nisn']; ?>">
			<label class="label">NIS</label>
			<!-- pesan no induk -->	
			<?= $errors['no_induk']??''; ?>		
			<input type="text" name="no_induk" maxlength="6" placeholder="..." value="<?= $r['no_induk']; ?>">
			
			<p class="keterangan_input marginTop20px">Tempat Tanggal Lahir :</p>
			<?php $arrTTL = explode('|',$r['tempat_tanggal_lahir']); ?>
			<label class="label">Tempat lahir</label>
			<!-- pesan tempat lahir -->
			<?= $errors['tempat_lahir']??''; ?>
			<input type="text" name="tempat_lahir" placeholder="Tempat lahir ..." value="<?= $arrTTL[0]; ?>">
			<label class="label">Tanggal lahir</label>
			<!-- pesan tanggal lahir -->
			<?= $errors['tanggal_lahir']??''; ?>
			<select name="tanggal_lahir">
				<option selected="" disabled="">...</option>
				<?php for($tgl=1;$tgl<=31;$tgl++) : ?>
				<option value="<?= $tgl; ?>"
				<?= $tgl==($arrTTL[1]??'')?'selected':''; ?>
				><?= $tgl; ?></option>
				<?php endfor; ?>
			</select>
			<label class="label">Bulan lahir</label>
			<!-- pesan bulan lahir -->
			<?= $errors['bulan_lahir']??''; ?>
			<select name="bulan_lahir">
				<option selected="" disabled="">...</option>
				<?php for($bln=1;$bln<=12;$bln++) : ?>
				<option value="<?= $bln; ?>"
				<?= $bln==($arrTTL[2]??'')?'selected':''; ?>
				><?= $bln; ?></option>
				<?php endfor; ?>
			</select>
			<label class="label">Tahun lahir</label>
			<!-- pesan tahun lahir -->
			<?= $errors['tahun_lahir']??''; ?>
			<select name="tahun_lahir">
				<option selected="" disabled="">...</option>
				<?php for($thn=date('Y', time())-24;$thn<=date('Y', time());$thn++) : ?>
				<option value="<?= $thn; ?>"
				<?= $thn==end($arrTTL)?'selected':''; ?>
				><?= $thn; ?></option>
				<?php endfor; ?>
			</select>

			<label class="label marginTop20px">Jenis kelamin</label>
			<?= $errors['jenis_kelamin']??''; ?>		
			<select name="jenis_kelamin">
				<option selected="" disabled="">...</option>
				<?php 
					$arrGender = ['Laki-laki','Perempuan']; 
					foreach($arrGender as $g) :
				?>
				<option value="<?= $g; ?>"
				<?= $g==$r['jenis_kelamin']?'selected':''; ?>
				><?= $g; ?></option>
				<?php endforeach; ?>				
			</select>
			<label class="label">Agama</label>
			<!-- pesan agama -->
			<?= $errors['agama']??''; ?>			
			<input type="text" name="agama" placeholder="..." value="<?= $r['agama']; ?>">
			<label class="label">Status dalam keluarga</label>
			<!-- pesan status dalam keluarga -->	
			<?= $errors['status_dalam_keluarga']??''; ?>		
			<input type="text" name="status_dalam_keluarga" placeholder="..." value="<?= $r['status_dalam_keluarga']; ?>">
			<label class="label">Anak ke</label>
			<!-- pesan anak ke -->	
			<?= $errors['anak_ke']??''; ?>		
			<input type="text" name="anak_ke" placeholder="..." value="<?= $r['anak_ke']; ?>">
			<label class="label">Alamat peserta didik</label>
			<!-- pesan alamat peserta didik -->	
			<?= $errors['alamat_peserta_didik']??''; ?>		
			<textarea name="alamat_peserta_didik" placeholder="..."><?= $r['alamat_peserta_didik']; ?></textarea>
			<label class="label">Nomor telpon rumah</label>		
			<input type="text" name="nomor_telpon_rumah" placeholder="..." maxlength="11" value="<?= $r['nomor_telp_rumah']; ?>">

			<!-- pesan asal sekolah -->
			<label class="label">Sekolah asal</label>	
			<?= $errors['sekolah_asal']??''; ?>		
			<input type="text" name="sekolah_asal" placeholder="..." value="<?= $r['sekolah_asal']; ?>">
		
			<p class="keterangan_input marginTop20px">Diterima disekolah ini :</p>
			<?php $arrDTrimLah = explode('|', $r['diterima_disekolah_ini']); ?>
			<label class="label">Dikelas</label>
			<!-- pesan di kelas -->
			<?= $errors['dikelas']??''; ?>
			<input type="text" name="dikelas" placeholder="..." value="<?= $arrDTrimLah[0]??''; ?>">
			<label class="label">Pada tanggal</label>
			<!-- pesan pada tanggal -->
			<?= $errors['pada_tanggal']??''; ?>
			<input type="text" name="pada_tanggal" placeholder="..." value="<?= $arrDTrimLah[1]??''; ?>">
			<label class="label">Semester</label>
			<!-- pesan semester -->
			<?= $errors['semester']??''; ?>	
			<select name="semester">
				<option disabled="" selected="">...</option>
				<?php  
					$dataSemester = $dbSem->tampil_semester();
					if($dataSemester) :
					foreach($dataSemester as $sem) :
				?>
				<option value="<?= $sem['semester']; ?>"
				<?= $sem['semester']==($arrDTrimLah[2]??'')?'selected':''; ?>
				><?= $sem['semester']; ?></option>
				<?php endforeach; endif; ?>
			</select>
		
			<p class="keterangan_input marginTop20px">Data orang tua :</p>
			<label class="label">Nama ayah</label>
			<!-- pesan nama ayah -->
			<?= $errors['nama_ayah']??''; ?>
			<input type="text" name="nama_ayah" placeholder="..." value="<?= $r['nama_ayah']; ?>">
			<label class="label">Nama ibu</label>
			<!-- pesan nama ibu -->
			<?= $errors['nama_ibu']??''; ?>
			<input type="text" name="nama_ibu" placeholder="..." value="<?= $r['nama_ibu']; ?>">
			<label class="label">Alamat orang tua</label>
			<!-- pesan alamat orang tua -->	
			<?= $errors['alamat_orang_tua']??''; ?>		
			<textarea name="alamat_orang_tua" placeholder="..."><?= $r['alamat_orang_tua']; ?></textarea>
			<label class="label">Perkerjaan ayah</label>
			<!-- pesan pekerjaan ayah -->
			<?= $errors['pekerjaan_ayah']??''; ?>
			<input type="text" name="pekerjaan_ayah" placeholder="..." value="<?= $r['pekerjaan_ayah']; ?>">
			<label class="label">Pekerjaan ibu</label>
			<!-- pesan pekerjaan ibu -->
			<?= $errors['pekerjaan_ibu']??''; ?>
			<input type="text" name="pekerjaan_ibu" placeholder="..." value="<?= $r['pekerjaan_ibu']; ?>">
		
			<p class="keterangan_input marginTop20px">Data Wali :</p>
			<label class="label">Nama wali</label>
			<input type="text" name="nama_wali" placeholder="..." value="<?= $r['nama_wali']; ?>">
			<label class="label">Pekerjaan wali</label>
			<textarea name="alamat_wali" placeholder="..."><?= $r['alamat_wali']; ?></textarea>
			<label class="label">Pekerjaan wali</label>
			<input type="text" name="pekerjaan_wali" placeholder="..." value="<?= $r['pekerjaan_wali']; ?>">
		<?php endif; ?>
			<a href="index.php?ref=siswa_detail" class="button no_hover"><span class="fa fa-arrow-left"></span></a>
		<?php if($r) : ?>
			<button id="simpan" class="button green"><span class="fa fa-send"></span> Simpan</button>
		<?php endif; ?>
			
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript" src="<?= config::base_url('assets/js/action/get_kelas.js'); ?>"></script>