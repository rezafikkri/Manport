<?php

include '../../init.php';
$db = new tahun_ajaran;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == 'add_tahun_ajaran') {
	echo $db->add_tahun_ajaran();

} else if($action == "delete_tahun_ajaran") {
	echo $db->delete_tahun_ajaran();

} else if($action == "makeSession_TahunAjaran") {
	echo $db->make_session_tahun_ajaran();

} else if($action == "tampil_tahun_ajaran") {
	$offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_STRING);
	echo json_encode($db->tampil_tahun_ajaran(20,$offset));
}