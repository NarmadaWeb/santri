CREATE DATABASE IF NOT EXISTS tpq_almisbah;
USE tpq_almisbah;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'santri') NOT NULL
);

CREATE TABLE IF NOT EXISTS ustadz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    bidang VARCHAR(100),
    no_hp VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    ustadz_id INT,
    level VARCHAR(50),
    kapasitas INT,
    FOREIGN KEY (ustadz_id) REFERENCES ustadz(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS santri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    nis VARCHAR(20) UNIQUE,
    nama VARCHAR(100) NOT NULL,
    orang_tua VARCHAR(100),
    kelas_id INT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    alamat TEXT,
    no_hp VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS presensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    santri_id INT NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('hadir', 'sakit', 'izin', 'alpha') NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    santri_id INT NOT NULL,
    materi VARCHAR(100) NOT NULL,
    nilai INT NOT NULL,
    predikat CHAR(1),
    tanggal_input DATE NOT NULL,
    FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    santri_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jenis VARCHAR(100) NOT NULL,
    jumlah DECIMAL(10, 2) NOT NULL,
    status ENUM('lunas', 'pending', 'belum_bayar') DEFAULT 'pending',
    bukti_pembayaran VARCHAR(255),
    FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    kategori VARCHAR(50),
    tanggal DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kelas_id INT NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    materi VARCHAR(100),
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE
);
