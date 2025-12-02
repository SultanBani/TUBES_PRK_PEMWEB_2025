<?php
session_start();
include '../config/koneksi.php';

$aksi = $_GET['aksi'];

// --- 1. PROSES LOGIN (UPDATED) ---
if ($aksi == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        if ($password == $data['password']) {
            // Set Session
            $_SESSION['status'] = 'login';
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nama'] = $data['nama_lengkap'];
            $_SESSION['role'] = $data['role'];

            // LOGIKA PEMISAH ROLE (BARU)
            if ($data['role'] == 'admin') {
                header("Location: ../admin/index.php"); // Admin ke sini
            } else {
                header("Location: ../produk/index.php"); // UMKM ke sini
            }
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
        header("Location: login.php");
    }
}

// --- 2. PROSES REGISTER (SAMA SEPERTI SEBELUMNYA) ---
elseif ($aksi == 'register') {
    $nama     = $_POST['nama_lengkap'];
    $toko     = $_POST['nama_toko'];
    $email    = $_POST['email'];
    $password = $_POST['password']; 
    $alamat   = $_POST['alamat'];

    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['error'] = "Email sudah digunakan!";
        header("Location: register.php");
        exit;
    }

    $query = "INSERT INTO users (nama_lengkap, nama_toko, email, password, alamat_toko, role) 
              VALUES ('$nama', '$toko', '$email', '$password', '$alamat', 'umkm')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Gagal daftar: " . mysqli_error($koneksi);
        header("Location: register.php");
    }
}

// --- 3. PROSES UPDATE PROFIL (BARU!) ---
elseif ($aksi == 'update_profil') {
    $id_user    = $_SESSION['user_id'];
    $nama_toko  = $_POST['nama_toko'];
    $alamat     = $_POST['alamat_toko'];
    $deskripsi  = $_POST['deskripsi_toko'];
    $no_hp      = $_POST['no_hp'];

    $query = "UPDATE users SET 
              nama_toko='$nama_toko', 
              alamat_toko='$alamat',
              deskripsi_toko='$deskripsi',
              no_hp='$no_hp'
              WHERE id='$id_user'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Profil toko berhasil diperbarui!";
        header("Location: profil.php");
    } else {
        $_SESSION['error'] = "Gagal update: " . mysqli_error($koneksi);
        header("Location: profil.php");
    }
}

// --- 4. LOGOUT ---
elseif ($aksi == 'logout') {
    session_destroy();
    header("Location: ../index.php");
}
?>