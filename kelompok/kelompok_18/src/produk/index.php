<?php 
// 1. Panggil Koneksi & Header
include '../config/koneksi.php'; 
include '../layouts/header.php'; 

// 2. CEK KEAMANAN (PENTING!)
// Kalau user belum login, tendang balik ke halaman login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    echo "<script>alert('Akses Ditolak! Harap Login Dulu.'); location.href='../auth/login.php';</script>";
    exit;
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            
            <div class="p-5 mb-4 bg-light rounded-3 shadow-sm border">
                <div class="container-fluid py-2">
                    <h1 class="display-5 fw-bold text-primary">Halo, <?php echo $_SESSION['nama']; ?>! 👋</h1>
                    <p class="col-md-8 fs-4">Selamat datang di Dashboard Toko <strong>"<?php echo $_SESSION['toko']; ?>"</strong>.</p>
                    
                    <hr>
                    
                    <p>Status Login Anda:</p>
                    <ul>
                        <li><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></li>
                        <li><strong>Email:</strong> (Tersimpan di database)</li>
                        <li><strong>Role:</strong> <?php echo $_SESSION['role']; ?></li>
                    </ul>

                    <a href="../auth/logout.php" class="btn btn-danger btn-lg mt-3">Test Logout</a>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Area Kerja Person 2 (Produk)</h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-boxes-stacked fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Di sini nanti tempat Tabel CRUD Produk.</h5>
                    <p class="text-muted">File ini (produk/index.php) nanti akan diedit oleh temanmu.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
// 3. Panggil Footer
include '../layouts/footer.php'; 
?>