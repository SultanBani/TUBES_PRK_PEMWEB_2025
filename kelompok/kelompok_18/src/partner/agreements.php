<?php
include '../config/koneksi.php';
include '../layouts/header.php';

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) { echo "<script>window.location='../auth/login.php';</script>"; exit; }

$query = "SELECT b.*, 
          u1.nama_toko as toko1, u2.nama_toko as toko2,
          p1.nama_produk as prod1, p1.gambar as img1,
          p2.nama_produk as prod2, p2.gambar as img2
          FROM bundles b
          JOIN users u1 ON b.pembuat_id = u1.id
          JOIN users u2 ON b.mitra_id = u2.id
          LEFT JOIN products p1 ON b.produk_pembuat_id = p1.id
          LEFT JOIN products p2 ON b.produk_mitra_id = p2.id
          WHERE (b.pembuat_id = '$my_id' OR b.mitra_id = '$my_id') 
          AND b.status = 'active'
          AND b.produk_pembuat_id IS NOT NULL 
          AND b.produk_mitra_id IS NOT NULL
          ORDER BY b.created_at DESC"; 

$result = mysqli_query($koneksi, $query);
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<style>

    .menu-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }
    .btn-menu {
        font-size: 0.9rem;
        white-space: nowrap;
    }

    
    @media (max-width: 768px) {
        .img-container {
            height: 180px !important; 
            border-bottom: 1px solid #eee;
        }
        
        .action-column {
            border-top: 1px solid #eee; 
            padding-top: 15px !important;
            flex-direction: row !important; 
            gap: 10px;
        }
        
        .border-start-md {
            border-left: none !important;
        }
    }

    @media (min-width: 769px) {
        .border-start-md {
            border-left: 1px solid #dee2e6 !important;
        }
    }
</style>

<div class="partner-header">
    <div class="container">
        <h2 class="fw-bold mb-0">Kesepakatan Produk</h2>
        <p class="text-muted mt-2">Daftar bundle yang sudah disepakati dan tayang di katalog.</p>
    </div>
</div>

<div class="container mb-5" style="max-width: 1000px;">

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
        <a href="agreements.php" class="btn-menu active">
            <i class="fa fa-handshake"></i> Produk Deal
        </a>
        <a href="history.php" class="btn-menu">
            <i class="fa fa-history"></i> Riwayat
        </a>
    </div>

    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-12"> <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            
                            <div class="col-12 col-md-3 d-flex img-container" style="height: 150px;">
                                <div class="w-50" style="background: url('../assets/img/<?= $row['img1'] ?? 'no-image.jpg' ?>') center/cover;"></div>
                                <div class="w-50" style="background: url('../assets/img/<?= $row['img2'] ?? 'no-image.jpg' ?>') center/cover; border-left: 2px solid white;"></div>
                            </div>

                            <div class="col-12 col-md-7 p-4">
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['nama_bundle']) ?></h5>
                                <p class="text-muted small mb-2">
                                    Mitra: <span class="fw-bold text-primary">
                                        <?= ($row['pembuat_id'] == $my_id) ? $row['toko2'] : $row['toko1'] ?>
                                    </span>
                                </p>
                                
                                <div class="d-flex flex-wrap gap-2 gap-md-3 text-secondary small mb-3">
                                    <span><i class="fa-solid fa-box text-success"></i> <?= $row['prod1'] ?></span>
                                    <span>+</span>
                                    <span><i class="fa-solid fa-box text-success"></i> <?= $row['prod2'] ?></span>
                                </div>

                                <h4 class="fw-bold text-danger mb-0">Rp <?= number_format($row['harga_bundle']) ?></h4>
                            </div>

                            <div class="col-12 col-md-2 p-3 text-center border-start-md action-column d-flex flex-md-column justify-content-center">
                                <a href="chat_room.php?bundle_id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill w-100 mb-0 mb-md-2 flex-fill">
                                    <i class="fa fa-comments"></i> Chat
                                </a>
                                
                                <button type="button" class="btn btn-light text-danger btn-sm rounded-pill w-100 flex-fill" data-bs-toggle="modal" data-bs-target="#modalBatal<?= $row['id'] ?>">
                                    <i class="fa fa-times-circle"></i> Batal
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalBatal<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h6 class="modal-title fw-bold">Batalkan Kesepakatan?</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="proses_partner.php" method="POST">
                            <input type="hidden" name="action" value="cancel_bundle">
                            <input type="hidden" name="bundle_id" value="<?= $row['id'] ?>">
                            <div class="modal-body text-center p-4">
                                <p class="mb-3">Anda yakin ingin membatalkan bundle <strong><?= htmlspecialchars($row['nama_bundle']) ?></strong>?</p>
                                <p class="small text-muted">Produk ini akan dihapus dari katalog dan status kerjasama akan dihentikan.</p>
                            </div>
                            <div class="modal-footer justify-content-center border-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Kembali</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Ya, Batalkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 shadow-sm d-inline-block">
                    <i class="fa-solid fa-file-circle-xmark fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold text-dark">Belum ada kesepakatan produk.</h5>
                    <p class="text-muted small">Silakan masuk ke menu Chat dan atur produk bundle.</p>
                    <a href="my_bundles.php" class="btn btn-primary rounded-pill px-4 btn-sm">Ke Daftar Mitra</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>