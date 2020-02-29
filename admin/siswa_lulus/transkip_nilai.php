<?php

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');
$db = new raport;
if($db->cekLoginNo_halamanAdmin() === true) die;

$dbM = new mapel;
$dbIS = new identitas_sekolah;
$dbS = new siswa;

$jurusan_id = filter_input(INPUT_GET, 'jurusan_id', FILTER_SANITIZE_STRING);
$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);

$data_tahun = $db->get_awal_akhir_tahun_ajaran_nilai($siswa_detail_id);
$arrawal_tahun = explode("-", $data_tahun['tahun_awal']);
$awal_tahun1 = $arrawal_tahun[0];

$arrakhir_tahun = explode("-", $data_tahun['tahun_akhir']);
$akhir_tahun2 = end($arrakhir_tahun);
// hitung jumlah tahun ajaran
$jml_tahun_ajaran = 0;
for($h=$awal_tahun1; $h<$akhir_tahun2; $h++) {
	$jml_tahun_ajaran++;
}

$identitas_sekolah = $dbIS->tampil_identitas_sekolah('kabupaten, provinsi, logo_prov, nama_kepala_sekolah, nip_kepala_sekolah, nama_sekolah, alamat');
$data_siswa = $dbS->get_one_siswa_detail($siswa_detail_id, 'lulus', 'sd.nisn, sd.nama_siswa, j.nama_jurusan, kelas', null, 'JOIN kelas as k USING(kelas_id) JOIN jurusan as j USING(jurusan_id)');

// generate nilai
$nilai = $db->get_nilai_transkip($siswa_detail_id);
$nilaiHasil = [];
if($nilai) {
	foreach($nilai as $n) {
		if(!isset($nilaiHasil[$n['nama_mapel']])) {
			$nilaiHasil[$n['nama_mapel']] = [
				$n['tahun'] => [
					$n['semester'] => ['nilai_p'=>str_replace(".", ",", $n['nilai_p']), 'nilai_k'=>str_replace(".", ",", $n['nilai_k'])]
				]
			];
		} else {
			if(isset($nilaiHasil[$n['nama_mapel']][$n['tahun']])) {
				$nilaiHasil[$n['nama_mapel']][$n['tahun']][$n['semester']] = [
					'nilai_p'=>str_replace(".", ",", $n['nilai_p']), 'nilai_k'=>str_replace(".", ",", $n['nilai_k'])
				];
			} else {
				$nilaiHasil[$n['nama_mapel']][$n['tahun']] = [ $n['semester'] => ['nilai_p'=>str_replace(".", ",", $n['nilai_p']), 'nilai_k'=>str_replace(".", ",", $n['nilai_k'])] ];
			}
		}
	}
}

if($jml_tahun_ajaran > 3) {
	$pageOrientation = "L";
	$widthForLogoProv = "140";
} else {
	$pageOrientation = "P";
	$widthForLogoProv = "20";
}
$pdf = new TCPDF($pageOrientation, 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(1.20,1.20,1.20,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Transkip Nilai");

if($nilai) {
	// add a page
	$pdf->AddPage();
	$pdf->SetFont('freeserif', '', 12);

	$judul = '
	<table cellspacing="0" cellpadding="4">
		<tr>
			<td width="'.$widthForLogoProv.'"></td>
			<td width="100"><img src="'.config::base_url('assets/img/logo/'.($identitas_sekolah['logo_prov']??'alt.jpg')).'" width="60"></td>
			<td width="325" align="center">
				<h2>PEMERINTAH PROVINSI '.$identitas_sekolah['provinsi'].' <br>DINAS PENDIDIKAN DAN KEBUDAYAAN <br>'.$identitas_sekolah['nama_sekolah'].'</h2>
				<p style="font-size: 10pt;">'.$identitas_sekolah['alamat'].'</p>
			</td>
		</tr>
	</table>
	<hr>
	<p></p>
	<h3 align="center">TRANSKIP NILAI</h3>
	<p></p>
	<table cellspacing="0" cellpadding="2">
		<tr>
			<td width="100">Nama</td>
			<td width="20">:</td>
			<td><b>'.$data_siswa['nama_siswa'].'</b></td>
		</tr>
		<tr>
			<td>NISN</td>
			<td>:</td>
			<td><b>'.$data_siswa['nisn'].'</b></td>
		</tr>
		<tr>
			<td>Jurusan</td>
			<td>:</td>
			<td><b>'.$data_siswa['nama_jurusan'].'</b></td>
		</tr>
	</table>
	';
	$pdf->writeHTML($judul, true, false, false, false, '');

	$pdf->SetFont('freeserif', '', 10);
	$transkip_nilai = '
	<table border="1" cellpadding="4" cellspacing="0">
		<tr style="background-color: #D4F2DB;">
			<th rowspan="3" width="25"><b><br>No</b></th>
			<th rowspan="3" width="170"><b><br>Nama Mapel</b></th>';
		if(!empty(trim($awal_tahun1)) && !empty(trim($akhir_tahun2))) :
		for($i=$awal_tahun1; $i<$akhir_tahun2; $i++) :
			$transkip_nilai .= '<th width="110" align="center" colspan="4"><b>'.$i.'-'.($i+1).'</b></th>';
		endfor;
		$transkip_nilai .= '</tr>';
		$transkip_nilai .= '
			<tr style="background-color: #D4F2DB;">';
		for($i=$awal_tahun1; $i<$akhir_tahun2; $i++) :
			$transkip_nilai .= '<th colspan="2" align="center"><b>Semester 1</b></th>
			<th colspan="2" align="center"><b>Semester 2</b></th>';
		endfor;
		$transkip_nilai .= '</tr>
			<tr style="background-color: #D4F2DB;">';
		for($i=$awal_tahun1; $i<$akhir_tahun2; $i++) :
			$transkip_nilai .= '<th align="center"><b>P</b></th>
				<th align="center"><b>K</b></th>
				<th align="center"><b>P</b></th>
				<th align="center"><b>K</b></th>';
		endfor;
		endif;
		$transkip_nilai .= '</tr>';
		if($nilai) {
			$no = 1;
			foreach($nilaiHasil as $k => $v) {
				$transkip_nilai .= '<tr>';
				$transkip_nilai .= '<td align="center">'.$no.'</td>';
				$transkip_nilai .= '<td>'.$k.'</td>';
				for($i=$awal_tahun1; $i<$akhir_tahun2; $i++) :
					if(isset($v[$i.'-'.($i+1)])) {
						$transkip_nilai .= '<td align="center" '.(($v[$i.'-'.($i+1)][1]??null) == null?'style="background-color: #ebebeb;"':'').'>'.($v[$i.'-'.($i+1)][1]??null)['nilai_p'].'</td>';
						$transkip_nilai .= '<td align="center" '.(($v[$i.'-'.($i+1)][1]??null) == null?'style="background-color: #ebebeb;"':'').'>'.($v[$i.'-'.($i+1)][1]??null)['nilai_k'].'</td>';

						$transkip_nilai .= '<td align="center" '.(($v[$i.'-'.($i+1)][2]??null) == null?'style="background-color: #ebebeb;"':'').'>'.($v[$i.'-'.($i+1)][2]??null)['nilai_p'].'</td>';
						$transkip_nilai .= '<td align="center" '.(($v[$i.'-'.($i+1)][2]??null) == null?'style="background-color: #ebebeb;"':'').'>'.($v[$i.'-'.($i+1)][2]??null)['nilai_k'].'</td>';
					} else {
						$transkip_nilai .= '<td style="background-color: #ebebeb;"></td>';
						$transkip_nilai .= '<td style="background-color: #ebebeb;"></td>';
						$transkip_nilai .= '<td style="background-color: #ebebeb;"></td>';
						$transkip_nilai .= '<td style="background-color: #ebebeb;"></td>';
					}
				endfor;
				$transkip_nilai .= '</tr>';
				$no++;
			}
		}
	$transkip_nilai .= '</table>';

	$pdf->writeHTML($transkip_nilai, true, false, false, false, '');

	// jika page landscape
	if($pageOrientation == "L") {
		// add a page
		$pdf->AddPage();
	} else {
		// jika jumlah mata pelajaran lebih dari 12
		if(count($nilaiHasil) > 12) {
			// add a page
			$pdf->AddPage();
		}	
	}

	$pdf->Ln(1);
	$pdf->SetFont('freeserif', '', 12);
	$tandaTangan = '
	<table cellpadding="4" cellspacing="0">
		<tr>
			<td></td>
			<td align="center">'.$identitas_sekolah['kabupaten'].', '.date('d').' '.$dbS->bulanIndo(date("m")).' '.date("Y").'<br>Kepala Sekolah</td>
			<td></td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr>
			<td></td>
			<td align="center"><b><u>'.$identitas_sekolah['nama_kepala_sekolah'].'</u></b><br>NIP. '.$identitas_sekolah['nip_kepala_sekolah'].'</td>
			<td></td>
		</tr>
	</table>
	';
	$pdf->writeHTML($tandaTangan, true, false, false, false, '');
}

$arrkelas = explode(".", $data_siswa['kelas']??'');
$pdf->Output('transkip nilai '.($data_siswa['nama_siswa']??'').' '.$arrkelas[0].' '.($data_siswa['nama_jurusan']??'').' '.($arrkelas[1]??'').'.pdf','I');