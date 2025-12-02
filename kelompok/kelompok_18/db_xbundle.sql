-- Active: 1744639830308@@localhost@3306@db_xbundle
-- DATABASE X-BUNDLE (FINAL UPDATE DENGAN FITUR CHAT & EXPIRY)
use db_xbundle;
-- 1. Tabel Users (Ditambah deskripsi & no_hp)
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

-- 2. Tabel Products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT 'no-image.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Tabel Bundles (Transaksi Kolaborasi)
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

-- 4. Tabel Chats (BARU - Untuk Fitur Chat Room)
CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL, 
    sender_id INT NOT NULL, 
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Tabel Vouchers (Ditambah Expired Date)
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL,
    kode_unik VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('available', 'used', 'expired') DEFAULT 'available',
    expired_at DATE NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
);

-- Insert Akun Admin 
INSERT INTO users (nama_lengkap, email, password, role, nama_toko) 
VALUES ('Admin', 'admin@xbundle.com', 'admin123', 'admin', 'Kantor Pusat');