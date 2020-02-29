<?php 

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');
$db = new siswa;
if($db->cekLoginNo_halamanGuru() === true) die;
// for admin akses halaman guru
if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

$dbIS = new identitas_sekolah;
$dataSiswa = $db->tampil_siswa_detail($_SESSION['RAPORT']['kelas_id'], 'masih_sekolah', 's.nama_siswa, s.nisn, s.nama_ayah, j.juara, j.rata_rata_nilai, k.sakit, k.izin, k.tanpa_keterangan, k.bolos', " LEFT JOIN juara_kelas as j ON j.siswa_detail_id=s.siswa_detail_id and j.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and j.semester_id='".$_SESSION['RAPORT']['semester_id']."' LEFT JOIN ketidakhadiran as k ON k.siswa_detail_id=s.siswa_detail_id and k.tahun_ajaran_id='".$_SESSION['RAPORT']['tahun_ajaran_id']."' and k.semester_id='".$_SESSION['RAPORT']['semester_id']."'");

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(1.27,1.27,1.27,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Export/Print serah terima raport");
// add a page
$pdf->AddPage();
$pdf->SetFont('freeserif', '', 12);
$identitas_sekolah = $dbIS->tampil_identitas_sekolah('nama_sekolah');
$arrKelas = explode(".", $_SESSION['RAPORT']['kelas']);
$judul = '
	<h4 align="center">TANDA TERIMA PENGAMBILAN RAPORT<br> '.($identitas_sekolah['nama_sekolah']??'').'  
	<br><span style="font-size: 10px;">Tahun Ajaran '.$_SESSION['RAPORT']['tahun_ajaran'].'</span></h4>
	<table>
		<tr>
			<th width="50">Kelas</th>
			<td width="10">:</td>
			<td>'.$arrKelas[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelas[1]??'').'</td>
		</tr>
		<tr>
			<th>Semester</th>
			<td>:</td>
			<td>'.$_SESSION['RAPORT']['semester'].'</td>
		</tr>
	</table>';
$pdf->writeHTML($judul, true, false, false, false, '');

$serahTerimaRaport = '
	<table border="1" cellspacing="0" cellpadding="4">
		<tr style="background-color: #F6F6F6;">
			<th width="25" rowspan="3"><br><br>No</th>
			<th rowspan="3" width="70"><br><br>NISN</th>
			<th rowspan="3" width="100"><br><br>Nama Siswa</th>
			<th rowspan="3" width="100"><br><br>Orang Tua/Wali</th>
			<th rowspan="3" width="80" align="center">Tanda Tangan Orang Tua/Wali</th>
			<th colspan="5" width="145" align="center">Keterangan</th>
		</tr>
		<tr style="background-color: #F6F6F6;">
			<th rowspan="2" width="35">Juara</th>
			<th colspan="4" width="110" align="center">Ketidakhadiran</th>
		</tr>
		<tr style="background-color: #F6F6F6;">
			<th align="center">S</th>
			<th align="center">I</th>
			<th align="center">A</th>
			<th align="center">B</th>
		</tr>';
$no = 1;
if($dataSiswa) {
	foreach($dataSiswa as $r) {
		$serahTerimaRaport .= '<tr>
			<td align="center">'.$no.'</td>
			<td>'.$r['nisn'].'</td>
			<td>'.$r['nama_siswa'].'</td>
			<td>'.$r['nama_ayah'].'</td>
			<td></td>
			<td align="center">'.$r['juara'].'</td>
			<td align="center">'.($r['sakit']??'').'</td>
			<td align="center">'.($r['izin']??'').'</td>
			<td align="center">'.($r['tanpa_keterangan']??'').'</td>
			<td align="center">'.($r['bolos']??'').'</td>
		</tr>';
		$no++;
	}
} else {
	$serahTerimaRaport .= '
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>';
}
$serahTerimaRaport .= '</table>';
$pdf->writeHTML($serahTerimaRaport, true, false, false, false, '');

$pdf->Output('TANDA TERIMA PENGAMBILAN RAPORT '.$arrKelas[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelas[1]??'').'.pdf','I');