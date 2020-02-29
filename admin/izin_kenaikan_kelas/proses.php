<?php

include '../../init.php';
$db = new izin_kenaikan_kelas;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == "generate_data_izin_kenaikan_kelas") {
	echo $db->generate_data_izin_kenaikan_kelas();

} elseif($action == "change_status_izin_kenaikan_kelas") {
	echo $db->change_status_izin_kenaikan_kelas();

} elseif($action == "delete_izin_kenaikan_kelas") {
	$izin_kenaikan_kelas_id = filter_input(INPUT_POST, 'izin_kenaikan_kelas_id', FILTER_SANITIZE_STRING);
	echo $db->delete_izin_kenaikan_kelas($izin_kenaikan_kelas_id);
}