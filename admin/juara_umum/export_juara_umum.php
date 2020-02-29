<?php

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');

$db = new juara_umum;
if($db->cekLoginNo_halamanAdmin() === true) die;
if(!isset($_SESSION['RAPORT']['tahun_ajaran_id']) || !isset($_SESSION['RAPORT']['semester_id'])) die;

$tipeJuara = filter_input(INPUT_GET, 'tipeJuara', FILTER_SANITIZE_STRING);
$kelas = filter_input(INPUT_GET, 'kelas', FILTER_SANITIZE_STRING);
$whiteListTipeJuara = ['all','perjenjang'];

if(empty($tipeJuara)) die;
if(!in_array($tipeJuara, $whiteListTipeJuara)) die;

if($tipeJuara == "perjenjang") {
	if(empty($kelas)) die;
	if(!preg_match("/^([IVX]+)(.[0-9a-z]+)?\z/i", $kelas)) die;
	$where = $db->generate_where($kelas);
	$kelasKet = '_kelas_'.$kelas;
} else {
	$where = null;
	$kelasKet = null;
}
$juara_umum = $db->tampil_juara_umum(null, $where);

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(1.27,1.27,1.27,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Export/Print data juara umum");
// add a page
$pdf->AddPage();
$pdf->SetFont('freeserif', '', 12);
$data = '
<h2 align="center">Juara Umum <br> Tahun ajaran '.$_SESSION['RAPORT']['tahun_ajaran'].' <br>Semester '.$_SESSION['RAPORT']['semester'].' <br>'.str_replace("_", " ", $kelasKet).'</h2>
<table border="1" cellspacing="0" cellpadding="4">
	<tr style="background-color: #F6F6F6;">
		<th width="25">No</th>
		<th width="100">NISN</th>
		<th width="128">Nama</th>
		<th width="100">Kelas</th>
		<th width="50" align="center">Jumlah</th>
		<th width="70" align="center">Rata-rata</th>
		<th width="50" align="center">Juara</th>
	</tr>';
if($juara_umum) {
	$no = 1;
	foreach($juara_umum as $r) {
		$arrKelas = explode(".", $r['kelas']);
		$data .= '<tr>';
		$data .= '<td align="center">'.$no.'</td>';
		$data .= '<td>'.$r['nisn'].'</td>';
		$data .= '<td>'.$r['nama_siswa'].'</td>';
		$data .= '<td>'.$arrKelas[0].' '.$r['nama_jurusan'].' '.($arrKelas[1]??'').'</td>';
		$data .= '<td align="center">'.$r['jml_nilai'].'</td>';
		$data .= '<td align="center">'.$r['rata_rata_nilai'].'</td>';
		$data .= '<td align="center">'.$r['juara'].'</td>';
		$data .= '</tr>';
		$no++;
	}
}
$data .= '</table>';
$pdf->writeHTML($data, true, true, false, false, '');

$pdf->Output('Data juara umum '.$_SESSION['RAPORT']['tahun_ajaran'].' Semester '.$_SESSION['RAPORT']['semester'].$kelasKet.'.pdf','I');