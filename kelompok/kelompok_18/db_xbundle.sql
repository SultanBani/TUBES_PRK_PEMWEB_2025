-- Active: 1753257938710@@127.0.0.1@3306@db_xbundle
-- DATABASE X-BUNDLE (FINAL VERSION 3.0 - WITH CATEGORIES)

-- Pastikan kita pakai DB yang benar
USE db_xbundle;

-- === 1. Tabel Users (Ditambah deskripsi & no_hp) ===
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_toko VARCHAR(100),
    alamat_toko TEXT,
    deskripsi_toko TEXT, 
    no_hp VARCHAR(20),   
    role ENUM('admin', 'umkm') DEFAULT 'umkm',
    foto_profil VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- === 2. Tabel Products (Ditambah Kategori & Satuan) ===
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    kategori ENUM('makanan', 'minuman', 'jasa', 'fashion', 'kerajinan', 'lainnya') DEFAULT 'lainnya',
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    satuan VARCHAR(50) DEFAULT 'pcs',
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT 'no-image.jpg',
    status_produk ENUM('aktif', 'arsip') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- === 3. Tabel Bundles (Transaksi Kolaborasi) ===
CREATE TABLE bundles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembuat_id INT NOT NULL,
    mitra_id INT NOT NULL,
    produk_pembuat_id INT NOT NULL,
    produk_mitra_id INT NOT NULL,
    nama_bundle VARCHAR(150),
    harga_bundle DECIMAL(10,2),
    status ENUM('pending', 'active', 'rejected', 'finished') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pembuat_id) REFERENCES users(id),
    FOREIGN KEY (mitra_id) REFERENCES users(id),
    FOREIGN KEY (produk_pembuat_id) REFERENCES products(id),
    FOREIGN KEY (produk_mitra_id) REFERENCES products(id)
);

-- === 4. Tabel Chats (Fitur Chat Room) ===
CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL, 
    sender_id INT NOT NULL, 
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

-- === 5. Tabel Vouchers (Expired Date & Kuota) ===
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL,
    kode_voucher VARCHAR(50) NOT NULL UNIQUE, 
    potongan_harga DECIMAL(10,2) DEFAULT 0,   
    kuota_maksimal INT DEFAULT 100,           
    kuota_terpakai INT DEFAULT 0,            
    expired_at DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
);

-- Insert Akun Admin Default
INSERT INTO users (nama_lengkap, email, password, role, nama_toko) 
VALUES ('Admin', 'admin@xbundle.com', 'admin123', 'admin', 'Kantor Pusat X-Bundle');

-- Insert Akun User Dummy (Buat Contoh di Katalog)
INSERT INTO users (nama_lengkap, email, password, role, nama_toko, deskripsi_toko, alamat_toko) 
VALUES ('Bani', 'bani@kopi.com', '123', 'umkm', 'Kopi Senja', 'Menjual kopi robusta asli lampung', 'Jl. Pagar Alam No 1');

-- Insert Produk Dummy (Biar Katalog Gak Kosong Pas Pertama Dibuka)
INSERT INTO products (user_id, nama_produk, kategori, harga, stok, satuan, deskripsi, gambar)
VALUES (2, 'Kopi Arabika 250gr', 'minuman', 25000, 50, 'bungkus', 'Kopi bubuk asli tanpa campuran', 'no-image.jpg');