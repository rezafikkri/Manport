<?php

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');
$db = new siswa;
if($db->cekLoginNo_halamanAdmin() === true) die;

$dbIS = new identitas_sekolah;

$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
$sebab_keluar_atas_permintaan = filter_input(INPUT_GET, 'sebab_keluar_atas_permintaan', FILTER_SANITIZE_STRING);
$data_siswa = $db->get_one_siswa_detail($siswa_detail_id, 'keluar', 'sd.nama_siswa, sd.nisn, sd.nama_ayah, k.kelas, j.nama_jurusan', null, 'JOIN kelas as k ON sd.kelas_id=k.kelas_id JOIN jurusan as j ON j.jurusan_id=k.jurusan_id');
$identitas_sekolah = $dbIS->tampil_identitas_sekolah('nama_kepala_sekolah, nip_kepala_sekolah, kabupaten');

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(1.27,1.27,1.27,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Export/Print surat pindah/keluar");
// add a page
$pdf->AddPage();
$pdf->SetFont('freeserif', '', 12);

$dataJudul = '
<h3 align="center">KETERANGAN PINDAH SEKOLAH</h3>
<h4 align="center">NAMA SISWA : '.($data_siswa['nama_siswa']??'').'</h4>';
$pdf->writeHTML($dataJudul, true, false, false, false, '');
$pdf->Ln(.8);

$arrKelas = explode(".", $data_siswa['kelas']??'');
$dataPokok = '
	<table border="1" cellspacing="0" cellpadding="4">
		<tr style="background-color: #F6F6F6; font-weight:bold;">
			<th colspan="4" align="center">KELUAR</th>
		</tr>
		<tr style="background-color: #F6F6F6; font-weight:bold;">
			<th width="50">Tanggal</th>
			<th width="80">Kelas yang ditinggalkan</th>
			<th width="213">Sebab keluar dan atas permintaan(tertulis) dari</th>
			<th width="180">Tanda tangan kepala sekolah dan Tanda Tangan Orang Tua/Wali</th>
		</tr>
		<tr>
			<td>'.date("d").' '.$db->bulanIndo(date("m")).' '.date("Y").'</td>
			<td>'.$arrKelas[0].' '.($data_siswa['nama_jurusan']??'').' '.($arrKelas[1]??'').'</td>
			<td>'.$sebab_keluar_atas_permintaan.'</td>
			<td>
				<table cellspacing="0" cellpadding="2">
					<tr>
						<td align="center">'.($identitas_sekolah['kabupaten']??'').', '.date("d").' '.$db->bulanIndo(date("m")).' '.date("Y").'</td>
					</tr>
					<tr>
						<td align="center">Kepala sekolah</td>
					</tr>
					<tr><td></td></tr><tr><td></td></tr>
					<tr>
						<td align="center"><u>'.($identitas_sekolah['nama_kepala_sekolah']??'').'</u></td>
					</tr>
					<tr>
						<td align="center">Orang Tua/Wali</td>
					</tr>
					<tr><td></td></tr><tr><td></td></tr>
					<tr>
						<td align="center"><u>'.($data_siswa['nama_ayah']??'').'</u></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
';
$pdf->writeHTML($dataPokok, true, false, false, false, '');

// add page
$pdf->AddPage();
$dataJudul2 = '
<h3 align="center">KETERANGAN PINDAH SEKOLAH</h3>
<h4 align="center">NAMA SISWA : '.($data_siswa['nama_siswa']??'').'</h4>
';
$pdf->WriteHTML($dataJudul2, true, false, false, false, '');
$pdf->Ln(.5);

$dataPokok2 = '
	<p>Diisi oleh sekolah yang baru</p>
	<table border="1" cellspacing="0" cellpadding="4">
		<tr style="background-color: #F6F6F6; font-weight:bold;">
			<th width="25">No</th>
			<th width="498" colspan="3" align="center">MASUK</th>
		</tr>
		<tr>
			<td align="center">1</td>
			<td>Nomor Induk</td>
			<td></td>

			<td rowspan="6">
				<table cellspacing="0" cellpadding="2">
					<tr>
						<td align="center">..............................</td>
					</tr>
					<tr>
						<td align="center">Kepala sekolah</td>
					</tr>
					<tr><td></td></tr><tr><td></td></tr>
					<tr>
						<td align="center">...................................</td>
					</tr>
					<tr>
						<td align="center">NIP. .............................</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center">2</td>
			<td>Nama Sekolah</td>
			<td></td>
		</tr>
		<tr>
			<td align="center">3</td>
			<td>Masuk diSekolah ini</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>A. Tanggal</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>B. Di Kelas</td>
			<td></td>
		</tr>
		<tr>
			<td align="center">4</td>
			<td>Tahun Pelajaran</td>
			<td></td>
		</tr>
	</table>
';
$pdf->WriteHTML($dataPokok2, true, false, false, false, '');

$pdf->Output('Surat keluar atau pindah '.($data_siswa['nama_siswa']??'').' '.$arrKelas[0].' '.($data_siswa['nama_jurusan']??'').' '.($arrKelas[1]??'').'.pdf','I');