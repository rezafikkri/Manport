<?php 

/**
* 
*/
class bantuan extends config {
	public function add_pusat_bantuan() {
	    $nama_bantuan = str_replace(" ", "_", filter_input(INPUT_POST, 'nama_bantuan', FILTER_SANITIZE_STRING));
	    $nama_file = str_replace(['?'], "", $nama_bantuan);
	    $for_to = filter_input(INPUT_POST, 'for_to', FILTER_SANITIZE_STRING);
	    // create a file
	   if(!file_exists($nama_file.'.php')) {
	   		fopen($nama_file.'.php', 'w');
		    // insert into database
		    $pusat_bantuan_id = $this->generate_uuid();
		    $insert = $this->db->prepare("INSERT INTO pusat_bantuan set pusat_bantuan_id=:pusat_bantuan_id, nama_bantuan=:nama_bantuan, for_to=:for_to");
		    $insert->execute([ ':pusat_bantuan_id'=>$pusat_bantuan_id, ':nama_bantuan'=>$nama_bantuan, ':for_to'=>$for_to ]);
	   }
	}

	public function tampil_pusat_bantuan() {
	    $get = $this->db->prepare("SELECT * from pusat_bantuan order by nama_bantuan desc");
	    $get->execute();
	    while($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    }
	    return @$hasil;
	}

	public function get_one_pusat_bantuan($pusat_bantuan_id) {
	    $get = $this->db->prepare("SELECT nama_bantuan from pusat_bantuan where pusat_bantuan_id=:pusat_bantuan_id");
	    $get->execute([':pusat_bantuan_id'=>$pusat_bantuan_id]);
	    return $get->fetch(PDO::FETCH_ASSOC);
	}
}
