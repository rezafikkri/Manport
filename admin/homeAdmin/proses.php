<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

include '../../init.php';
$db = new siswa;
$dbR = new raport;
$dbHA = new home_admin;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "count_persentase_insert_raport") {
	$jml_siswa = $db->count_jml_siswa("masih_sekolah");
	if($jml_siswa > 0 && isset($_SESSION['RAPORT']['tahun_ajaran_id']) && isset($_SESSION['RAPORT']['semester_id'])) {
		if($_SESSION['RAPORT']['semester']==2){
			$join = "JOIN status_akhir_semester as sas on sas.siswa_detail_id=sd.siswa_detail_id and sas.tahun_ajaran_id=:sas_tahun_ajaran_id and sas.semester_id=:sas_semester_id";
			$execute = [':sas_tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], ':sas_semester_id'=>$_SESSION['RAPORT']['semester_id']];
		} else {
			$join = null;
			$execute = [];
		}

		$jml_persen = $dbHA->cek_persentase_pengisian_raport($join, $execute);
		$jml_persentase_insert_raport = number_format(($jml_persen/$jml_siswa)*100, 1);
		$db->sendDataRealTime(time(), '"jml_persentase_insert_raport":"'.$jml_persentase_insert_raport.'"');

	} else {
		$db->sendDataRealTime(time(), '"jml_persentase_insert_raport":"0"');
	}
}