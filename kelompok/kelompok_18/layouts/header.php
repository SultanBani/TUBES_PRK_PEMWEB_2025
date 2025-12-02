<?php
// Cek session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek status login user
$isLoggedIn = isset($_SESSION['status']) && $_SESSION['status'] == 'login';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Bundle | Platform Kolaborasi UMKM</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Custom -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_global.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom navbar-light fixed-top shadow-sm">
  <div class="container">
    
    <!-- LOGO (Klik logo masuk dashboard. Kalau tamu, masuk landing page) -->
    <a class="navbar-brand fw-bold" href="<?php echo $isLoggedIn ? $base_url . '/produk/index.php' : $base_url . '/index.php'; ?>">
        <i class="fa-solid fa-box-open"></i> X-Bundle
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        
        <!-- Menu Katalog (Tetap ada buat semua orang) -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/katalog.php">Katalog Produk</a>
        </li>

        <?php if ($isLoggedIn): ?>
            <!-- === MENU KHUSUS MEMBER (Sudah Login) === -->
            <!-- Menu Beranda Dihapus dari sini -->
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>/partner/index.php">Cari Partner</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>/produk/index.php">Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>/voucher/index.php">Laporan</a>
            </li>

            <!-- Dropdown Profil -->
            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle btn btn-outline-primary px-3" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user"></i> Akun
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>/auth/profil.php">Edit Profil Toko</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>/auth/logout.php">Logout</a></li>
                </ul>
            </li>

        <?php else: ?>
            <!-- === MENU TAMU (Belum Login) === -->
            
            <!-- (PINDAHAN) Menu Beranda cuma buat Tamu -->
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>/index.php">Beranda</a>
            </li>

            <li class="nav-item">
                <a class="nav-link btn btn-primary text-white fw-bold btn-sm ms-2 px-3" href="<?php echo $base_url; ?>/auth/login.php">Login</a>
            </li>
        <?php endif; ?>

      </ul>
    </div>
  </div> 
</nav> 

<!-- Container Utama -->
<div class="container" style="min-height: 80vh; margin-top: 100px;">