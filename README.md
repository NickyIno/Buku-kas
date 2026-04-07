<div align="center">

# 💰 Buku Kas Tahu Kupat 2

<img src="https://www.php.net/images/logos/php-logo.svg" width="180" alt="PHP Logo">

<br>

> **Aplikasi kas berbasis web untuk pencatatan pemasukan dan pengeluaran secara efisien**
> 
> *Sederhana, cepat, dan tidak ribet — kayak hidup yang seharusnya.*

<br>

![PHP](https://img.shields.io/badge/PHP-8+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Status](https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)

</div>

---

## 📖 Tentang Proyek

**Buku Kas Tahu Kupat 2** adalah aplikasi manajemen kas berbasis web yang dibangun menggunakan **PHP Native** tanpa framework. Dirancang untuk membantu pengguna mencatat transaksi keuangan secara terstruktur — cocok untuk kebutuhan pribadi, organisasi kecil, maupun tugas sekolah yang dikejar deadline.

Dibuat dengan pendekatan sederhana namun tetap memperhatikan struktur backend yang bersih, pengelolaan database yang aman, serta tampilan frontend yang responsif.

---

## 🎯 Tujuan Pengembangan

| # | Tujuan |
|---|--------|
| 1 | 🪶 Membuat sistem pencatatan kas yang **ringan dan mudah digunakan** |
| 2 | 🔄 Memahami alur kerja **CRUD** dengan PHP Native |
| 3 | ⚡ Mengimplementasikan interaksi frontend menggunakan **JavaScript** |
| 4 | 🔗 Melatih integrasi antara **backend, database, dan UI** |
| 5 | 🚫 Tidak bergantung pada **framework berat** |

---

## ⚙️ Tech Stack

<div align="center">

<img src="https://skillicons.dev/icons?i=php,js,css,mysql" />

</div>

<br>

| Teknologi | Fungsi |
|-----------|--------|
| ![PHP](https://img.shields.io/badge/PHP_8_Native-777BB4?style=flat-square&logo=php&logoColor=white) | Backend logic dan proses data |
| ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) | Penyimpanan database |
| ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) | Styling tampilan |
| ![JS](https://img.shields.io/badge/Vanilla_JS-F7DF1E?style=flat-square&logo=javascript&logoColor=black) | Interaksi frontend |
| ![SweetAlert](https://img.shields.io/badge/SweetAlert2_(CDN)-FF6161?style=flat-square) | Notifikasi interaktif |
| ![FontAwesome](https://img.shields.io/badge/Font_Awesome_6.5_(CDN)-528DD7?style=flat-square&logo=fontawesome&logoColor=white) | Ikon UI |

---

## ✨ Fitur Utama

```
🔐  Sistem Login & Logout
💰  Pencatatan pemasukan dan pengeluaran
📂  Manajemen akun kas
📊  Monitoring transaksi
⚡  Notifikasi interaktif (SweetAlert2)
🎨  UI sederhana & clean
```

---

## 📌 Diagram & Desain

<details>
<summary>📊 <b>Activity Diagram</b></summary>
<br>
<p align="center">
  <img src="https://drive.google.com/uc?export=view&id=1Hi1xitJtlyGqRkN02zmuIdnSHQgDkKad" width="600"/>
</p>
</details>

<details>
<summary>🗂️ <b>ERD (Entity Relationship Diagram)</b></summary>
<br>
<p align="center">
  <img src="https://drive.google.com/uc?export=view&id=1GevxVUbUpQ-jD_kxzWBYNMDPySZ3v-yf" width="600"/>
</p>
</details>

<details>
<summary>👤 <b>Use Case Diagram</b></summary>
<br>
<p align="center">
  <img src="https://drive.google.com/uc?export=view&id=1SpOTi4JraRP3aJWWxY5f4mDBYzgZBwhX" width="600"/>
</p>
</details>

---

## 📁 Struktur Folder

```
BukuKasTahuKupat2/
│
├── 📂 aksi/
│   ├── koneksi.php
│   ├── hapusAkun.php
│   ├── hapusTransaksi.php
│   ├── logout.php
│   └── ...
│
├── 📂 style/
│   ├── main.css
│   ├── components.css
│   └── ...
│
├── 📂 user/
│   ├── dashboard.php
│   ├── akun.php
│   ├── tambahKas.php
│   └── ...
│
├── index.php
├── dump.sql
└── README.md
```

---

## 🗄️ Struktur Database

Database dirancang sederhana dengan tiga entitas utama:

```
👤 User ──────┐
              ├──► melakukan update pada ──► 💼 Kas
              │                                  │
              │                                  └──► memiliki banyak ──► 💸 Transaksi
              └──────────────────────────────────────────────────────────────────────
```

---

## 🔐 Implementasi Keamanan

| Fitur | Deskripsi |
|-------|-----------|
| 🔑 **Password Hashing** | Menggunakan `password_hash()` & `password_verify()` |
| 🛡️ **Prepared Statements** | Mencegah risiko SQL Injection |
| 🔒 **Session Management** | Autentikasi & pembatasan akses halaman |
| 🧹 **Input Handling** | Sanitasi input dengan `trim()` |

> ⚠️ **Catatan:** Implementasi keamanan masih tahap dasar. Belum ada proteksi CSRF, brute force protection, dan sanitasi XSS secara menyeluruh.

---

## 🚀 Instalasi & Menjalankan

### 1. Clone Repository

```bash
git clone https://github.com/username/BukuKasTahuKupat2.git
```

### 2. Pindahkan ke Server

```
Laragon  →  C:\laragon\www\BukuKasTahuKupat2
XAMPP    →  htdocs\BukuKasTahuKupat2
```

### 3. Import Database

```
1. Buka phpMyAdmin
2. Buat database baru
3. Import file: dump.sql
```

### 4. Konfigurasi Koneksi

Edit `aksi/koneksi.php`:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "nama_database";
```

### 5. Jalankan Aplikasi

```
http://localhost/Proyek2Semester2
```

> ✅ **Requirement:** PHP 8+, MySQL aktif, dan koneksi internet untuk CDN.

---

## 🔭 Rencana Pengembangan(silahkan kembangkan sendiri kalo niat)

- [ ] 🔍 Search & filter transaksi
- [ ] 📈 Grafik keuangan interaktif
- [ ] 👥 Multi-user & role management
- [ ] 📦 Export data (PDF / Excel)
- [ ] 🔐 Security improvement (CSRF, brute force protection, XSS sanitasi)

---

## 🧑‍💻 Author

<div align="center">

**NickyIno**

*Developer yang ngerti konsep, tapi kadang masih ngetik command setengah.*
*Progress is progress.* 🚀

</div>

---

<div align="center">

### ⭐ Kalau project ini bermanfaat, jangan lupa kasih bintang!

*Dibuat untuk pembelajaran dan pengembangan skill web dengan teknologi dasar.*

<br>

> *Kalau suatu saat kamu lihat kode lama dan cringe... itu tandanya kamu berkembang.*
> *Kalau masih bangga... ya berarti stagnan. 😌*

</div>
