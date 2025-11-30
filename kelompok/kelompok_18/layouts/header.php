<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['status']) && $_SESSION['status'] == 'login';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Bundle | Platform Kolaborasi UMKM</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/style_global.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand" href="/index.php"><i class="fa-solid fa-box-open"></i> X-Bundle</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link text-white" href="/index.php">Beranda</a></li>

        <?php if ($isLoggedIn): ?>
            <li class="nav-item"><a class="nav-link text-white" href="/partner/index.php">Cari Partner</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/produk/index.php">Produk Saya</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/voucher/index.php">Voucher</a></li>
            <li class="nav-item"><a class="nav-link btn btn-danger btn-sm ms-2 px-3 text-white" href="/auth/logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link btn btn-light text-primary btn-sm ms-2 px-3 fw-bold" href="/auth/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container" style="min-height: 80vh;">