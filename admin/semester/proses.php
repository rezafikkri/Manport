<?php

include '../../init.php';
$db = new semester;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if($action == 'make_session_semester') {
	echo $db->make_session_semester();
}