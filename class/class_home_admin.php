<?php

/**
* 
*/
class home_admin extends config {
	
	public function cek_persentase_pengisian_raport($join, $execute) {
		$execute = array_merge([
			':ss_tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], 
			':ss_semester_id'=>$_SESSION['RAPORT']['semester_id'],
			':n_tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], 
			':n_semester_id'=>$_SESSION['RAPORT']['semester_id'],
			':k_tahun_ajaran_id'=>$_SESSION['RAPORT']['tahun_ajaran_id'], 
			':k_semester_id'=>$_SESSION['RAPORT']['semester_id']
		],$execute);
		$cek = $this->db->prepare("SELECT sd.nama_siswa, k.sakit
                from siswa_detail as sd
                JOIN sikap_siswa as ss ON ss.siswa_detail_id=sd.siswa_detail_id and ss.tahun_ajaran_id=:ss_tahun_ajaran_id and ss.semester_id=:ss_semester_id
                JOIN nilai as n ON n.siswa_detail_id=sd.siswa_detail_id and n.tahun_ajaran_id=:n_tahun_ajaran_id and n.semester_id=:n_semester_id
                JOIN ketidakhadiran as k ON k.siswa_detail_id=sd.siswa_detail_id and k.tahun_ajaran_id=:k_tahun_ajaran_id and k.semester_id=:k_semester_id
                $join
                where sd.status='masih_sekolah'
                group by sd.siswa_detail_id");
		$cek->execute($execute);
		return $cek->rowCount();
	}
}