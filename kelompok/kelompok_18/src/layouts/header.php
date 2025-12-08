<?php
// Cek session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek Base URL
if (!isset($base_url)) {
    if (file_exists(__DIR__ . '/../config/koneksi.php')) {
        include __DIR__ . '/../config/koneksi.php';
    } else {
        $base_url = "http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_18/src";
    }
}

$isLoggedIn = isset($_SESSION['user_id']);
$namaUser   = isset($_SESSION['nama']) ? explode(' ', $_SESSION['nama'])[0] : 'User';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Bundle | Platform Kolaborasi UMKM</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Custom Global -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_global.css">
    
    <style>
        body { 
            padding-top: 80px; 
            font-family: 'Poppins', sans-serif; /* Font Modern */
        }
        
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px); /* Efek blur modern */
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        /* --- LOGO HITAM --- */
        .brand-logo {
            color: #212529 !important; /* Hitam Pekat */
            font-size: 1.6rem;
            letter-spacing: -0.5px;
        }

        /* --- NAVIGASI MODERN --- */
        .nav-link-modern {
            color: #555 !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 5px 0 !important;
            margin: 0 15px;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link-modern:hover, .nav-link-modern.active {
            color: #ED7D31 !important; /* Warna Oranye saat hover */
        }

        /* Efek Garis Bawah Animasi */
        .nav-link-modern::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #ED7D31;
            transition: width 0.3s ease;
        }

        .nav-link-modern:hover::after {
            width: 100%;
        }

        /* Tombol Login/Akun */
        .btn-nav-action {
            background: linear-gradient(135deg, #ED7D31, #d66a20);
            color: white !important;
            border: none;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(237, 125, 49, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-nav-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(237, 125, 49, 0.4);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
  <div class="container">
    
    <!-- LOGO HITAM -->
    <a class="navbar-brand fw-bold brand-logo" href="<?php echo $base_url; ?>/index.php">
        <i class="fa-solid fa-box-open me-2"></i>X-Bundle
    </a>
    
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <li class="nav-item">
            <a class="nav-link nav-link-modern" href="<?php echo $base_url; ?>/katalog.php">Katalog Produk</a>
        </li>

        <?php if ($isLoggedIn): ?>
            <!-- MENU LOGIN -->
            <li class="nav-item">
                <a class="nav-link nav-link-modern" href="<?php echo $base_url; ?>/partner/index.php">Cari Partner</a>
            </li>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-modern" href="<?php echo $base_url; ?>/admin/index.php">Admin Panel</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-modern" href="<?php echo $base_url; ?>/produk/index.php">Dashboard</a>
                </li>
            <?php endif; ?>

            <li class="nav-item dropdown ms-3">
                <a class="nav-link dropdown-toggle btn-nav-action text-white" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user-circle me-1"></i> <?= $namaUser ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 rounded-4 overflow-hidden">
                    <li><h6 class="dropdown-header bg-light py-2">Akun Saya</h6></li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin'): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo $base_url; ?>/auth/profil.php"><i class="fa-solid fa-store me-2 text-muted"></i> Profil Toko</a></li>
                        <li><a class="dropdown-item py-2" href="<?php echo $base_url; ?>/partner/my_bundles.php"><i class="fa-solid fa-handshake me-2 text-muted"></i> Kolaborasi</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider my-0"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger fw-bold" href="<?php echo $base_url; ?>/auth/logout.php" onclick="return confirm('Keluar dari aplikasi?')">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>

        <?php else: ?>
            <!-- MENU TAMU -->
            <li class="nav-item">
                <a class="nav-link nav-link-modern" href="<?php echo $base_url; ?>/index.php">Beranda</a>
            </li>
            <li class="nav-item ms-3">
                <a class="nav-link btn-nav-action" href="<?php echo $base_url; ?>/auth/login.php">
                    Login
                </a>
            </li>
        <?php endif; ?>

      </ul>
    </div>
  </div> 
</nav>