-- Active: 1744639830308@@localhost@3306@db_xbundle
-- =============================================
-- DATABASE X-BUNDLE (FINAL STRUCTURE)
-- =============================================
USE db_xbundle;
-- 1. HAPUS TABEL LAMA (URUTAN PENTING KARENA RELASI)
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS chats;
DROP TABLE IF EXISTS bundles;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- 2. BUAT TABEL USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Disimpan dalam bentuk Hash
    nama_toko VARCHAR(100),
    kategori_bisnis VARCHAR(50) DEFAULT 'Lainnya',
    alamat_toko TEXT,
    deskripsi_toko TEXT, 
    no_hp VARCHAR(20),   
    foto_profil VARCHAR(255) DEFAULT 'default.jpg',
    role ENUM('admin', 'umkm') DEFAULT 'umkm',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. BUAT TABEL PRODUCTS
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
    -- Relasi: Jika User dihapus, Produk ikut terhapus
    CONSTRAINT fk_products_user 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. BUAT TABEL BUNDLES
CREATE TABLE bundles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembuat_id INT NOT NULL, -- User yang mengajak
    mitra_id INT NOT NULL,   -- User yang diajak
    produk_pembuat_id INT NULL, 
    produk_mitra_id INT NULL,
    nama_bundle VARCHAR(150),
    harga_bundle DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'active', 'rejected', 'cancelled', 'finished') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Relasi: Jika User dihapus, Bundle ikut terhapus
    CONSTRAINT fk_bundles_pembuat 
        FOREIGN KEY (pembuat_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bundles_mitra 
        FOREIGN KEY (mitra_id) REFERENCES users(id) ON DELETE CASCADE,
    -- Relasi: Jika Produk dihapus, set NULL di bundle (biar history bundle tetap ada tapi produk kosong)
    CONSTRAINT fk_bundles_prod1 
        FOREIGN KEY (produk_pembuat_id) REFERENCES products(id) ON DELETE SET NULL,
    CONSTRAINT fk_bundles_prod2 
        FOREIGN KEY (produk_mitra_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. BUAT TABEL CHATS
CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL, 
    sender_id INT NOT NULL, 
    message TEXT NOT NULL,
    attachment VARCHAR(255) NULL DEFAULT NULL,
    attachment_type ENUM('image', 'file') NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Relasi: Jika Bundle dihapus, Chat hilang
    CONSTRAINT fk_chats_bundle 
        FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
    -- Relasi: Jika User dihapus, Chat dia hilang
    CONSTRAINT fk_chats_sender 
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. BUAT TABEL VOUCHERS
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
    -- Relasi: Jika Bundle dihapus, Voucher hilang
    CONSTRAINT fk_vouchers_bundle 
        FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- DATA DUMMY (UNTUK TEST)
-- =============================================

-- Password default: '123456' (Hash bcrypt)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (nama_lengkap, email, password, role, nama_toko, kategori_bisnis) VALUES 
('Super Admin', 'admin@xbundle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'X-Bundle HQ', 'Teknologi'),
('Budi Kopi', 'budi@kopi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'umkm', 'Kopi Senja', 'Kuliner (FnB)'),
('Siti Roti', 'siti@roti.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'umkm', 'Roti Bunda', 'Kuliner (FnB)');

INSERT INTO products (user_id, nama_produk, kategori, satuan, harga, stok, deskripsi) VALUES 
(2, 'Es Kopi Susu', 'minuman', 'cup', 18000, 50, 'Kopi susu gula aren kekinian.'),
(3, 'Roti Bakar Coklat', 'makanan', 'porsi', 15000, 30, 'Roti bakar tebal topping melimpah.');