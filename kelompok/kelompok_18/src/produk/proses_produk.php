<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

function uploadGambar($file) {
    $target_dir = "../assets/img/";
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 2 * 1024 * 1024;
    
    if ($file['error'] == 4) {
        return null; 
    }
    
    if ($file['error'] != 0) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        $_SESSION['error'] = "Ukuran gambar terlalu besar. Maksimal 2MB.";
        return false;
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error'] = "Format file tidak didukung. Gunakan JPG atau PNG.";
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'produk_' . time() . '_' . uniqid() . '.' . $extension;
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $filename;
    }
    
    return false;
}

if ($action == 'tambah') {
    $nama_produk = trim($_POST['nama_produk']);
    $kategori = $_POST['kategori'];
    $satuan = trim($_POST['satuan']);
    $harga = (int)$_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    
    if (empty($nama_produk) || empty($kategori) || empty($satuan) || $harga <= 0) {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar.";
        header('Location: tambah.php');
        exit;
    }
    
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $gambar = uploadGambar($_FILES['gambar']);
        if ($gambar === false) {
            header('Location: tambah.php');
            exit;
        }
    }
    
    $query = "INSERT INTO produk (user_id, nama_produk, kategori, satuan, harga, deskripsi, gambar, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssiis", $user_id, $nama_produk, $kategori, $satuan, $harga, $deskripsi, $gambar);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Produk berhasil ditambahkan!";
        header('Location: index.php');
    } else {
        $_SESSION['error'] = "Gagal menambahkan produk. Silakan coba lagi.";
        header('Location: tambah.php');
    }
    
    $stmt->close();
}

elseif ($action == 'edit') {
    $produk_id = (int)$_POST['id'];
    $nama_produk = trim($_POST['nama_produk']);
    $kategori = $_POST['kategori'];
    $satuan = trim($_POST['satuan']);
    $harga = (int)$_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];
    
    if (empty($nama_produk) || empty($kategori) || empty($satuan) || $harga <= 0) {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar.";
        header('Location: edit.php?id=' . $produk_id);
        exit;
    }
    
    $check_query = "SELECT id FROM produk WHERE id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $produk_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        $_SESSION['error'] = "Produk tidak ditemukan atau bukan milik Anda.";
        header('Location: index.php');
        exit;
    }
    $check_stmt->close();
    
    $gambar = $gambar_lama;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        $new_gambar = uploadGambar($_FILES['gambar']);
        if ($new_gambar === false) {
            header('Location: edit.php?id=' . $produk_id);
            exit;
        }
        
        if ($gambar_lama && file_exists("../assets/img/" . $gambar_lama)) {
            unlink("../assets/img/" . $gambar_lama);
        }
        
        $gambar = $new_gambar;
    }
    
    $query = "UPDATE produk SET nama_produk = ?, kategori = ?, satuan = ?, harga = ?, deskripsi = ?, gambar = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssisiii", $nama_produk, $kategori, $satuan, $harga, $deskripsi, $gambar, $produk_id, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Produk berhasil diupdate!";
        header('Location: index.php');
    } else {
        $_SESSION['error'] = "Gagal mengupdate produk. Silakan coba lagi.";
        header('Location: edit.php?id=' . $produk_id);
    }
    
    $stmt->close();
}

else {
    header('Location: index.php');
}

$conn->close();
?>