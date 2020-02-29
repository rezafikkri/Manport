<?php

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');

$db = new siswa;
if($db->cekLoginNo_halamanAdmin() === true) die;

$dbIS = new identitas_sekolah;

$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
$status = "masih_sekolah";
$dataSiswa = $db->tampil_siswa_detail_where_IN($siswa_detail_id,$status);
$identitas_sekolah = $dbIS->tampil_identitas_sekolah('nama_kepala_sekolah, nip_kepala_sekolah, kabupaten');

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(3,1.27,3,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Export/Print data siswa detail");

if($dataSiswa) {
	$no = 1;
	foreach($dataSiswa as $r) {
		$arrTTL = explode("|", $r['tempat_tanggal_lahir']);
		$bln = $db->bulanIndo($arrTTL[2]);
		$TTL = $arrTTL[0].' '.$arrTTL[1].' '.$bln.' '.end($arrTTL);

		$arrDiterima_di_sekolah_ini = explode("|", $r['diterima_disekolah_ini']);
		$dikelas = $arrDiterima_di_sekolah_ini[0]??'';
		$pada_tanggal = $arrDiterima_di_sekolah_ini[1]??'';
		$semester = $arrDiterima_di_sekolah_ini[2]??'';

		// add a page
		$pdf->AddPage();
		$pdf->SetFont('freeserif', '', 12);
		$pdf->Bookmark($no.'. '.$r['nama_siswa'], 0, 0, '', 'B');

		$pdf->SetFont('freeserif', '', 12);
		$dataSiswa = <<<EOD
			<h1 align="center" style="font-size: 13pt;">KETERANGAN TENTANG DIRI PESERTA DIDIK</h1>
			<table cellspacing="0" cellpadding="2">
				<tr><td></td><td></td><td></td></tr>
				<tr>
					<td width="170">1. Nama Peserta Didik</td>
					<td width="60" align="right">:</td>
					<td>$r[nama_siswa]</td>
				</tr>
				<tr>
					<td>2. Nomor Induk Siswa</td>
					<td align="right">:</td>
					<td>$r[no_induk]</td>
				</tr>
				<tr>
					<td>3. Nomor Induk Siswa Nasional</td>
					<td align="right">:</td>
					<td>$r[nisn]</td>
				</tr>
				<tr>
					<td>4. Tempat dan Tanggal Lahir</td>
					<td align="right">:</td>
					<td>$TTL</td>
				</tr>
				<tr>
					<td>5. Jenis Kelamin</td>
					<td align="right">:</td>
					<td>$r[jenis_kelamin]</td>
				</tr>
				<tr>
					<td>6. Agama</td>
					<td align="right">:</td>
					<td>$r[agama]</td>
				</tr>
				<tr>
					<td>7. Status dalam Keluarga</td>
					<td align="right">:</td>
					<td>$r[status_dalam_keluarga]</td>
				</tr>
				<tr>
					<td>8. Anak Ke</td>
					<td align="right">:</td>
					<td>$r[anak_ke]</td>
				</tr>
				<tr>
					<td>9. Alamat Peserta Didik</td>
					<td align="right">:</td>
					<td>$r[alamat_peserta_didik]</td>
				</tr>
				<tr>
					<td>10. Nomor telp. Rumah</td>
					<td align="right">:</td>
					<td>$r[nomor_telp_rumah]</td>
				</tr>
				<tr>
					<td>11. Sekolah Asal</td>
					<td align="right">:</td>
					<td>$r[sekolah_asal]</td>
				</tr>
				<tr>
					<td>12. Diterima di Sekolah ini</td>
					<td></td>
					<td></td>
				</tr>
					<tr>
						<td style="text-indent: 35px">a. di Kelas</td>
						<td align="right">:</td>
						<td>$dikelas</td>
					</tr>
					<tr>
						<td style="text-indent: 35px">b. Pada tanggal</td>
						<td align="right">:</td>
						<td>$pada_tanggal</td>
					</tr>
					<tr>
						<td style="text-indent: 35px">c. Semester</td>
						<td align="right">:</td>
						<td>$semester</td>
					</tr>
				<tr>
					<td>13. Nama Orang Tua</td>
					<td></td>
					<td></td>
				</tr>
					<tr>
						<td style="text-indent: 35px">a. Ayah</td>
						<td align="right">:</td>
						<td>$r[nama_ayah]</td>
					</tr>
					<tr>
						<td style="text-indent: 35px">b. Ibu</td>
						<td align="right">:</td>
						<td>$r[nama_ibu]</td>
					</tr>
				<tr>
					<td>14. Alamat Orang Tua</td>
					<td align="right">:</td>
					<td>$r[alamat_orang_tua]</td>
				</tr>
				<tr>
					<td>15. Pekerjaan Orang Tua</td>
					<td></td>
					<td></td>
				</tr>
					<tr>
						<td style="text-indent: 35px">a. Ayah</td>
						<td align="right">:</td>
						<td>$r[pekerjaan_ayah]</td>
					</tr>
					<tr>
						<td style="text-indent: 35px">b. Ibu</td>
						<td align="right">:</td>
						<td>$r[pekerjaan_ibu]</td>
					</tr>
				<tr>
					<td>16. Nama Wali Peserta didik</td>
					<td align="right">:</td>
					<td>$r[nama_wali]</td>
				</tr>
				<tr>
					<td>17. Alamat Wali Peserta didik</td>
					<td align="right">:</td>
					<td>$r[alamat_wali]</td>
				</tr>
				<tr>
					<td>16. Pekerjaan Wali Peserta didik</td>
					<td align="right">:</td>
					<td>$r[pekerjaan_wali]</td>
				</tr>
			</table>
EOD;
		$pdf->writeHTML($dataSiswa, true, false, false, false, '');

		$pdf->SetFillColor(255, 255, 255);
		$pdf->MultiCell(3, 4, '', 0, 'L', 1, 0, '', '', true);
		$pdf->MultiCell(3, 4, '', 1, 'L', 1, 0, '', '', true);

		$tandaTangan = '
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td width="50"></td>
					<td width="200">'.($identitas_sekolah['kabupaten']??'').', '.$pada_tanggal.'<br>Kepala Sekolah</td>
				</tr>
				<tr>
					<td width="50"></td>
					<td></td>
				</tr>
				<tr>
					<td width="50"></td>
					<td></td>
				</tr>
				<tr>
					<td width="50"></td>
					<td></td>
				</tr>
				<tr>
					<td width="50"></td>
					<td>'.($identitas_sekolah['nama_kepala_sekolah']??'').'<br>NIP: '.($identitas_sekolah['nip_kepala_sekolah']??'').'</td>
				</tr>
			</table>';
		$pdf->writeHTML($tandaTangan, true, true, false, false, '');
		$no++;
	}
}

$nama_file = 'Data siswa detail X Multimedia '.date('Y m d H i a');
$pdf->Output($nama_file.'.pdf','I');