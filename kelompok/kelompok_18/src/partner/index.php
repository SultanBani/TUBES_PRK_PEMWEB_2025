<?php
include '../config/koneksi.php';
include '../layouts/header.php';

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) echo "<script>window.location='../auth/login.php';</script>";

$where = "WHERE role='umkm' AND id != '$my_id'";

// Cari Nama Toko (Search)
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = mysqli_real_escape_string($koneksi, $_GET['q']);
    $where .= " AND (nama_toko LIKE '%$q%' OR nama_lengkap LIKE '%$q%')";
}

// Filter Kategori (Dropdown)
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kat = mysqli_real_escape_string($koneksi, $_GET['kategori']);
    $where .= " AND kategori_bisnis LIKE '%$kat%'"; 
}

$query = "SELECT * FROM users $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<style>
    .menu-nav {
        flex-wrap: wrap; 
        gap: 10px;
    }
    
    .btn-menu {
        white-space: nowrap;
    }
    
    @media (max-width: 768px) {
        .card-partner { margin-bottom: 15px; }
    }
</style>

<div class="partner-header">
    <div class="container">
        <h2 class="fw-bold mb-2">Cari Partner Bisnis</h2>
        <p class="text-muted">Temukan mitra UMKM lain untuk kolaborasi.</p>

        <form action="" method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Cari nama toko..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            
            <select name="kategori" class="filter-select">
                <option value="">Semua Kategori</option>
                <option value="FnB" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'FnB') ? 'selected' : '' ?>> FnB</option>
                <option value="Fashion" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Fashion') ? 'selected' : '' ?>> Fashion</option>
                <option value="Jasa" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Jasa') ? 'selected' : '' ?>> Jasa</option>
                <option value="Craft" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Craft') ? 'selected' : '' ?>> Craft</option>
            </select>

            <button type="submit" class="btn-cari">Cari</button>
        </form>
    </div>
</div>

<div class="container mb-5">
    
    <div class="menu-nav d-flex justify-content-center">
        <a href="index.php" class="btn-menu active">
            <i class="fa fa-store"></i> Jelajahi Mitra
        </a>
        <a href="request.php" class="btn-menu">
            <i class="fa fa-envelope"></i> Inbox Request
            <?php 
            $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE mitra_id='$my_id' AND status='pending'");
            if(mysqli_num_rows($cek) > 0) echo "<span class='badge bg-danger rounded-pill ms-1'>".mysqli_num_rows($cek)."</span>";
            ?>
        </a>
        <a href="my_bundles.php" class="btn-menu">
            <i class="fa fa-handshake"></i> Kolaborasi Aktif
        </a>
        <a href="agreements.php" class="btn-menu">
            <i class="fa fa-handshake"></i> Produk Deal
        </a>
        <a href="history.php" class="btn-menu">
            <i class="fa fa-history"></i> Riwayat
        </a>
    </div>

    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card-partner h-100 shadow-sm border">
                    <div class="card-img-wrapper">
                        <img src="<?= !empty($row['foto_profil']) ? '../assets/uploads/'.$row['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($row['nama_toko']).'&background=random' ?>">
                        <div class="card-category"><?= htmlspecialchars($row['kategori_bisnis'] ?? 'Umum') ?></div>
                    </div>
                    
                    <div class="card-body-custom">
                        <h5 class="fw-bold mb-1 text-dark text-truncate"><?= htmlspecialchars($row['nama_toko']) ?></h5>
                        <p class="small text-muted mb-2 text-truncate">
                            <i class="fa fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($row['alamat_toko'] ?? '-') ?>
                        </p>
                        
                        <p class="small text-secondary mb-3" style="min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= !empty($row['deskripsi_toko']) ? $row['deskripsi_toko'] : 'Tidak ada deskripsi.' ?>
                        </p>

                        <div class="d-grid gap-2">
                            <button class="btn-collab" data-bs-toggle="modal" data-bs-target="#modalAjak<?= $row['id'] ?>">
                                Ajak Kolaborasi
                            </button>
                            <a href="chat_room.php?partner_id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-bold py-2">
                                <i class="fa fa-comments me-1"></i> Ajak Diskusi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalAjak<?= $row['id'] ?>">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Ajak <?= htmlspecialchars($row['nama_toko']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="proses_partner.php" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="create_request">
                                <input type="hidden" name="mitra_id" value="<?= $row['id'] ?>">
                                <label class="form-label small fw-bold">Pesan Sapaan:</label>
                                <textarea name="pesan_awal" class="form-control" rows="3" required placeholder="Halo, mari berkolaborasi..."></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-cari w-100">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <p>Mitra tidak ditemukan.</p>
                <a href="index.php" class="text-decoration-none">Reset Filter</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>