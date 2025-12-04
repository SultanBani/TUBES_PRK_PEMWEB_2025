<?php
// Cek session kalau belum mulai
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Proteksi Keamanan
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | X-Bundle</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_admin.css">
</head>
<body class="bg-light">

<!-- NAVBAR DARK -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm" style="background-color: #4F4A45;">
  <div class="container">
    
    <!-- Logo Admin -->
    <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>/admin/index.php" style="color: #ED7D31;">
        <i class="fa-solid fa-shield-halved me-2"></i> ADMIN PANEL
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAdmin">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNavAdmin">
      <ul class="navbar-nav ms-auto">
        
        <!-- Menu Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/admin/index.php">
                <i class="fa-solid fa-gauge me-1"></i> Dashboard
            </a>
        </li>
        
        <!-- Menu Kelola User -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/admin/users.php">
                <i class="fa-solid fa-users me-1"></i> Kelola User
            </a>
        </li>
        
        <!-- Menu Laporan -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/admin/laporan.php">
                <i class="fa-solid fa-file-invoice me-1"></i> Laporan Global
            </a>
        </li>
        
        <!-- Tombol Logout (Merah biar kontras) -->
        <li class="nav-item ms-lg-3">
            <a class="nav-link btn btn-danger btn-sm text-white px-4 rounded-pill" href="<?php echo $base_url; ?>/auth/logout.php">
                Logout <i class="fa-solid fa-right-from-bracket ms-1"></i>
            </a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<div class="container" style="min-height: 80vh; margin-top: 100px;">