# MANPORT - Management Raport<br>![downloads](https://img.shields.io/badge/Version-3.0-brightgreen)
Assalamualaikum Warohmatullohi Wabarokatuh

Halo, Sesuai namanya aplikasi ini adalah aplikasi untuk Manage/Mengelola data-data **raport Siswa SMK** untuk kurikulum K13, mulai dari siswa masuk sampai siswa lulus, dengan fitur yang dimilikinya diharapkan aplikasi ini bisa membantu para guru dan pihak sekolah untuk Manage/Mengelola data raport siswa. 
Aplikasi memiliki 3 Hak Akses (Admin, Wali Kelas, Siswa), Admin bertugas mengisi semua data seperti Identitas Sekolah, Jurusan, dll. Wali Kelas bertugas mengisi data raport siswa seperti Sikap Siswa, Nilai, dll. Hak Akses Siswa hanya bisa digunakan jika aplikasi di onlinekan tidak dilocalhost. Aplikasi juga dilengkapi dengan pusat bantuan dan panduan penggunaan, cara mengaksesnya simak pada bagian **Petunjuk Install** dibawah!.

Demo Aplikasi :
1. Via Video - https://youtu.be/x88-q89TyyI

## Latar Belakang Pembuatan Aplikasi
Alasan saya membuat aplikasi ini adalah karena saya sering mendengar keluhan para guru wali kelas saat akhir semester, mengenai pengisian raport. Setiap akhir semester harus membuat dokumen pada Microsoft Word (membuat table, mengatur layout dll), juga membuat dokumen pada Microsoft Excel untuk menentukan siapa-siapa saja juara kelas-nya. Dan lagi jika ada siswa yang terdapat kesalahan nama pada lembaran raport-nya, ini biasanya jika siswa sudah naik kelas dan baru menyadari ada kesalahan penulisan nama dilembaran raportnya, apa lagi jika siswa sudah kelas XII dan lembaran raport yang salah itu kelas X. Agak tidak merepotkan jika si-wali kelas masih menyimpan dokumen-nya, namun jika tidak, pasti merepotkan sekali.

## Apa Saja Fiturnya?
### Admin :
1. Kelola data Identitas Sekolah
	- Tambah Identitas Sekolah
	- Ubah Identitas Sekolah
2. Kelola data Jurusan
	- Tambah Jurusan
	- Ubah Jurusan
	- Hapus Jurusan
3. Kelola data Kelas
	- Tambah Kelas
	- Ubah Kelas
	- Hapus Kelas
4. Kelola data Wali Kelas
	- Tambah Wali Kelas
	- Ubah Wali Kelas
	- Hapus Wali Kelas
5. Kelola data Siswa Detail
	- Tambah Siswa Detail
	- Ubah Siswa Detail
	- Hapus Siswa Detail
	- Export(PDF)/Cetak data Siswa Detail
6. Kelola data Tahun Ajaran
	- Tambah Tahun Ajaran
	- Hapus Tahun Ajaran
	- Set Tahun Ajaran Aktif
7. Kelola data Semester
	- Set Semester Aktif
8. Kelola data KKM
	- Tambah KKM
	- Ubah KKM
	- Hapus KKM
9. Kelola data Mata Pelajaran
	- Tambah Mata Pelajaran
	- Ubah Mata Pelajaran
	- Hapus Mata Pelajaran
10. Kelola data Siswa Lulus
	- Ubah Siswa Lulus
	- Hapus Siswa Lulus
	- Export(PDF)/Cetak Transkip Nilai Dari Semua Semester
	- Export(PDF)/Cetak Surat Kelulusan
11. Kelola data Siswa keluar
	- Ubah Siswa Keluar
	- Hapus Siswa Keluar
	- Export(PDF)/Cetak Surat Keluar & Masuk
12. Izin Menjalankan Kenaikan Terhadap Guru Wali Kelas
13. Juara Umum
	- Menentukan Juara Umum per-Jurusan atau per-Jenjang Kelas
	- Export(PDF)/Cetak data Juara Umum
14. Backup Database
Dan masih ada fitur-fitur tambahan lainnya.

### Wali Kelas :
1. Home Guru
	- Cek Nilai Belum Dimasukkan
	- Menentukan Juara Kelas
	- Cek Status Akhir Semester(Naik Kelas Atau Tidak) Belum Dimasukkan
	- Export(PDF)/Cetak Lembaran Serah Terima Raport
	- Menjalankan Kenaikan Kelas(Kelas Siswa Akan Diupdate Sesuai Status Akhir Semester)
	- Tampil Siswa Tinggal Kelas
	- Tampil Siswa Tidak Lulus
2. Raport Siswa
	- Tambah Sikap Siswa
	- Ubah Sikap Siswa
	- Tambah Nilai
	- Ubah Nilai
	- Tambah Prakerin
	- Hapus Prakerin
	- Tambah Ekstrakurikuler
	- Hapus Ekstrakurikuler
	- Tambah Prestasi
	- Hapus Prestasi
	- Tambah Ketidakhadiran
	- Ubah Ketidakhadiran
	- Tambah Catatan Wali Kelas
	- Ubah Catatan Wali Kelas
	- Tambah Status Akhir Semester
	- Ubah Status Akhir Semester
	- Export(PDF)/Cetak Raport Siswa
	- Reset/Hapus data Raport
Dan masih ada fitur-fitur tambahan lainnya.
Siswa :
1. Melihat Raport

## Teknologi Apa yang digunakan?
1. PHP
2. HTML5
3. CSS3
4. Javascript
5. Jquery
6. MariaDB
7. SweetAlert
8. TCPDF

## Petunjuk Install
### Localhost
Memerlukan :
1. PHP v7.4.x

Install :
1. Clone atau Download Zip and Extract
2. Copy Folder manport ke dalam folder HTDOCS
3. Export database `raport.sql`
4. Selamat Aplikasi Berhasil diInstall
5. Akses Aplikasi dengan alamat `localhost/Manport`
6. Harap baca Panduan Penggunaan, akses dengan alamat `localhost/Manport/panduan_penggunaan/`!
7. Baca juga Pusat Banutan, akses dengan alamat `http://localhost/Manport/index.php?ref=pusat_bantuan` atau pada Menu Header klik link dengan icon tanda tanya!

### Online
Memerlukan :
1. PHP v7.4.x

Install :
1. Clone atau Download Zip and Extract
2. Copy folder dan file yang ada dalam folder `Manport` kecuali file (`raport.sql`,`README.md`,`.gitignore`,`.git/`) ke dalam folder HTDOCS or PUBLIC_HTML
3. Export database `raport.sql`
4. Pada Folder CLASS buka file `class_config.php`
	- Cari dan ubah bagian ini
    ```php
    public static function base_url($uri='') {
      if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
        $protocol = 'https://';
      } else {
        $protocol = 'http://';
      }
      return self::protocol().$_SERVER['HTTP_HOST']."/Manport/".$uri;
    }
    ```
    menjadi
    ```php
    public static function base_url($uri='') {
      if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
        $protocol = 'https://';
      } else {
        $protocol = 'http://';
      }
      return self::protocol().$_SERVER['HTTP_HOST'].'/'.$uri;
    }
    ```
5. Jangan lupa untuk mengubah value dari $host, $dbname, $username dan $password pada file class_config.php, sesuaikan nilainya dengan konfigurasi database kamu.
5. Selamat Aplikasi Berhasil diInstall
6. Akses Aplikasi dengan alamat `[nama hostmu]`
7. Harap baca Panduan Penggunaan, akses dengan alamat `[nama hostmu]/panduan_penggunaan/`!
8. Baca juga Pusat Bantuan, akses dengan alamat `[nama hostmu]/index.php?ref=pusat_bantuan` atau pada Menu Header klik link dengan icon tanda tanya!

## Info Login Default
### Admin :
**No**|**Username**|**Password**
:----:|:----:|:----:
1|reza|reza12345678

URL (local): [host]/Manport/admin

URL (online): [host]/admin

Contoh: http://localhost/Manport/admin

### Wali Kelas :
> Username dan Password harus dibuat terlebih dahulu oleh admin, lalu perlu beberapa pengaturan dasar yang bisa dilihat di demo aplikasi

URL (local): [host]/Manport

URL (online): [host]/

Contoh: http://localhost/Manport

**NOTE: Setelah meng-Install, Sangat disarankan untuk mengubah password default tersebut**
