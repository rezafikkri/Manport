<?php

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');

$db = new siswa;
if($db->cekLoginNo_halamanAdmin() === true) die;

$dbIS = new identitas_sekolah;

$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
$data_siswa = $db->tampil_siswa_detail_where_IN($siswa_detail_id, 'lulus', 'no_un, nama_siswa, j.nama_jurusan, kelas, tahun_ajaran_kelulusan, status', 'JOIN kelas USING(kelas_id) JOIN jurusan as j USING(jurusan_id)');
$identitas_sekolah = $dbIS->tampil_identitas_sekolah('kabupaten, provinsi, logo_prov, nama_kepala_sekolah, nip_kepala_sekolah, nama_sekolah, alamat');
$no_surat = filter_input(INPUT_GET, 'no_surat', FILTER_SANITIZE_STRING);
$_SESSION['RAPORT']['no_surat'] = $no_surat;

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(2.54,2.54,2.54,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Surat keterangan lulus");

if($data_siswa) {
	$no = 1;
	foreach($data_siswa as $r) {
		// add a page
		$pdf->AddPage();
		$pdf->SetFont('freeserif', '', 12);
		$pdf->Bookmark($no.'. '.$r['nama_siswa'], 0, 0, '', 'B');

		$surat = '
		<table cellspacing="0" cellpadding="4">
			<tr>
				<td width="100"><img src="'.config::base_url('assets/img/logo/'.($identitas_sekolah['logo_prov']??'alt.jpg')).'" width="60"></td>
				<td width="325" align="center">
					<h2>PEMERINTAH PROVINSI '.$identitas_sekolah['provinsi'].' <br>DINAS PENDIDIKAN DAN KEBUDAYAAN <br>'.$identitas_sekolah['nama_sekolah'].'</h2>
					<p style="font-size: 10pt;">'.$identitas_sekolah['alamat'].'</p>
				</td>
			</tr>
		</table>
		<hr>
		<p align="center"><u>SURAT KETERANGAN KELULUSAN</u><br>Nomor: '.$no_surat.'</p>
		<p></p>
		<table cellspacing="0" cellpadding="4">
			<tr>
				<td width="150">Nama</td>
				<td width="20" align="right">:</td>
				<td width="270">'.$r['nama_siswa'].'</td>
			</tr>
			<tr>
				<td>Nomor Peserta US/UN</td>
				<td align="right">:</td>
				<td>'.$r['no_un'].'</td>
			</tr>
			<tr>
				<td>Jurusan</td>
				<td align="right">:</td>
				<td>'.$r['nama_jurusan'].'</td>
			</tr>
			<tr>
				<td>Tahun Pelajaran</td>
				<td align="right">:</td>
				<td>'.$r['tahun_ajaran_kelulusan'].'</td>
			</tr>
		</table>
		<p>Berdasarkan hasil Ujian Sekolah/Ujian Nasional Tahun pelajaran '.$r['tahun_ajaran_kelulusan'].', maka siswa tersebut dinyatakan</p>
		<h3 align="center"><i>';
		if($r['status'] == 'lulus') {
			$surat .= '<span>LULUS</span>/<span style="text-decoration: line-through;">TIDAK LULUS</span></i></h3>';
		} else {
			$surat .= '<span style="text-decoration: line-through;">LULUS</span>/<span>TIDAK LULUS</span></i></h3>';
		}

		$surat .= '<p></p><p></p>
		<p>Demikian Surat Keterangan ini diberikan untuk diketahui dan dijadikan bahan seperlunya.</p>

		<p></p><p></p><p></p><p></p>
		<table>
			<tr>
				<td width="120"></td>
				<td width="140"></td>
				<td align="center" width="200">
					<p>'.$identitas_sekolah['kabupaten'].', '.date('d').' '.$db->bulanIndo(date('m')).' '.date('Y').'<br>
					Kepala Sekolah</p>
					<p></p>
					<p></p>
					<p></p>
					<p></p>
					<p><b><u>'.$identitas_sekolah['nama_kepala_sekolah'].'</u><br>
					NIP.'.$identitas_sekolah['nip_kepala_sekolah'].'</b></p>
				</td>
			</tr>
		</table>
		';

		$pdf->writeHTML($surat, true, false, false, false, '');
		$no++;
	}
}

$arrKelas = explode(".", $data_siswa[0]['kelas']??'');
$pdf->Output('Surat keterangan lulus '.$arrKelas[0].' '.($data_siswa[0]['nama_jurusan']??'').' '.($arrKelas[1]??'').' '.($data_siswa[0]['tahun_ajaran_kelulusan']??'').'.pdf','I');