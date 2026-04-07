💰 Buku Kas
<p align="center"> <img src="https://www.php.net/images/logos/php-logo.svg" width="220" alt="PHP Logo"> </p> <p align="center"> <b>Aplikasi kas berbasis web untuk pencatatan pemasukan dan pengeluaran secara efisien</b><br> <i>Sederhana, cepat, dan tidak ribet (kayak hidup yang seharusnya)</i> </p>
📖 Tentang Proyek

Buku Kas Tahu Kupat 2 adalah aplikasi manajemen kas berbasis web yang dibuat menggunakan PHP Native tanpa framework. Aplikasi ini bertujuan untuk membantu pengguna mencatat transaksi keuangan secara terstruktur, baik untuk kebutuhan pribadi, organisasi kecil, maupun tugas sekolah (yang biasanya dikejar deadline).

Dibuat dengan pendekatan sederhana namun tetap memperhatikan struktur backend, pengelolaan database, serta interaksi frontend yang responsif.

🎯 Tujuan Pengembangan
Membuat sistem pencatatan kas yang ringan dan mudah digunakan
Memahami alur kerja CRUD dengan PHP Native
Mengimplementasikan interaksi frontend menggunakan JavaScript
Melatih integrasi antara backend, database, dan UI
Tidak bergantung pada framework berat
⚙️ Tech Stack
<p align="center"> <img src="https://skillicons.dev/icons?i=php,js,css,mysql" /> </p>
Teknologi	Fungsi
PHP 8 (Native)	Backend logic dan proses data
MySQL	Penyimpanan database
CSS3	Styling tampilan
Vanilla JavaScript	Interaksi frontend
SweetAlert2 (CDN)	Notifikasi interaktif
Font Awesome 6.5 (CDN)	Ikon UI


📌 Activity Diagram
<p align="center"> <img src="https://skillicons.dev/icons?i=php,js,css,mysql" /> </p
📌 ERD (Entity Relationship Diagram)
Diagram is not supported.
📌 Use Case Diagram
Diagram is not supported.
✨ Fitur Utama
🔐 Sistem Login & Logout
💰 Pencatatan pemasukan dan pengeluaran
📂 Manajemen akun kas
📊 Monitoring transaksi
⚡ Notifikasi interaktif (SweetAlert2)
🎨 UI sederhana & clean
📁 Struktur Folder
/aksi
  ├── koneksi.php
  ├── hapusAkun.php
  ├── hapusTransaksi.php
  ├── logout.php
  └── ...

/style
  ├── main.css
  ├── components.css
  └── ...

/user
  ├── dashboard.php
  ├── akun.php
  ├── tambahKas.php
  └── ...

index.php
dump.sql
README.md
🗄️ Struktur Database (Overview)

Database dirancang sederhana dengan fokus pada:

User
Kas
Transaksi

Relasi utama:

User mungkin melakukan update pada Kas 
Kas mungkin memiliki banyak transaksi

Project ini menunjukkan implementasi dasar:

Authentication system (login/logout + session)
Secure database interaction
CRUD dengan validasi
Struktur backend modular sederhana
🔐 Security Implementation

Aplikasi ini menerapkan beberapa dasar keamanan:

🔑 Password Hashing
Menggunakan password_hash() dan password_verify() untuk mengamankan password user.
🛡️ Prepared Statements
Query database menggunakan prepared statement untuk mengurangi risiko SQL Injection.
🔒 Session Management
Menggunakan session untuk autentikasi user serta pembatasan akses halaman.
🧹 Basic Input Handling
Input dibersihkan menggunakan fungsi seperti trim() untuk menjaga konsistensi data.
⚠️ Limitations

Implementasi keamanan masih dalam tahap dasar dan dapat ditingkatkan lebih lanjut:

Belum ada proteksi CSRF
Validasi input masih sederhana
Belum ada pembatasan percobaan login (brute force protection)
Sanitasi output (XSS protection) belum diterapkan secara menyeluruh

🚀 Cara Instalasi & Menjalankan
1. Clone Repository
git clone https://github.com/username/BukuKasTahuKupat2.git
2. Pindahkan ke Server

Masukkan ke:

Laragon → C:\laragon\www\
XAMPP → htdocs
3. Import Database
Buka phpMyAdmin
Buat database baru
Import file:
dump.sql
4. Konfigurasi Koneksi

Edit file:

aksi/koneksi.php

Sesuaikan:

$host = "localhost";
$user = "root";
$pass = "";
$db   = "nama_database";
5. Jalankan Aplikasi

Buka browser:

http://localhost/Proyek2Semester2
⚠️ Catatan Penting
Gunakan PHP 8+
Pastikan MySQL berjalan
Jangan lupa sesuaikan koneksi database
CDN membutuhkan koneksi internet
🧪 Pengembangan Selanjutnya

Kalau kamu niat (dan gak males), ini bisa kamu upgrade:

🔍 Search & filter transaksi
📈 Grafik keuangan
👥 Multi-user role
📦 Export data (PDF/Excel)
🔐 Security improvement (hashing, validation, dll)
🧑‍💻 Author

NickyIno

Developer yang ngerti konsep, tapi kadang masih ngetik command setengah. Progress is progress.

🧠 Catatan

Project ini berfokus pada pembelajaran dasar pengembangan web menggunakan PHP Native, sehingga aspek keamanan masih dapat dikembangkan lebih lanjut sesuai kebutuhan production

⭐ Penutup

Project ini dibuat untuk pembelajaran dan pengembangan skill dalam membangun aplikasi berbasis web menggunakan teknologi dasar.

Kalau suatu saat kamu lihat kode lama dan cringe… itu tandanya kamu berkembang.
Kalau masih bangga… ya berarti stagnan. 😌
