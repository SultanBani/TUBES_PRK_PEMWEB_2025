<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// Session Check
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
if (!$my_id) echo "<script>window.location='../auth/login.php';</script>";

// Filter Logic
$where = "WHERE role='umkm' AND id != '$my_id'";
if (isset($_GET['q'])) {
    $q = mysqli_real_escape_string($koneksi, $_GET['q']);
    $where .= " AND (nama_toko LIKE '%$q%' OR nama_lengkap LIKE '%$q%')";
}

$query = "SELECT * FROM users $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<div class="hero-wrapper text-center">
    <div class="container">
        <h1 class="display-5 fw-bold text-dark">Cari Jodoh <span class="text-primary">Bisnis.</span></h1>
        <p class="text-muted">Temukan mitra UMKM untuk bundling produk bersama.</p>
    </div>
</div>

<div class="container" style="margin-top: -50px; position: relative; z-index: 20;">
    <div class="hero-card">
        <form action="" method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control rounded-pill px-4" placeholder="Cari Toko Kopi, Snack..." value="<?= $_GET['q'] ?? '' ?>">
            <button class="btn btn-primary rounded-pill px-4">Cari</button>
        </form>
    </div>
</div>

<div class="container mb-5">
    <ul class="nav nav-pills justify-content-center mb-4 gap-3">
        <li class="nav-item"><a href="index.php" class="nav-link active rounded-pill">Jelajahi Mitra</a></li>
        <li class="nav-item"><a href="request.php" class="nav-link bg-white text-dark border rounded-pill">Request Masuk</a></li>
        <li class="nav-item"><a href="my_bundles.php" class="nav-link bg-white text-dark border rounded-pill">Kolaborasi Aktif</a></li>
    </ul>

    <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3">
            <div class="card card-partner">
                <div class="card-img-top-wrapper">
                    <img src="<?= !empty($row['foto_profil']) ? '../assets/uploads/'.$row['foto_profil'] : 'https://ui-avatars.com/api/?name='.$row['nama_toko'].'&background=random' ?>">
                    <span class="badge bg-light text-primary position-absolute top-0 end-0 m-2 shadow-sm">
                        <?= $row['kategori_bisnis'] ?? 'UMKM' ?>
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold mb-1"><?= $row['nama_toko'] ?></h5>
                    <p class="small text-muted mb-3"><i class="fa fa-map-marker-alt text-danger"></i> <?= $row['alamat_toko'] ?? 'Lokasi belum diset' ?></p>
                    <button class="btn btn-outline-primary w-100 rounded-pill btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjak<?= $row['id'] ?>">
                        Ajak Kolaborasi
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAjak<?= $row['id'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="proses_partner.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Ajak <?= $row['nama_toko'] ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="create_request">
                            <input type="hidden" name="mitra_id" value="<?= $row['id'] ?>">
                            <label class="small text-muted fw-bold">PESAN SAPAAN</label>
                            <textarea name="pesan_awal" class="form-control bg-light" rows="3" required placeholder="Halo, ayo kita buat paket bundling..."></textarea>
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-primary rounded-pill w-100">Kirim Ajakan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include '../layouts/footer.php'; ?>