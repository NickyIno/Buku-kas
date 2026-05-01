-- Membuat Database
CREATE DATABASE `Kas`; 

-- Struktur tabel: users
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel: transaksi_kas
CREATE TABLE `transaksi_kas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_master` int NOT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  `keterangan` varchar(255) NOT NULL,
  `jumlah_uang` decimal(15,2) NOT NULL,
  `type` enum('masuk','keluar') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_master` (`id_master`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel: master_kas
CREATE TABLE `master_kas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kas` varchar(100) NOT NULL,
  `total_masuk` decimal(15,2) DEFAULT 0.00,
  `total_keluar` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membuat akun utama
INSERT INTO `users` (`username`, `password`, `role`) 
VALUES ('admin', '$2y$10$wx/9wTK6DRkc3GsqfST6R.5A7pli9UfqfheYdrwx9EO5AOFlC1x1C', 'admin');
-- Passwordnya adalah admin
