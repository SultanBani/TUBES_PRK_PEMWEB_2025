<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
// Proteksi: Kalau bukan admin, jangan kasih liat navbar ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Akses Ditolak.");
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
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_global.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning" href="#">
        <i class="fa-solid fa-shield-halved"></i> ADMIN PANEL
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/admin/index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/admin/users.php">Kelola User</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/admin/laporan.php">Laporan Global</a></li>
        <li class="nav-item">
            <a class="nav-link btn btn-danger btn-sm text-white ms-3" href="<?php echo $base_url; ?>/auth/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container" style="min-height: 80vh; margin-top: 100px;">