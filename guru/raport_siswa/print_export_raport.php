<?php

include '../../init.php';
require_once('../../assets/plugin/TCPDF-master/tcpdf_include.php');
$db = new raport;
if($db->cekLoginNo_halamanGuru() === true) die;
// for admin akses halaman guru
if(!$db->cek_has_tahun_ajaran_semester_session_kelas_jurusan()) die;

$dbWK = new wali_kelas;
$dbTA = new tahun_ajaran;
$dbS = new siswa;
$dbIS = new identitas_sekolah;
$dbK = new kelas;
$siswa_detail_id = filter_input(INPUT_GET, 'siswa_detail_id', FILTER_SANITIZE_STRING);
$arrTahun_ajaran = explode(".", filter_input(INPUT_GET, 'tahun_ajaran_id', FILTER_SANITIZE_STRING));
$arrSemester = explode(".", filter_input(INPUT_GET, 'semester_id', FILTER_SANITIZE_STRING));
$tahun_ajaran_id = $arrTahun_ajaran[0]??null;
$tahun_ajaran = $arrTahun_ajaran[1]??null;
$semester_id = $arrSemester[0]??null;
$semester = $arrSemester[1]??null;

// set nama wali kelas dan kelas jurusan
$nama_wali_kelas = filter_input(INPUT_GET, 'nama_wali_kelas', FILTER_SANITIZE_STRING);
$kelasJurusan = filter_input(INPUT_GET, 'kelasJurusan', FILTER_SANITIZE_STRING);
$data_siswa = $dbS->get_one_siswa_detail($siswa_detail_id, 'masih_sekolah', $select="sd.nama_siswa, sd.nisn, sd.nama_ayah");
$arrKelasKetSiswa = explode(".", $_SESSION['RAPORT']['kelas']);

if($nama_wali_kelas != null && $kelasJurusan != null) {
	$data_wali_kelas = $nama_wali_kelas;	
	$kelasJurusan = $kelasJurusan;
} else {
	$data_wali_kelas = $dbWK->get_one_wali_kelas(($_SESSION['RAPORT']['wali_kelas_id']??''), "nama")['nama']??'';
	$kelasJurusan = $arrKelasKetSiswa[0].' '.$_SESSION['RAPORT']['jurusan'].' '.($arrKelasKetSiswa[1]??'');
}

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
// set margins
$pdf->SetMargins(1.27,1.27,1.27,true);
// set auto breaks
$pdf->SetAutoPageBreak(true, 1.27);
// menghapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setTitle("Export/Print RAPORT");
// halaman 1
$pdf->AddPage();
$pdf->SetFont('freeserif', '', 11);
$page1 = '
	<table>
		<tr>
			<td width="100">Nama Peserta Didik</td>
			<td width="10">:</td>
			<td width="200">'.($data_siswa['nama_siswa']??'').'</td>

			<td width="70">Kelas</td>
			<td width="10">:</td>
			<td>'.$kelasJurusan.'</td>
		</tr>
		<tr>
			<td>NISN</td>
			<td>:</td>
			<td>'.($data_siswa['nisn']??'').'</td>

			<td>Semester/TP</td>
			<td>:</td>
			<td>'.$semester.'/'.$tahun_ajaran.'</td>
		</tr>
	</table>
	<br>

	<h4>A. Sikap</h4>
	<table border="1" cellspacing="0" cellpadding="25">
		<tr>
			<td align="justify">'.($db->tampil_sikap($siswa_detail_id, $tahun_ajaran_id, $semester_id)['sikap']??'').'</td>
		</tr>
	</table>

	<h4>B. Capaian Pengetahuan dan Keterampilan</h4>
	<table border="1" cellspacing="0" cellpadding="4">
		<tr style="background-color: #D4F2DB">
			<th rowspan="2" width="25"><br><br>No</th>
			<th rowspan="2" width="221.5"><br><br>Mata pelajaran</th>
			<th rowspan="2" width="35"><br><br>Kkm</th>
			<th colspan="2" width="118" align="center">Pengetahuan</th>
			<th colspan="2" width="118" align="center">Keterampilan</th>
		</tr>
		<tr style="background-color: #D4F2DB">
			<th align="center" width="40">Angka</th>
			<th align="center" width="78">Predikat</th>

			<th align="center" width="40">Angka</th>
			<th align="center" width="78">Predikat</th>
		</tr>';

	$cek_has_nilai_deskripsi = $db->cek_has_nilai_deskripsi($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	if($cek_has_nilai_deskripsi > 0) {
		$nilai_deskripsi = $db->tampil_nilai($siswa_detail_id, $tahun_ajaran_id, $semester_id);
		$kelompok_sebelum = '';
		$no = 1;
		foreach($nilai_deskripsi as $n) {

			if($kelompok_sebelum != $n['kelompok_mapel']) {
				$kelompok_sebelum = $n['kelompok_mapel'];
			$page1 .= '<tr>
					<th colspan="7">Kelompok '.$n['kelompok_mapel'].'</th>
				</tr>
			';
			}
			$page1 .= '<tr>
				<td align="center">'.$no.'</td>
				<td>'.$n['nama_mapel'].'</td>
				<td align="center">'.$n['kkm'].'</td>
				<td align="center">'.str_replace(".", ",", $n['nilai_p']).'</td>
				<td align="center">'.$db->generate_predikat($n['nilai_p'], $n['predikat_d'], $n['predikat_c'], $n['predikat_b'], $n['predikat_a']).'</td>
				<td align="center">'.str_replace(".", ",", $n['nilai_k']).'</td>
				<td align="center">'.$db->generate_predikat($n['nilai_k'], $n['predikat_d'], $n['predikat_c'], $n['predikat_b'], $n['predikat_a']).'</td>
			</tr>';
			$no++;
		}
	} else {
		$page1 .= '<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
	}
$page1 .= '</table>';
$pdf->writeHTML($page1, true, true, true, true, '');

// halaman 2
$pdf->AddPage();
$page2 = '
	<h4>C. Deskripsi pencapaian kompetensi</h4>
	<table border="1" cellspacing="" cellpadding="4">
		<tr style="background-color: #D4F2DB">
			<th width="25">No</th>
			<th width="150">Mata pelajaran</th>
			<th width="68">Ranah</th>
			<th width="274.5">Deskripsi</th>
		</tr>';
	if($cek_has_nilai_deskripsi > 0) {
		$no = 1;
		$kelompok_sebelum = '';
		foreach($nilai_deskripsi as $d) {
			if($kelompok_sebelum != $d['kelompok_mapel']) {
				$kelompok_sebelum = $d['kelompok_mapel'];
				$page2 .= '<tr>
					<th colspan="4">Kelompok '.$d['kelompok_mapel'].'</th>
				</tr>';
			}
			$page2 .= '<tr>
				<td rowspan="2" align="center"><br><br><br>'.$no.'</td>
				<td rowspan="2"><br><br><br>'.$d['nama_mapel'].'</td>
				<td>Pengetahuan</td>
				<td align="justify">'.$d['deskripsi_p'].'</td>
			</tr>
			<tr>
				<td>Keterampilan</td>
				<td align="justify">'.$d['deskripsi_k'].'</td>
			</tr>';
			$no++;
		}		
	} else {
		$page2 .= '<tr><td></td><td></td><td></td><td></td></tr>';
	}
$page2 .= '</table>';
$pdf->writeHTML($page2, true, true, true, true, '');

// halaman 3
$pdf->AddPage();
$praktik_kerja_industri = $db->tampil_praktik_kerja_industri($siswa_detail_id, $tahun_ajaran_id, $semester_id);
if($praktik_kerja_industri) {
	$offsetRowPrakerin = 3-count($praktik_kerja_industri??[]);
	$page31 = '<h4>D. Praktik Kerja Industri</h4>
		<table border="1" cellspacing="" cellpadding="8">
		<tr style="background-color: #D4F2DB">
			<th width="30">No</th>
			<th>Mitra DU/DI</th>
			<th width="150">Lokasi</th>
			<th>Lamanya(Bulan)</th>
			<th width="130.5">Keterangan</th>
		</tr>';
	
	$no = 1;
	foreach($praktik_kerja_industri as $prin) {
		$page31 .= '<tr>
			<td>'.$no.'</td>
			<td>'.$prin['mitra_du_di'].'</td>
			<td>'.$prin['lokasi'].'</td>
			<td>'.$prin['lamanya'].'</td>
			<td>'.$prin['keterangan'].'</td>
		</tr>';
		$no++;
	}
	for($orPrin = 1; $orPrin <= $offsetRowPrakerin; $orPrin++) {
		$page31 .= '<tr><td></td><td></td><td></td><td></td><td></td></tr>';
	}
	$page31 .= '</table>';
	$pdf->writeHTML($page31, true, true, true, true, '');
	$pdf->Ln(0.5);
	$page32 = '<h4>E. Ekstrakurikuler</h4>';
	$page33 = '<h4>F. Prestasi</h4>';
	$page41 = '<h4>G. Ketidakhadiran</h4>';
	$page42 = '<h4>H. Catatan Wali kelas</h4>';
	$page43 = '<h4>I. Tanggapan Orang Tua/Wali</h4>';
} else {
	$page32 = '<h4>D. Ekstrakurikuler</h4>';
	$page33 = '<h4>E. Prestasi</h4>';
	$page41 = '<h4>F. Ketidakhadiran</h4>';
	$page42 = '<h4>G. Catatan Wali kelas</h4>';
	$page43 = '<h4>H. Tanggapan Orang Tua/Wali</h4>';
}
$page32 .= '
	<table border="1" cellspacing="" cellpadding="6">
		<tr style="background-color: #D4F2DB">
			<th width="30">No</th>
			<th width="200">Ekstrakurikuler</th>
			<th width="35">Nilai</th>
			<th width="252.5">Keterangan</th>
		</tr>';
	$ekstrakurikuler = $db->tampil_ekstrakurikuler($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	$offsetRowEskul = 3-count($ekstrakurikuler??[]);
	if($ekstrakurikuler) {
		$no = 1;
		foreach($ekstrakurikuler as $e) {
			$page32 .= '<tr>
				<td align="center">'.$no.'</td>
				<td>'.$e['nama_ekstrakurikuler'].'</td>
				<td align="center">'.str_replace(".", ",", $e['nilai']).'</td>
				<td>'.$e['keterangan'].'</td>
			</tr>';
				$no++;
		}
	}
	for($orE = 1; $orE <= $offsetRowEskul; $orE++) {
		$page32 .= '<tr><td></td><td></td><td></td><td></td></tr>';
	}
$page32 .= '</table>';
$pdf->writeHTML($page32, true, true, true, true, '');
$pdf->Ln(0.5);
$page33 .= '
	<table border="1" cellspacing="" cellpadding="6">
		<tr style="background-color: #D4F2DB">
			<th width="30">No</th>
			<th width="200">Jenis prestasi</th>
			<th width="287.5">Keterangan</th>
		</tr>';
	$prestasi = $db->tampil_prestasi($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	$offsetRowPrestasi = 3-count($prestasi??[]);
	if($prestasi) {
		$no = 1;
		foreach($prestasi as $p) {
			$page33 .= '<tr>
				<td align="center">'.$no.'</td>
				<td>'.$p['jenis_prestasi'].'</td>
				<td>'.$p['keterangan'].'</td>
			</tr>';
			$no++;
		}
	}
	for($orP = 1; $orP <= $offsetRowPrestasi; $orP++) {
		$page33 .= '<tr><td></td><td></td><td></td></tr>';
	}
$page33 .= '</table>';
$pdf->writeHTML($page33, true, true, true, true, '');

// halaman 4
$pdf->AddPage();
$page41 .= '
	<table border="1" cellspacing="" cellpadding="4">
		<tr style="background-color: #D4F2DB">
			<th>Sakit</th>
			<th>Izin</th>
			<th>Tanpa keterangan</th>
			<th>Bolos</th>
		</tr>';
	$ketidakhadiran = $db->tampil_ketidakhadiran($siswa_detail_id, $tahun_ajaran_id, $semester_id);
	$page41 .= '<tr>
			<td>'.($ketidakhadiran['sakit']??'').'</td>
			<td>'.($ketidakhadiran['izin']??'').'</td>
			<td>'.($ketidakhadiran['tanpa_keterangan']??'').'</td>
			<td>'.($ketidakhadiran['bolos']??'').'</td>
		</tr>
	</table>';
$pdf->writeHTML($page41, true, true, true, true, '');
$pdf->Ln(0.5);
$page42 .= '
	<table border="1" cellspacing="" cellpadding="20">
		<tr>
			<td>'.($db->tampil_catatan_wali_kelas($siswa_detail_id, $tahun_ajaran_id, $semester_id)['catatan']??'').'</td>
		</tr>
	</table>';
$pdf->writeHTML($page42, true, true, true, true, '');
$pdf->Ln(0.5);
$page43 .= '
	<table border="1" cellspacing="" cellpadding="20">
		<tr>
			<td></td>
		</tr>
	</table>';
	if($semester == 2) {
		$page43 .= '<p>Keputusan :<br>Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan :</p>';
	
		$status_akhir = $db->get_one_status_akhir_semester($siswa_detail_id, $tahun_ajaran_id, $semester_id)['status_akhir']??'';

			if($status_akhir) {			
			if($status_akhir == "lulus" || $status_akhir == "tidak_lulus") {
				$page43 .= '<p><b>'.strtoupper(str_replace("_", " ", $status_akhir)).'</b></p>';

			} else { 
				$arrStatus_akhir = explode("_", $status_akhir);
				$arrKelas = explode(".", $dbK->get_one_kelas($arrStatus_akhir[3]??'', 'kelas')['kelas']??'');
				$jurusan = $dbK->get_jurusan_where_kelas_id($arrStatus_akhir[3]??'', "nama_jurusan")['nama_jurusan']??'';

				$page43 .= '<p><span style="text-transform: capitalize;">'.$arrStatus_akhir[0].'</span> '.($arrStatus_akhir[1]??'').' '.($arrStatus_akhir[2]??'').' <b>'.($arrKelas[0]??'').' '.$jurusan.' '.($arrKelas[1]??'').'</b></p>';
		 	}
		}
	}
$pdf->writeHTML($page43, true, true, true, true, '');
$pdf->Ln(2);
$identitas_sekolah = $dbIS->tampil_identitas_sekolah('nama_kepala_sekolah, nip_kepala_sekolah, kabupaten, nama_sekolah');
$tandaTangan = '
	<table border="0" cellspacing="" cellpadding="8">
		<tr>
			<td align="center">Orang Tua/Wali</td>
			<td></td>
			<td align="center">'.($identitas_sekolah['kabupaten']??'').', '.date('d').' '.$dbS->bulanIndo(date("m")).' '.date("Y").'<br>Wali Kelas</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align="center">'.($data_siswa['nama_ayah']??'').'</td>
			<td></td>
			<td align="center">'.$data_wali_kelas.'</td>
		</tr>

		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td align="center">Mengetahui Kepala sekolah<br>'.($identitas_sekolah['nama_sekolah']??'').'</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td align="center">'.($identitas_sekolah['nama_kepala_sekolah']??'').'<br>NIP.'.($identitas_sekolah['nip_kepala_sekolah']??'').'</td>
			<td></td>
		</tr>
	</table>
';
$pdf->writeHTML($tandaTangan, true, true, true, true, '');

$pdf->Output('RAPORT '.$kelasJurusan.' '.($data_siswa['nama_siswa']??'').' semester '.$semester.'.pdf','I');