<?php
include '../config/koneksi.php';
include '../layouts/header.php';

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>window.location='../auth/login.php';</script>";
    exit;
}

$query = "SELECT b.*, 
          u.nama_toko as mitra_nama, 
          u.foto_profil as mitra_foto,
          p1.nama_produk as produk_saya,
          p2.nama_produk as produk_mitra
          FROM bundles b
          JOIN users u ON (u.id = CASE WHEN b.pembuat_id = '$my_id' THEN b.mitra_id ELSE b.pembuat_id END)
          LEFT JOIN products p1 ON p1.id = b.produk_pembuat_id
          LEFT JOIN products p2 ON p2.id = b.produk_mitra_id
          WHERE (b.pembuat_id = '$my_id' OR b.mitra_id = '$my_id') 
          AND b.status IN ('finished', 'rejected', 'cancelled') 
          ORDER BY b.created_at DESC";

$result = mysqli_query($koneksi, $query);
?>

<link rel="stylesheet" href="../assets/css/style_partner.css?v=<?= time(); ?>">

<style>
    .menu-nav {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap; 
    }

    .btn-menu {
        font-size: 0.9rem;
        white-space: nowrap; 
    }

    .history-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        padding: 20px;
        transition: 0.3s;
        position: relative;
        overflow: hidden;
    }
    
    .history-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transform: translateY(-5px);
    }

    .status-strip {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
    }
    .strip-finished { background-color: #4CAF50; } 
    .strip-rejected { background-color: #F44336; } 
    .strip-cancelled { background-color: #9E9E9E; } 

    .badge-status-history {
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 700;
    }

    @media (max-width: 576px) {
        .history-card {
            padding: 15px;
        }
        .partner-header h2 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="partner-header">
    <div class="container">
        <h3 class="fw-bold mb-0" style="color: #4F4A45;">Riwayat Kolaborasi</h3>
        <p class="text-muted mt-2">Arsip kolaborasi yang telah selesai atau dibatalkan.</p>
    </div>
</div>

<div class="container mb-5" style="max-width: 1200px;">

    <div class="menu-nav">
        <a href="index.php" class="btn-menu">
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
        <a href="history.php" class="btn-menu active">
            <i class="fa fa-history"></i> Riwayat
        </a>
    </div>

    <div class="row g-3 g-md-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $st = $row['status'];
                    $stripClass = '';
                    $badgeStyle = '';
                    $label = '';

                    if ($st == 'finished') {
                        $stripClass = 'strip-finished';
                        $badgeStyle = 'background: #E8F5E9; color: #2E7D32;';
                        $label = 'SELESAI';
                    } elseif ($st == 'rejected') {
                        $stripClass = 'strip-rejected';
                        $badgeStyle = 'background: #FFEBEE; color: #C62828;';
                        $label = 'DITOLAK';
                    } else {
                        $stripClass = 'strip-cancelled';
                        $badgeStyle = 'background: #F5F5F5; color: #757575;';
                        $label = 'DIBATALKAN';
                    }

                    $foto = !empty($row['mitra_foto']) && file_exists('../assets/uploads/'.$row['mitra_foto']) 
                            ? '../assets/uploads/'.$row['mitra_foto'] 
                            : 'https://ui-avatars.com/api/?name='.urlencode($row['mitra_nama']).'&background=D7CCC8&color=6D4C41';
                ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="history-card h-100">
                        <div class="status-strip <?= $stripClass ?>"></div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= $foto ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($row['mitra_nama'] ?? 'Unknown') ?></h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Partner</small>
                                </div>
                            </div>
                            <span class="badge-status-history" style="<?= $badgeStyle ?>">
                                <?= $label ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <h5 class="fw-bold mb-1" style="color: var(--primary);">
                                <?= htmlspecialchars($row['nama_bundle'] ?? 'Bundle Tanpa Nama') ?>
                            </h5>
                            <p class="text-muted small mb-0">
                                <?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                            </p>
                        </div>

                        <div class="bg-light p-2 rounded mb-3">
                            <small class="d-block text-muted mb-1">Produk Kolaborasi:</small>
                            <div class="d-flex align-items-center small fw-bold text-dark">
                                <span><?= htmlspecialchars($row['produk_saya'] ?? '-') ?></span>
                                <i class="fa fa-plus mx-2 text-warning"></i>
                                <span><?= htmlspecialchars($row['produk_mitra'] ?? '-') ?></span>
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="detail.php?bundle_id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3 opacity-50">
                    <i class="fa fa-history fa-4x text-muted"></i>
                </div>
                <h5 class="fw-bold text-secondary">Belum Ada Riwayat</h5>
                <p class="text-muted">
                    Bundle yang "Aktif" ada di tab sebelah.<br>
                    Halaman ini hanya untuk bundle yang sudah Selesai, Ditolak, atau Batal.
                </p>
                <a href="my_bundles.php" class="btn btn-primary rounded-pill px-4 mt-2">
                    Cek Kolaborasi Aktif
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>