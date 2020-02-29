<?php 

include '../../init.php';
$db = new backup_data;
if($db->cekLoginNo_halamanAdmin() === true) die;

$data_backup = $db->generate_backup_data();

$nameFile = str_replace(" ", "_", "raport_".date("d M Y h:i:s:a"));
header('Content-Type: application/octet-stream');   
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=$nameFile.sql"); 
echo $data_backup['structureNData'].$data_backup['QIndexes'].$data_backup['QConstraint'];