isi databasenya ini -- 1. Tabel Users (Untuk Login Pelapak & Admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_toko VARCHAR(100),
    alamat_toko TEXT,
    role ENUM('admin', 'umkm') DEFAULT 'umkm',
    foto_profil VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Products (Barang Dagangan)
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

-- 3. Tabel Bundles (Kolaborasi Antar Toko)
CREATE TABLE bundles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembuat_id INT NOT NULL, -- User A (Yang Ngajak)
    mitra_id INT NOT NULL,   -- User B (Yang Diajak)
    produk_pembuat_id INT NOT NULL,
    produk_mitra_id INT NOT NULL,
    nama_bundle VARCHAR(150),
    harga_bundle DECIMAL(10,2),
    status ENUM('pending', 'active', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pembuat_id) REFERENCES users(id),
    FOREIGN KEY (mitra_id) REFERENCES users(id),
    FOREIGN KEY (produk_pembuat_id) REFERENCES products(id),
    FOREIGN KEY (produk_mitra_id) REFERENCES products(id)
);

-- 4. Tabel Vouchers (Kode Unik untuk Pembeli)
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL,
    kode_unik VARCHAR(20) NOT NULL UNIQUE, -- Misal: XB-KopiRoti-001
    status ENUM('available', 'used') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
    ALTER TABLE vouchers 
ADD COLUMN expired_at DATE AFTER created_at;
);


-- (Opsional) Akun Admin Dummy biar bisa langsung Login
INSERT INTO users (nama_lengkap, email, password, role) 
VALUES ('Super Admin', 'admin@xbundle.com', 'admin123', 'admin');
-- Password masih polosan (belum di-hash) buat testing awal doang.

