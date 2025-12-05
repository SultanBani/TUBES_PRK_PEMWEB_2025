-- Active: 1744639830308@@localhost@3306@db_xbundle
-- DATABASE X-BUNDLE

USE db_xbundle;

-- === BERSIHKAN TABEL LAMA ===
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS chats;
DROP TABLE IF EXISTS bundles;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- === BUAT TABEL BARU ===

-- 1. USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_toko VARCHAR(100),
    kategori_bisnis VARCHAR(50) DEFAULT 'Lainnya',
    alamat_toko TEXT,
    deskripsi_toko TEXT, 
    no_hp VARCHAR(20),   
    foto_profil VARCHAR(255) DEFAULT 'default.jpg',
    role ENUM('admin', 'umkm') DEFAULT 'umkm',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. PRODUCTS
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    kategori ENUM('makanan', 'minuman', 'jasa', 'fashion', 'kerajinan', 'lainnya') DEFAULT 'lainnya',
    satuan VARCHAR(50) DEFAULT 'pcs',
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT 'no-image.jpg',
    status_produk ENUM('aktif', 'arsip') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. BUNDLES (UPDATED: Tambah Status Cancelled)
CREATE TABLE bundles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembuat_id INT NOT NULL,
    mitra_id INT NOT NULL,
    produk_pembuat_id INT, 
    produk_mitra_id INT,
    nama_bundle VARCHAR(150),
    harga_bundle DECIMAL(10,2),
    status ENUM('pending', 'active', 'rejected', 'cancelled', 'finished') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pembuat_id) REFERENCES users(id),
    FOREIGN KEY (mitra_id) REFERENCES users(id),
    FOREIGN KEY (produk_pembuat_id) REFERENCES products(id),
    FOREIGN KEY (produk_mitra_id) REFERENCES products(id)
);

-- 4. Tabel Chats (UPDATED: Support File Upload)
CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL, 
    sender_id INT NOT NULL, 
    message TEXT NOT NULL,
    attachment VARCHAR(255) NULL DEFAULT NULL, -- Nama file (misal: struk.jpg)
    attachment_type ENUM('image', 'file') NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);
);

-- 5. VOUCHERS
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL,
    kode_voucher VARCHAR(50) NOT NULL UNIQUE, 
    potongan_harga DECIMAL(10,2) DEFAULT 0,   
    kuota_maksimal INT DEFAULT 100,           
    kuota_terpakai INT DEFAULT 0,            
    expired_at DATE NULL,
    status ENUM('available', 'used', 'expired') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
);

-- DATA DUMMY
INSERT INTO users (nama_lengkap, email, password, role, nama_toko, kategori_bisnis) 
VALUES ('Admin', 'admin@xbundle.com', 'admin123', 'admin', 'X-Bundle HQ', 'Teknologi');

INSERT INTO users (nama_lengkap, email, password, role, nama_toko, kategori_bisnis, alamat_toko) 
VALUES ('Bani Barista', 'bani@kopi.com', '123456', 'umkm', 'Kopi Pagi', 'Kuliner (FnB)', 'Jl. Melati No 1');

INSERT INTO products (user_id, nama_produk, kategori, satuan, harga, stok, deskripsi)
VALUES (2, 'Es Kopi Susu Gula Aren', 'minuman', 'cup', 18000, 50, 'Kopi susu kekinian.');