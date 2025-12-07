<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// --- FUNGSI UPLOAD GAMBAR ---
function uploadGambar($file) {
    $target_dir = "../assets/img/";
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    // Cek Error
    if ($file['error'] == 4) return null; // Tidak ada file
    if ($file['error'] != 0) return false;
    
    // Validasi Tipe & Ukuran
    if ($file['size'] > $max_size) return false;
    if (!in_array($file['type'], $allowed_types)) return false;
    
    // Generate Nama Unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'prod_' . time() . '_' . uniqid() . '.' . $extension;
    
    if (move_uploaded_file($file['tmp_name'], $target_dir . $filename)) {
        return $filename;
    }
    return false;
}

// --- LOGIKA TAMBAH ---
if ($action == 'tambah') {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori    = $_POST['kategori'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga       = (int)$_POST['harga'];
    $stok        = (int)$_POST['stok'];
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Default Gambar
    $gambar = 'no-image.jpg';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $upload = uploadGambar($_FILES['gambar']);
        if ($upload) {
            $gambar = $upload;
        } else {
            $_SESSION['error'] = "Gagal upload gambar (Max 2MB, JPG/PNG).";
            header('Location: tambah.php');
            exit;
        }
    }
    
    // INSERT KE TABEL PRODUCTS (Sesuai DB Schema)
    $query = "INSERT INTO products (user_id, nama_produk, kategori, satuan, harga, stok, deskripsi, gambar, status_produk, created_at) 
              VALUES ('$user_id', '$nama_produk', '$kategori', '$satuan', '$harga', '$stok', '$deskripsi', '$gambar', 'aktif', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Produk berhasil ditambahkan!";
        header('Location: index.php');
    } else {
        $_SESSION['error'] = "Database Error: " . mysqli_error($koneksi);
        header('Location: tambah.php');
    }
}

// --- LOGIKA EDIT ---
elseif ($action == 'edit') {
    $id          = $_POST['id'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori    = $_POST['kategori'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga       = (int)$_POST['harga'];
    $stok        = (int)$_POST['stok'];
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];
    
    // Validasi Kepemilikan (Security)
    $cek = mysqli_query($koneksi, "SELECT id FROM products WHERE id='$id' AND user_id='$user_id'");
    if (mysqli_num_rows($cek) == 0) {
        $_SESSION['error'] = "Akses ditolak!";
        header('Location: index.php');
        exit;
    }

    $gambar = $gambar_lama;
    
    // Jika ada upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $upload = uploadGambar($_FILES['gambar']);
        if ($upload) {
            $gambar = $upload;
            // Hapus gambar lama jika bukan default
            if ($gambar_lama != 'no-image.jpg' && file_exists("../assets/img/$gambar_lama")) {
                unlink("../assets/img/$gambar_lama");
            }
        }
    }
    
    $query = "UPDATE products SET 
              nama_produk='$nama_produk', 
              kategori='$kategori', 
              satuan='$satuan', 
              harga='$harga', 
              stok='$stok', 
              deskripsi='$deskripsi', 
              gambar='$gambar' 
              WHERE id='$id' AND user_id='$user_id'";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Produk berhasil diperbarui!";
        header('Location: index.php');
    } else {
        $_SESSION['error'] = "Gagal update: " . mysqli_error($koneksi);
        header('Location: edit.php?id='.$id);
    }
}

else {
    header('Location: index.php');
}
?>