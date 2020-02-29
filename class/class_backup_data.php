<?php

/**
* 
*/
class backup_data extends config {
	private function forLoop($arr){
		$data = '';
		for($a=0; $a<count($arr); $a++) {
			$data .= "  ADD ".ltrim(str_replace("\n", "", $arr[$a]));
			if($a<count($arr)-1) {
				$data .= ",\n";
			}
		}
		return $data;
	}
	
	public function generate_backup_data() {
		$qTables = $this->db->prepare("SHOW TABLES");
		$qTables->execute();
		while ($tbs = $qTables->fetch(PDO::FETCH_ASSOC)) {
			$tableNames[] = $tbs['Tables_in_'.$this->dbname];
		}

		// get server version
		$qServerVersion = $this->db->query("SELECT version()");
		$serVersion = $qServerVersion->fetch(PDO::FETCH_ASSOC)['version()'];

		$structureNData = "--".PHP_EOL."-- MANPORT Backup Database".PHP_EOL."-- Author : Reza Sariful Fikri".PHP_EOL."-- Version : 1.3".PHP_EOL."--".PHP_EOL."-- Host : ".$_SERVER['HTTP_HOST'].PHP_EOL."-- Generation Time : ".date('M d, Y')." at ".date('h:i A').PHP_EOL."-- Server Version : ".$serVersion.PHP_EOL."-- PHP Version : ".phpversion().PHP_EOL.PHP_EOL."--".PHP_EOL."-- Database : `".$this->dbname."`".PHP_EOL."--".PHP_EOL;
		$QIndexes = PHP_EOL."--".PHP_EOL."-- Indexes for dumped tables".PHP_EOL."--";
		$QConstraint = PHP_EOL."--".PHP_EOL."-- Constraints for dumped tables".PHP_EOL."--";

		foreach($tableNames as $tableName) {
			$structureNData .= PHP_EOL."-- --------------------------------------------------------".PHP_EOL.PHP_EOL;
			$structureNData .= "--".PHP_EOL."-- Table structure for table `$tableName`".PHP_EOL."--".PHP_EOL;

			$r = $this->db->prepare("SELECT * from $tableName");
			$r->execute();
			$jml_column = $r->columnCount();
			$jml_row = $r->rowCount();

			// generate query create table
			$queryCreateTable = $this->db->prepare("SHOW CREATE TABLE $tableName");
			$queryCreateTable->execute();
			$dataQCTable = $queryCreateTable->fetch(PDO::FETCH_ASSOC)['Create Table'];

			// jika primary key ada
			if(preg_match("/PRIMARY/", $dataQCTable)) {
				$dataQCTableSub = substr($dataQCTable, strpos($dataQCTable, "PRIMARY"));
			
				$pushQStucturTable = substr($dataQCTableSub, strpos($dataQCTableSub, ") ENGINE"));
				$structureNData .= PHP_EOL.rtrim(trim(str_replace($dataQCTableSub, "", $dataQCTable)), ",").PHP_EOL.$pushQStucturTable.";".PHP_EOL;

				$QForeignKey = str_replace($pushQStucturTable, "", $dataQCTableSub);
				/// jika constraint ada
				if(preg_match("/CONSTRAINT/", $QForeignKey)) {
					$QConstraintOrigin = trim(substr($QForeignKey, strpos($QForeignKey, "CONSTRAINT")));
					
					// constraint
					$QConstraint .= PHP_EOL.PHP_EOL."--".PHP_EOL."-- Constraints for table `$tableName`".PHP_EOL."--";
					$QConstraint .= PHP_EOL."ALTER TABLE `$tableName`".PHP_EOL;
					$arrQConstraint = explode(",", $QConstraintOrigin.";");
					$QConstraint .= $this->forLoop($arrQConstraint);
				}

				// indexes
				$QIndexes .= PHP_EOL.PHP_EOL."--".PHP_EOL."-- Indexes for table `$tableName`".PHP_EOL."--";
				$QIndexes .= PHP_EOL."ALTER TABLE `$tableName`".PHP_EOL;
				/// jika constraint ada
				if(preg_match("/CONSTRAINT/", $QForeignKey)) {
					$arrQIndexes = explode(",", rtrim(trim(str_replace($QConstraintOrigin, "", $QForeignKey)), ",").";");
				} else {
					$arrQIndexes = explode(",", $QForeignKey.";");
				}
				$QIndexes .= $this->forLoop($arrQIndexes);

			} else {
				$structureNData .= $dataQCTable.";\n";
			}

			// get all name column from table
			$column_name = [];
			for($i=0; $i<$jml_column; $i++){
				$col = $r->getColumnMeta($i);
				$column_name[] = "`".$col['name']."`";
			}

			// jika data didatabase ada
			if($jml_row > 0){
				$structureNData .= PHP_EOL."--".PHP_EOL."-- Dumping data for table `$tableName`".PHP_EOL."--".PHP_EOL;
				$structureNData .= PHP_EOL."INSERT INTO `$tableName` (".implode(",", $column_name).") VALUES";
				// generate values
				$row_position = 0;
				while($dataRow = $r->fetch(PDO::FETCH_NUM)) {
					$structureNData .= PHP_EOL."(";
					for($j=0; $j<$jml_column; $j++) {
						if(isset($dataRow[$j])) {
							$structureNData .= "'".$dataRow[$j]."'";
						} else {
							$structureNData .= "''";
						}
						if($j<$jml_column-1){
							$structureNData .= ", ";
						}
					}
					$structureNData .= ")";
					if($row_position < $jml_row-1) {
						$structureNData .= ",";
					} else {
						$structureNData .= ";".PHP_EOL;
					}
					$row_position++;
				}
			}
		}

		return ['structureNData'=>$structureNData, 'QIndexes'=>$QIndexes, 'QConstraint'=>$QConstraint];
	}
}