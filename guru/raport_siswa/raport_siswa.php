<?php  if(!class_exists("config")) { die; }

	$db = new raport;
	if($db->cekLoginNo_halamanGuru() === true) die;
	// for admin akses halaman guru
	if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

	$dbTA = new tahun_ajaran;
	$dbS = new siswa;
	$dbJ = new jurusan;
	$dbK = new kelas;
	$dbSem = new semester;
	$dbIS = new identitas_sekolah;
	$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
?>
<div class="col-12 raport_siswa">
	<ul class="menuActionRaportSiswa cf" id="menuRaport1">
		<li id="reset_data_raport"><a id="btnReset_data_raport" class="btnReset_data_raport"><span class="fa fa-trash-o"></span></a></li>
		<li id="print_raport"><a id="btnPrint_raport" target="blank" href="raport_siswa/print_export_raport.php?siswa_detail_id=<?= $siswa_detail_id; ?>&tahun_ajaran_id=<?= $_SESSION['RAPORT']['tahun_ajaran_id'].'.'.$_SESSION['RAPORT']['tahun_ajaran']; ?>&semester_id=<?= $_SESSION['RAPORT']['semester_id'].'.'.$_SESSION['RAPORT']['semester']; ?>"><span class="fa fa-print"></span></a></li>
		<?php 
			// jika sikap siswa sudah ada
			if($db->cek_has_sikap_siswa($siswa_detail_id) > 0) : 
		?>
		<li id="edit_sikap"><a href="<?= config::base_url('guru/index.php?ref=edit_sikap&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-edit"></span> Sikap</a></li>
		<?php else : ?>
		<li id="add_sikap"><a href="<?= config::base_url('guru/index.php?ref=add_sikap&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Sikap</a></li>
		<?php endif; ?>

		<?php  
			// jika nilai deskripsi sudah ada
			$cek_has_nilai_deskripsi = $db->cek_has_nilai_deskripsi($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
			if($cek_has_nilai_deskripsi > 0) :
		?>
		<li id="edit_nilai_deskripsi"><a href="<?= config::base_url('guru/index.php?ref=edit_nilai_deskripsi&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-edit"></span> Nilai dan Deskripsi</a></li>
		<?php else : ?>
		<li id="add_nilai_deskripsi"><a href="<?= config::base_url('guru/index.php?ref=add_nilai_deskripsi&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Nilai dan Deskripsi</a></li>
		<?php endif; ?>
	</ul>
	<div class="home default overflowXAuto">
		<table class="table noborder">
			<?php
				$data_siswa = $dbS->get_one_siswa_detail($siswa_detail_id, 'masih_sekolah', "sd.nama_siswa, sd.nisn");
			?>
			<tr>
				<td width="200">Nama peserta didik</td>
				<td width="10">:</td>
				<td width="300"><?= $data_siswa['nama_siswa']??''; ?></td>

				<td width="200">Kelas</td>
				<td width="10">:</td>
				<td width="300">
					<?php
						$arrKelas = explode(".", $_SESSION['RAPORT']['kelas']);
						echo $arrKelas[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelas[1]??''); 
					?>
				</td>
			</tr>
			<tr>
				<td>NISN</td>
				<td>:</td>
				<td><?= $data_siswa['nisn']??''; ?></td>

				<td>Semester/TP</td>
				<td>:</td>
				<td class="input_semester">
					<select id="semester">
					<?php 
						$dataSemester = $dbSem->tampil_semester();
						if($dataSemester) : 
						foreach($dataSemester as $sem) : 
					?>
						<option value="<?= $sem['semester_id'].'.'.$sem['semester']; ?>"
						<?= $sem['semester_id']==$_SESSION['RAPORT']['semester_id']?'selected':''; ?>
						><?= $sem['semester']; ?></option>
					<?php endforeach; endif; ?>
					</select>
					/
					<select id="tahun_ajaran">
					<?php  
						$dataTahun_ajaran = $dbTA->tampil_tahun_ajaran();
						if($dataTahun_ajaran) :
						foreach($dataTahun_ajaran as $ta) :
					?>
						<option value="<?= $ta['tahun_ajaran_id'].'.'.$ta['tahun']; ?>"
						<?= $_SESSION['RAPORT']['tahun_ajaran_id']==$ta['tahun_ajaran_id']?'selected':'';  ?>
						><?= $ta['tahun']; ?></option>
					<?php endforeach; endif; ?>
					</select>
				</td>
			</tr>
		</table>

		<h1 class="judul_divisi marginTop40px">A. Sikap</h1>
		<table class="table table__text-align-justify marginTop20px">
			<tbody id="tampil_sikap">
				<tr>
					<td class="sikap"><?= $db->tampil_sikap($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'],$_SESSION['RAPORT']['semester_id'])['sikap']??''; ?></td>
				</tr>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop40px">B. Capaian Pengetahuan dan Keterampilan</h1>
		<table class="table marginTop20px">
			<tr class="green">
				<th rowspan="2" width="10">No</th>
				<th rowspan="2" width="400">Mata pelajaran</th>
				<th rowspan="2" width="10">KKM</th>
				<th colspan="2">Pengetahuan</th>
				<th colspan="2">Keterampilan</th>
			</tr>
			<tr class="green">
				<th width="100" align="center">Angka</th>
				<th align="center">Predikat</th>

				<th width="100" align="center">Angka</th>
				<th align="center">Predikat</th>
			</tr>
			<tbody id="tampil_nilai">
				<?php
					if($cek_has_nilai_deskripsi > 0) :
						$nilai_deskripsi = $db->tampil_nilai($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
						$kelompok_sebelum = '';
						$no = 1;
						foreach($nilai_deskripsi as $n) :

						if($kelompok_sebelum != $n['kelompok_mapel']) :
							$kelompok_sebelum = $n['kelompok_mapel'];
				?>
				<tr>
					<th colspan="7">Kelompok <?= $n['kelompok_mapel']; ?></th>
				</tr>
				<?php endif; ?>
				<tr>
					<td align="center"><?= $no; ?></td>
					<td><?= $n['nama_mapel']; ?></td>
					<td align="center"><?= $n['kkm']; ?></td>
					<td align="center"><?= str_replace(".", ",", $n['nilai_p']); ?></td>
					<td align="center"><?= $db->generate_predikat($n['nilai_p'], $n['predikat_d'], $n['predikat_c'], $n['predikat_b'], $n['predikat_a']); ?></td>
					<td align="center"><?= str_replace(".", ",", $n['nilai_k']); ?></td>
					<td align="center"><?= $db->generate_predikat($n['nilai_k'], $n['predikat_d'], $n['predikat_c'], $n['predikat_b'], $n['predikat_a']); ?></td>
				</tr>
				<?php $no++; endforeach; ?>
				<?php else : ?>
				<tr>
					<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop40px">C. Deskripsi pencapaian kompetensi</h1>
		<table class="table table__text-align-justify marginTop20px">
			<tr class="green">
				<th width="10">No</th>
				<th width="400">Mata pelajaran</th>
				<th width="100">Ranah</th>
				<th>Deskripsi</th>
			</tr>
			<tbody id="tampil_deskripsi">
				<?php 
					if($cek_has_nilai_deskripsi > 0) :
						$no = 1;
						$kelompok_sebelum = '';
						foreach($nilai_deskripsi as $d) :
						if($kelompok_sebelum != $d['kelompok_mapel']) :
							$kelompok_sebelum = $d['kelompok_mapel'];
				?>
				<tr>
					<th colspan="7">Kelompok <?= $d['kelompok_mapel']; ?></th>
				</tr>
				<?php endif; ?>
				<tr>
					<td rowspan="2" align="center"><?= $no; ?></td>
					<td rowspan="2"><?= $d['nama_mapel']; ?></td>
					<td>Pengetahuan</td>
					<td><?= $d['deskripsi_p']; ?>
				</tr>
				<tr>
					<td>Keterampilan</td>
					<td><?= $d['deskripsi_k']; ?></td>
				</tr>
				<?php $no++; endforeach; ?> 
				<?php else: ?>
				<tr>
					<td></td><td></td><td></td><td></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div><!-- col 12 -->

<div class="col-12 raport_siswa">
	<ul class="menuActionRaportSiswa cf" id="menuRaport2">
		<li id="add_praktik_kerja_industri"><a href="<?= config::base_url('guru/index.php?ref=add_praktik_kerja_industri&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Praktik Kerja Industri</a></li>
		<li id="add_ekstrakurikuler"><a href="<?= config::base_url('guru/index.php?ref=add_ekstrakurikuler&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Ekstrakurikuler</a></li>
		<li id="add_prestasi"><a href="<?= config::base_url('guru/index.php?ref=add_prestasi&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Prestasi</a></li>
	</ul>
	<div class="home default overflowXAuto">
		<h1 class="judul_divisi">D. Praktik Kerja Industri</h1>
		<table class="table marginTop20px">
			<tr class="green">
				<th width="10" id="aksiPrin">Aksi</th>
				<th width="200">Mitra DU/DI</th>
				<th>Lokasi</th>
				<th>Lamanya (Bulan)</th>
				<th>Keterangan</th>
			</tr>
			<tbody id="tampil_praktik_kerja_industri">
				<?php  
					$praktik_kerja_industri = $db->tampil_praktik_kerja_industri($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
					if($praktik_kerja_industri):
					foreach($praktik_kerja_industri as $prin) :
				?>
				<tr>
					<td align="center"><a class="delete_praktik_kerja_industri" prakerin_id="<?= $prin['prakerin_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
					<td><?= $prin['mitra_du_di']; ?></td>
					<td><?= $prin['lokasi']; ?></td>
					<td><?= $prin['lamanya']; ?></td>
					<td><?= $prin['keterangan']; ?></td>
				</tr>
				<?php endforeach; else: ?>
				<tr>
					<td></td><td></td><td></td><td></td><td></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop40px">E. Ekstrakurikuler</h1>
		<table class="table marginTop20px">
			<tr class="green">
				<th width="10" id="aksiEskul">Aksi</th>
				<th width="400">Ekstrakurikuler</th>
				<th width="10">Nilai</th>
				<th>Keterangan</th>
			</tr>
			<tbody id="tampil_ekstrakurikuler">
				<?php  
					$ekstrakurikuler = $db->tampil_ekstrakurikuler($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
					if($ekstrakurikuler) :
					foreach($ekstrakurikuler as $e) : 
				?>
				<tr>
					<td align="center"><a class="delete_ekstrakurikuler" ekstrakurikuler_id="<?= $e['ekstrakurikuler_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
					<td><?= $e['nama_ekstrakurikuler']; ?></td>
					<td><?= str_replace(".", ",", $e['nilai']); ?></td>
					<td><?= $e['keterangan']; ?></td>
				</tr>
				<?php endforeach; else : ?>
				<tr>
					<td></td><td></td><td></td><td></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop40px">F. Prestasi</h1>
		<table class="table marginTop20px">
			<tr class="green">
				<th width="10" id="aksiPres">Aksi</th>
				<th width="400">Jenis prestasi</th>
				<th>Keterangan</th>
			</tr>
			<tbody id="tampil_prestasi">
				<?php  
					$prestasi = $db->tampil_prestasi($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
					if($prestasi) :
					foreach($prestasi as $p) :
				?>
				<tr>
					<td align="center"><a class="delete_prestasi" prestasi_id="<?= $p['prestasi_id']; ?>"><span class="fa fa-trash-o fa-lg"></span></a></td>
					<td><?= $p['jenis_prestasi']; ?></td>
					<td><?= $p['keterangan']; ?></td>
				</tr>
				<?php endforeach; else : ?>
				<tr>
					<td></td><td></td><td></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div><!-- col 12 -->

<div class="col-12 raport_siswa marginBottom100px">
	<ul class="menuActionRaportSiswa cf" id="menuRaport3">
		<?php if($db->cek_has_ketidakhadiran($siswa_detail_id) > 0) : ?>
		<li id="edit_ketidakhadiran"><a href="<?= config::base_url('guru/index.php?ref=edit_ketidakhadiran&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-edit"></span> Ketidakhadiran</a></li>
		<?php else : ?>
		<li id="add_ketidakhadiran"><a href="<?= config::base_url('guru/index.php?ref=add_ketidakhadiran&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Ketidakhadiran</a></li>
		<?php endif; ?>

		<?php if($db->cek_has_catatan_wali_kelas($siswa_detail_id) > 0) : ?>
		<li id="edit_catatan_wali_kelas"><a href="<?= config::base_url('guru/index.php?ref=edit_catatan_wali_kelas&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-edit"></span> Catatan wali kelas</a></li>
		<?php else : ?>
		<li id="add_catatan_wali_kelas"><a href="<?= config::base_url('guru/index.php?ref=add_catatan_wali_kelas&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Catatan wali kelas</a></li>
		<?php endif; ?>
		<?php if($_SESSION['RAPORT']['semester'] == 2) : ?>
			<?php if($db->cek_has_status_akhir_semester($siswa_detail_id) > 0) : ?>
			<li id="edit_status_akhir_semester"><a href="<?= config::base_url('guru/index.php?ref=edit_status_akhir_semester&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-edit"></span> Status akhir semester</a></li>
			<?php else: ?>
			<li id="add_status_akhir_semester"><a href="<?= config::base_url('guru/index.php?ref=add_status_akhir_semester&siswa_detail_id='.$siswa_detail_id); ?>"><span class="fa fa-database"></span> Status akhir semester</a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<div class="home default overflowXAuto">
		<h1 class="judul_divisi">G. Ketidakhadiran</h1>
		<table class="table marginTop20px">
			<tr class="green">
				<th>Sakit</th>
				<th>Izin</th>
				<th>Tanpa keterangan</th>
				<th>Bolos</th>
			</tr>
			<tbody id="tampil_ketidakhadiran">
				<?php  
					$ketidakhadiran = $db->tampil_ketidakhadiran($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id']);
					if($ketidakhadiran) :
				?>
				<tr>
					<td><?= $ketidakhadiran['sakit']; ?></td>
					<td><?= $ketidakhadiran['izin']; ?></td>
					<td><?= $ketidakhadiran['tanpa_keterangan']; ?></td>
					<td><?= $ketidakhadiran['bolos']; ?></td>
				</tr>
				<?php else: ?>
				<tr>
					<td></td><td></td><td></td><td></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h1 class="judul_divisi marginTop40px">H. Catatan Wali kelas</h1>
		<table class="table marginTop20px">
			<tbody id="tampil_catatan_wali_kelas">
				<tr>
					<td class="catatan_wali_kelas"><?= $db->tampil_catatan_wali_kelas($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id'])['catatan']??''; ?></td>
				</tr>
			</tbody>
		</table>

		<div id="tampil_status_akhir_semester">
		<?php if($_SESSION['RAPORT']['semester'] == 2) : ?>
			<p class="marginTop40px">Keputusan :</p>
			<p class="marginTop10px">Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>
		<?php
			$status_akhir = $db->get_one_status_akhir_semester($siswa_detail_id, $_SESSION['RAPORT']['tahun_ajaran_id'], $_SESSION['RAPORT']['semester_id'])['status_akhir']??'';
			if($status_akhir) :
			if($status_akhir == "lulus" || $status_akhir == "tidak_lulus") :
		?>
			<p class="naikTinggal marginTop20px"><b><?= strtoupper(str_replace("_", " ", $status_akhir)); ?></b></p>
		<?php 
			else :
			$arrStatus_akhir = explode("_", $status_akhir);
			$arrKelas = explode(".", $dbK->get_one_kelas($arrStatus_akhir[3]??'', 'kelas')['kelas']??'');
			$jurusan = $dbK->get_jurusan_where_kelas_id($arrStatus_akhir[3]??'', "nama_jurusan")['nama_jurusan']??'';
		?>
			<p class="naikTinggal marginTop20px"><span><?= $arrStatus_akhir[0]?></span> <?= ($arrStatus_akhir[1]??'').' '.($arrStatus_akhir[2]??''); ?> <b><?= ($arrKelas[0]??'').' '.$jurusan.' '.($arrKelas[1]??''); ?></b></p>
		<?php endif; endif; endif; ?>
		</div>
	</div>
</div>

<div class="modal_bg"></div>
<div class="modal printExportRaportNotTimeNow">
	<h1></h1>
	<form method="get" target="_blank" id="form" action="raport_siswa/print_export_raport.php">
		<input type="hidden" name="siswa_detail_id">
		<input type="hidden" name="tahun_ajaran_id">
		<input type="hidden" name="semester_id">

		<label class="label">Kelas</label>
		<input type="text" name="kelasJurusan" placeholder="...">
		<label class="label">Nama wali kelas</label>
		<input type="text" name="nama_wali_kelas" placeholder="..."">

		<a id="closeModal" class="button no_hover">Batal</a>
		<button type="submit" class="button green">Ok</button>
	</form>
</div>

<input type="hidden" id="tokenCSRF" value="<?= $db->generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	// delete ekstrakurikuler
	const tampil_ekstrakurikuler = document.querySelector("tbody#tampil_ekstrakurikuler");
	tampil_ekstrakurikuler.addEventListener('click', function(e){
		let target = e.target;
		if(!target.classList.contains('delete_ekstrakurikuler')) {
			target = e.target.parentElement;
		}
		if(target.classList.contains('delete_ekstrakurikuler')) {
			const statusAjax = document.querySelector("statusAjax");
			if(statusAjax.getAttribute("value") == "yes") {
				const ekstrakurikuler_id = target.getAttribute("ekstrakurikuler_id");
				const siswa_detail_id = '<?= $siswa_detail_id; ?>';
				const tokenCSRF = $("input#tokenCSRF").val();
				$.ajax({
					type:"POST",
					url:"raport_siswa/proses.php?action=delete_ekstrakurikuler",
					data:{tokenCSRF:tokenCSRF, ekstrakurikuler_id:ekstrakurikuler_id, siswa_detail_id:siswa_detail_id},
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
							$(target.parentElement.parentElement).remove();
						} else {
							swal('Oops', 'Ekstrakurikuler gagal dihapus!');
						}

						setTimeout(function(){
							$(".progress_bar").css({"width":"0%","transition":"0s"});
						}, 200);
					}
				})
			}
		}
	})

	// delete prestasi
	const tampil_prestasi = document.querySelector("tbody#tampil_prestasi");
	tampil_prestasi.addEventListener('click', function(e){
		let target = e.target;
		if(!target.classList.contains('delete_prestasi')) {
			target = e.target.parentElement;
		}
		if(target.classList.contains('delete_prestasi')) {
			const statusAjax = document.querySelector("statusAjax");
			if(statusAjax.getAttribute("value") == "yes") {
				const prestasi_id = target.getAttribute("prestasi_id");
				const siswa_detail_id = '<?= $siswa_detail_id; ?>';
				const tokenCSRF = $("input#tokenCSRF").val();
				$.ajax({
					type:"POST",
					url:"raport_siswa/proses.php?action=delete_prestasi",
					data:{tokenCSRF:tokenCSRF, prestasi_id:prestasi_id, siswa_detail_id:siswa_detail_id},
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
							$(target.parentElement.parentElement).remove();
						} else {
							swal('Ooops','Prestasi gagal dihapus!');
						}

						setTimeout(function(){
							$(".progress_bar").css({"width":"0%","transition":"0s"});
						}, 200);
					}
				})
			}
		}
	})

	// reset_data_raport
	const menuRaport1 = document.querySelector("ul#menuRaport1");
	if(menuRaport1 != null) {
		menuRaport1.addEventListener('click', function(e){
			let target = e.target;
			if(!target.classList.contains('btnReset_data_raport')) {
				target = e.target.parentElement;
			}
			if(target.classList.contains('btnReset_data_raport')) {
				swal({
					title: "Apakah kamu yakin?",
					text: "Data raport siswa akan dihapus!",
					showCancelButton: true,
					cancelButtonText: "Batal",
					confirmButtonText: "Hapus",
					closeOnConfirm: false,
					showLoaderOnConfirm: true,
				},
				function(isConfirm){
					if (isConfirm) {
						const statusAjax = document.querySelector("statusAjax");
						if(statusAjax.getAttribute("value") == "yes") {
							const tokenCSRF = $("input#tokenCSRF").val();
							const siswa_detail_id = '<?= $siswa_detail_id; ?>';
							statusAjax.setAttribute("value","ajax");
							$.ajax({
								type:"POST",
								url:"raport_siswa/proses.php?action=reset_data_raport",
								data:{tokenCSRF:tokenCSRF, siswa_detail_id:siswa_detail_id},
								success:function(respon) {
									statusAjax.setAttribute("value","yes");

									let data;
									try {
										data = JSON.parse(respon);
									}catch(e){}

									if(data != undefined && data.success != undefined) {
										swal('Selamat', 'Data raport berhasil dihapus!');
										// reset sikap
										$("tbody#tampil_sikap").html('<tr><td class="sikap"></td></tr>');
										// reset nilai
										$("tbody#tampil_nilai").html('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
										// reset deskripsi
										$("tbody#tampil_deskripsi").html('<tr><td></td><td></td><td></td><td></td></tr>');
										// reset ekstrakurikuler
										$("tbody#tampil_ekstrakurikuler").html('<tr><td></td><td></td><td></td><td></td></tr>');
										// reset prestasi
										$("tbody#tampil_prestasi").html('<tr><td></td><td></td><td></td></tr>');
										// reset ketidakhadiran
										$("tbody#tampil_ketidakhadiran").html('<tr><td></td><td></td><td></td><td></td></tr>');
										// reset catatan wali kelas
										$("tbody#tampil_catatan_wali_kelas").html('<tr><td class="catatan_wali_kelas"></td></tr>');
										// reset praktik kerja industri
										$("tbody#tampil_praktik_kerja_industri").html('<tr><td></td><td></td><td></td><td></td><td></td></tr>');
										// replace edit sikap
										$("li#edit_sikap").replaceWith('<li id="edit_sikap"><a href="index.php?ref=add_sikap&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Sikap</a></li>');
										// replace edit nilai deskripsi
										$("li#edit_nilai_deskripsi").replaceWith('<li id="edit_nilai_deskripsi"><a href="index.php?ref=add_nilai_deskripsi&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Nilai dan Deskripsi</a></li>');
										// replace edit ketidakhadiran
										$("li#edit_ketidakhadiran").replaceWith('<li id="edit_ketidakhadiran"><a href="index.php?ref=add_ketidakhadiran&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Ketidakhadiran</a></li>');
										// replace edit 
										$("li#edit_catatan_wali_kelas").replaceWith('<li id="edit_catatan_wali_kelas"><a href="index.php?ref=add_catatan_wali_kelas&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Catatan wali kelas</a></li>');
										// replace status akhir semester
										$("li#edit_status_akhir_semester").replaceWith('<li id="edit_status_akhir_semester"><a href="index.php?ref=add_status_akhir_semester&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Status akhir semester</a></li>');
										$("p.naikTinggal").text("");
									} else {
										swal('Oops', 'Data raport gagal dihapus!');
									}
								}
							})
						}
					}
				});
			}
		})
	}

	// delete praktik kerja industri
	const tampil_praktik_kerja_industri = document.querySelector("tbody#tampil_praktik_kerja_industri");
	if(tampil_praktik_kerja_industri != null) {
		tampil_praktik_kerja_industri.addEventListener('click', function(e){
			let target = e.target;
			if(!target.classList.contains('delete_praktik_kerja_industri')){
				target = e.target.parentElement;
			}
			if(target.classList.contains('delete_praktik_kerja_industri')) {
				const statusAjax = document.querySelector("statusAjax");
				if(statusAjax.getAttribute("value") == "yes") {
					const prakerin_id = target.getAttribute("prakerin_id");
					const siswa_detail_id = '<?= $siswa_detail_id; ?>';
					const tokenCSRF = $("input#tokenCSRF").val();
					$.ajax({
						type:"POST",
						url:"raport_siswa/proses.php?action=delete_praktik_kerja_industri",
						data:{tokenCSRF:tokenCSRF, prakerin_id:prakerin_id, siswa_detail_id:siswa_detail_id},
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
								$(target.parentElement.parentElement).remove();
							} else {
								swal('Oops', 'Praktik Kerja Industri gagal dihapus!');
							}

							setTimeout(function(){
								$(".progress_bar").css({"width":"0%","transition":"0s"});
							}, 200);
						}
					})
				}
			}
		})
	}

	// tampil raport ajax
	function tampil_raport_ajax(tahun_ajaran_id, semester_id, statusAjax, tahun_ajaran){
		const siswa_detail_id = '<?= $siswa_detail_id; ?>';
		$.ajax({
			type:"POST",
			url:"raport_siswa/proses.php?action=tampil_raport_ajax",
			data:{siswa_detail_id:siswa_detail_id, semester_id:semester_id, tahun_ajaran_id:tahun_ajaran_id},
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

				// btnprintRaport
				if(tahun_ajaran_id.split('.')[0] != '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
					// remove href btn export print
					document.querySelector("a#btnPrint_raport").removeAttribute('href');
					// add class btnPrintExportRaportNotTimeNow
					document.querySelector("a#btnPrint_raport").classList.add("btnPrintExportRaportNotTimeNow");
					// set tahun semester, tahun ajaran dan siswa detail id
					$("input[name='siswa_detail_id']").val(siswa_detail_id);
					$("input[name='tahun_ajaran_id']").val(tahun_ajaran);
					$("input[name='semester_id']").val(semester_id);

				} else {
					// change href export raport
					const hrefPrint_raport = document.querySelector("a#btnPrint_raport").getAttribute("href");
					const urlPrint_raport = '<?= config::base_url('guru/raport_siswa/print_export_raport.php'); ?>';
					document.querySelector("a#btnPrint_raport").setAttribute('href', urlPrint_raport+'?siswa_detail_id='+siswa_detail_id+'&tahun_ajaran_id='+tahun_ajaran+'&semester_id='+semester_id);
					// remove class btnPrintExportRaportNotTimeNow
					document.querySelector("a#btnPrint_raport").classList.remove("btnPrintExportRaportNotTimeNow");
				}

				// remove button
				if(semester_id.split('.')[0] != '<?= $_SESSION['RAPORT']['semester_id']; ?>' || tahun_ajaran_id.split('.')[0] != '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
					if($("li#reset_data_raport").length > 0) {
						$("li#reset_data_raport").remove();
					}
					if($("li#add_sikap").length > 0) {
						$("li#add_sikap").remove();
					} else if($("li#edit_sikap").length > 0) {
						$("li#edit_sikap").remove();
					}
					if($("li#add_nilai_deskripsi").length > 0) {
						$("li#add_nilai_deskripsi").remove();
					} else if($("li#edit_nilai_deskripsi").length > 0) {
						$("li#edit_nilai_deskripsi").remove();
					}

					if($("li#add_praktik_kerja_industri").length > 0) {
						$("li#add_praktik_kerja_industri").remove();
					}
					if($("li#add_ekstrakurikuler").length > 0) {
						$("li#add_ekstrakurikuler").remove();
					}
					if($("li#add_prestasi").length > 0) {
						$("li#add_prestasi").remove();
					}
					 
					if($("li#add_ketidakhadiran").length > 0) {
						$("li#add_ketidakhadiran").remove();
					} else if($("li#edit_ketidakhadiran").length > 0) {
						$("li#edit_ketidakhadiran").remove();
					}
					if($("li#add_catatan_wali_kelas").length > 0) {
						$("li#add_catatan_wali_kelas").remove();
					} else if($("li#edit_catatan_wali_kelas").length > 0) {
						$("li#edit_catatan_wali_kelas").remove();
					}
					if($("li#add_status_akhir_semester").length > 0) {
						$("li#add_status_akhir_semester").remove();
					} else if($("li#edit_status_akhir_semester").length > 0) {
						$("li#edit_status_akhir_semester").remove();
					}

				} else {
					$('<li id="reset_data_raport"><a id="btnReset_data_raport" class="btnReset_data_raport"><span class="fa fa-trash-o"></span></a></li>').insertBefore('li#print_raport');
				}

				// sikap
				if(data != undefined && data.sikap != undefined) {
					$("tbody#tampil_sikap").html('<tr><td class="sikap">'+data.sikap+'</td></tr>');
					// jika data ada dan semester serta tahun ajaran adalah semester yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$('<li id="edit_sikap"><a href="index.php?ref=edit_sikap&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-edit"></span> Sikap</a></li>').insertAfter("li#print_raport");
					}
				} else {
					$("tbody#tampil_sikap").html('<tr><td class="sikap"></td></tr>');
					// jika data tidak ada dan semester serta tahun ajaran adalah semester yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$('<li id="add_sikap"><a href="index.php?ref=add_sikap&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Sikap</a></li>').insertAfter("li#print_raport");
					}
				}
				// nilai
				if(data != undefined && data.nilai_deskripsi != undefined) {
					let hasil = '';
					let kelompok_sebelum = '';
					data.nilai_deskripsi.forEach(function(e, i){
						if(kelompok_sebelum != e.kelompok_mapel) {
							kelompok_sebelum = e.kelompok_mapel;
							hasil+='<tr><th colspan="7">Kelompok '+e.kelompok_mapel+'</th></tr>';
						}
						hasil+='<tr>';
						hasil+='<td align="center">'+(i+1)+'</td>';
						hasil+='<td>'+e.nama_mapel+'</td>';
						hasil+='<td align="center">'+e.kkm+'</td>';
						hasil+='<td align="center">'+(e.nilai_p!=null?e.nilai_p:'')+'</td>';
						hasil+='<td align="center">'+(e.predikat_p!=null?e.predikat_p:'')+'</td>';
						hasil+='<td align="center">'+(e.nilai_k!=null?e.nilai_k:'')+'</td>';
						hasil+='<td align="center">'+(e.predikat_k!=null?e.predikat_k:'')+'</td>';
					})
					$("tbody#tampil_nilai").html(hasil);
					// jika data ada dan semester serta tahun ajaran adalah semester yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport1").append('<li id="edit_nilai_deskripsi"><a href="index.php?ref=edit_nilai_deskripsi&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-edit"></span> Nilai dan Deskripsi</a></li>');
					}
				} else {
					$("tbody#tampil_nilai").html('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
					// jika data tidak ada dan semester serta tahun ajaran adalah semester yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport1").append('<li id="add_nilai_deskripsi"><a href="index.php?ref=add_nilai_deskripsi&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Nilai dan Deskripsi</a></li>');
					}
				}
				// deskripsi
				if(data != undefined && data.nilai_deskripsi != undefined) {
					let hasil = '';
					let kelompok_sebelum = '';
					data.nilai_deskripsi.forEach(function(e, i){
						if(kelompok_sebelum != e.kelompok_mapel) {
							kelompok_sebelum = e.kelompok_mapel;
							hasil+='<tr><th colspan="7">Kelompok '+e.kelompok_mapel+'</th></tr>';
						}
						hasil+='<tr>';
						hasil+='<td rowspan="2" align="center">'+(i+1)+'</td>';
						hasil+='<td rowspan="2">'+e.nama_mapel+'</td>';
						hasil+='<td>Pengetahuan</td>';
						hasil+='<td>'+(e.deskripsi_p!=null?e.deskripsi_p:'')+'</td>';
						hasil+='</tr>';
						hasil+='<tr>';
						hasil+='<td>Keterampilan</td>';
						hasil+='<td>'+(e.deskripsi_k!=null?e.deskripsi_k:'')+'</td>';
						hasil+='</tr>';
					})
					$("tbody#tampil_deskripsi").html(hasil);
				} else {
					$("tbody#tampil_deskripsi").html("<tr><td></td><td></td><td></td><td></td></tr>");
				}

				// jika semester dan tahun ajaran adalah yang sekarang
				if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
					$("ul#menuRaport2").append('<li id="add_praktik_kerja_industri"><a href="index.php?ref=add_praktik_kerja_industri&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Praktik Kerja Industri</a></li>');
					$("ul#menuRaport2").append('<li id="add_ekstrakurikuler"><a href="index.php?ref=add_ekstrakurikuler&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Ekstrakurikuler</a></li>');
					$("ul#menuRaport2").append('<li id="add_prestasi"><a href="index.php?ref=add_prestasi&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Prestasi</a></li>');
				}
				// praktik kerja industri
				if(data != undefined && data.praktik_kerja_industri != undefined) {
					let hasil = '';
					data.praktik_kerja_industri.forEach(function(e, i){
						hasil+='<tr>';
						if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
							$("th#aksiPrin").text("Aksi");
							hasil+='<td align="center"><a class="delete_praktik_kerja_industri" prakerin_id="'+e.prakerin_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
						} else {
							$("th#aksiPrin").text("No");
							hasil+='<td align="center">'+(i+1)+'</td>';
						}
						hasil+='<td>'+e.mitra_du_di+'</td>';
						hasil+='<td>'+e.lokasi+'</td>';
						hasil+='<td>'+e.lamanya+'</td>';
						hasil+='<td>'+e.keterangan+'</td>';
						hasil+='</tr>';
					})
					$("tbody#tampil_praktik_kerja_industri").html(hasil);
				} else {
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("th#aksiPrin").text("Aksi");
					} else {
						$("th#aksiPrin").text("No");
					}
					$("tbody#tampil_praktik_kerja_industri").html("<tr><td></td><td></td><td></td><td></td><td></td></tr>");
				}
				// ekstrakurikuler
				if(data != undefined && data.ekstrakurikuler != undefined) {
					let hasil = '';
					data.ekstrakurikuler.forEach(function(e, i){
						hasil+='<tr>';
						if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
							$("th#aksiEskul").text("Aksi");
							hasil+='<td align="center"><a class="delete_ekstrakurikuler" ekstrakurikuler_id="'+e.ekstrakurikuler_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
						} else {
							$("th#aksiEskul").text("No");
							hasil+='<td align="center">'+(i+1)+'</td>';
						}
						hasil+='<td>'+e.nama_ekstrakurikuler+'</td>';
						hasil+='<td>'+e.nilai+'</td>';
						hasil+='<td>'+e.keterangan+'</td>';
						hasil+='</tr>';
					})
					$("tbody#tampil_ekstrakurikuler").html(hasil);
				} else {
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("th#aksiEskul").text("Aksi");
					} else {
						$("th#aksiEskul").text("No");
					}
					$("tbody#tampil_ekstrakurikuler").html("<tr><td></td><td></td><td></td><td></td></tr>");
				}
				// prestasi
				if(data != undefined && data.prestasi != undefined) {
					let hasil = '';
					data.prestasi.forEach(function(e, i){
						hasil+='<tr>';
						if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
							$("th#aksiPres").text("Aksi");
							hasil+='<td align="center"><a class="delete_prestasi" prestasi_id="'+e.prestasi_id+'"><span class="fa fa-trash-o fa-lg"></span></a></td>';
						} else {
							$("th#aksiPres").text("No");
							hasil+='<td align="center">'+(i+1)+'</td>'
						}
						hasil+='<td>'+e.jenis_prestasi+'</td>';
						hasil+='<td>'+e.keterangan+'</td>';
						hasil+='</tr>';
					})
					$("tbody#tampil_prestasi").html(hasil);
				} else {
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("th#aksiPres").text("Aksi");
					} else {
						$("th#aksiPres").text("No");
					}
					$("tbody#tampil_prestasi").html("<tr><td></td><td></td><td></td></tr>");
				}

				// ketidakhadiran
				if(data != undefined && data.ketidakhadiran != undefined) {
					let hasil='<tr>';
					hasil+='<td>'+data.ketidakhadiran.sakit+'</td>';
					hasil+='<td>'+data.ketidakhadiran.izin+'</td>';
					hasil+='<td>'+data.ketidakhadiran.tanpa_keterangan+'</td>';
					hasil+='<td>'+data.ketidakhadiran.bolos+'</td>';
					hasil+='</tr>';
					$("tbody#tampil_ketidakhadiran").html(hasil);
					// jika ketidakhadiran ada dan semester serta tahun ajaran adalah yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport3").append('<li id="edit_ketidakhadiran"><a href="index.php?ref=edit_ketidakhadiran&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-edit"></span> Ketidakhadiran</a></li>');
					}
				} else {
					$("tbody#tampil_ketidakhadiran").html("<tr><td></td><td></td><td></td><td></td></tr>");
					// jika ketidakhadiran tidak ada dan semester serta tahun ajaran adalah yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport3").append('<li id="add_ketidakhadiran"><a href="index.php?ref=add_ketidakhadiran&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Ketidakhadiran</a></li>');
					}
				}
				// catatan wali kelas
				if(data != undefined && data.catatan_wali_kelas != undefined) {
					let hasil='<tr>';
					hasil+='<td class="catatan_wali_kelas">'+data.catatan_wali_kelas+'</td>';
					hasil+='</tr>';
					$("tbody#tampil_catatan_wali_kelas").html(hasil);
					// jika catatan wali kelas ada dan semester serta tahun ajaran adalah yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport3").append('<li id="edit_catatan_wali_kelas"><a href="index.php?ref=edit_catatan_wali_kelas&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-edit"></span> Catatan wali kelas</a></li>');
					}
				} else {
					$("tbody#tampil_catatan_wali_kelas").html('<tr><td class="catatan_wali_kelas"></td></tr>');
					// jika catatan wali kelas tidak ada dan semester serta tahun ajaran adalah yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport3").append('<li id="add_catatan_wali_kelas"><a href="index.php?ref=add_catatan_wali_kelas&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Catatan wali kelas</a></li>');
					}
				}
				// status akhir semester
				if(data != undefined && data.status_akhir != undefined) {
					let hasil='<p class="marginTop40px">Keputusan :</p><p class="marginTop10px">Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>';
					hasil+=data.status_akhir;
					$("div#tampil_status_akhir_semester").html(hasil);
					// jika status akhir semester ada dan semester serta tahun ajaran adalah yang sekarang
					if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
						$("ul#menuRaport3").append('<li id="edit_status_akhir_semester"><a href="index.php?ref=edit_status_akhir_semester&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-edit"></span> Status akhir semester</a></li>');
					}
				} else {
					if(semester_id.split('.')[1] == 2) {
						$("div#tampil_status_akhir_semester").html('<p class="marginTop40px">Keputusan :</p><p class="marginTop10px">Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>');
						// jika status akhir semester tidak ada dan semester serta tahun ajaran adalah yang sekarang
						if(semester_id.split('.')[0] == '<?= $_SESSION['RAPORT']['semester_id']; ?>' && tahun_ajaran_id.split('.')[0] == '<?= $_SESSION['RAPORT']['tahun_ajaran_id']; ?>') {
							$("ul#menuRaport3").append('<li id="add_status_akhir_semester"><a href="index.php?ref=add_status_akhir_semester&siswa_detail_id='+siswa_detail_id+'"><span class="fa fa-database"></span> Status akhir semester</a></li>');
						}
					} else {
						$("div#tampil_status_akhir_semester").html('');

						if($("li#add_status_akhir_semester").length > 0) {
							$("li#add_status_akhir_semester").remove();
						} else if($("li#edit_status_akhir_semester").length > 0) {
							$("li#edit_status_akhir_semester").remove();
						}
					}
				}

				setTimeout(function(){
					$(".progress_bar").css({"width":"0%","transition":"0s"});
				}, 200);
			}
		})
	}

	$("select#semester").change(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const semester_id = $(this).val();;
			const tahun_ajaran = $("select#tahun_ajaran").val();
			const tahun_ajaran_id = $("select#tahun_ajaran").val().split('.')[0];
			// tampil raport ajax
			tampil_raport_ajax(tahun_ajaran_id, semester_id, statusAjax, tahun_ajaran);
		}
	})

	$("select#tahun_ajaran").change(function(){
		const statusAjax = document.querySelector("statusAjax");
		if(statusAjax.getAttribute("value") == "yes") {
			const semester_id = $("select#semester").val();
			const tahun_ajaran = $(this).val();
			const tahun_ajaran_id = $(this).val().split('.')[0];
			// tampil raport ajax
			tampil_raport_ajax(tahun_ajaran_id, semester_id, statusAjax, tahun_ajaran);
		}
	})

	// modal get data wali kelas and jurusan kelas for export/print raport not time now
	const ul = document.querySelector("ul#menuRaport1");
	ul.addEventListener("click", function(e){
		let target = e.target;
		if(target.classList.contains('btnPrintExportRaportNotTimeNow') == false) {
			target = e.target.parentElement;
		}
		if(target.classList.contains('btnPrintExportRaportNotTimeNow') == true) {
			$("div.modal_bg").addClass("muncul");
			$("div.modal").addClass("muncul");
		}
	})
	
	$("a#closeModal").click(function(){
		$("div.modal_bg").removeClass("muncul");
		$("div.modal").removeClass("muncul");
	})
	$('button[type="submit"]').click(function(){
		$("div.modal_bg").removeClass("muncul");
		$("div.modal").removeClass("muncul");
	})
})
</script>