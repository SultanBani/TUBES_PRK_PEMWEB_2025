<?php
include '../config/koneksi.php';
include '../layouts/header.php';

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis. Silakan login ulang.'); window.location='../auth/login.php';</script>";
    exit;
}

$query = "SELECT b.*, 
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.id 
            ELSE u_buat.id 
          END as partner_id_real,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.nama_toko 
            ELSE u_buat.nama_toko 
          END as nama_partner,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.foto_profil 
            ELSE u_buat.foto_profil 
          END as foto_partner,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.kategori_bisnis 
            ELSE u_buat.kategori_bisnis 
          END as kategori_partner,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.alamat_toko 
            ELSE u_buat.alamat_toko 
          END as alamat_partner
          FROM bundles b
          JOIN users u_buat ON b.pembuat_id = u_buat.id
          JOIN users u_mitra ON b.mitra_id = u_mitra.id
          WHERE (b.pembuat_id = '$my_id' OR b.mitra_id = '$my_id') 
          AND b.status = 'active'
          ORDER BY b.created_at DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error Database: " . mysqli_error($koneksi));
}

$displayed_partners = [];
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .menu-nav {
        flex-wrap: wrap; 
        gap: 10px;
    }
    
    .btn-menu {
        font-size: 0.9rem;
        white-space: nowrap;
    }

    @media (max-width: 576px) {
        .partner-header h2 {
            font-size: 1.5rem; 
        }
        .bundle-img-wrapper {
            height: 150px; 
        }
        .btn {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
    }
</style>

<div class="partner-header">
    <div class="container">
        <h2 class="mb-0 fw-bold"><i class="fa fa-handshake me-2"></i> Kolaborasi Aktif</h2>
        <p class="mt-2 mb-0 text-muted">Kelola dan kembangkan kemitraan bisnis Anda dengan mitra terpercaya.</p>
    </div>
</div>

<div class="container mb-5" style="max-width: 1200px;">

    <div class="menu-nav d-flex justify-content-center mb-4">
        <a href="index.php" class="btn-menu">
            <i class="fa fa-store"></i> <span class="d-none d-sm-inline">Jelajahi Mitra</span> <span class="d-inline d-sm-none">Mitra</span>
        </a>
        <a href="request.php" class="btn-menu">
            <i class="fa fa-envelope"></i> Request
            <?php 
            $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE mitra_id='$my_id' AND status='pending'");
            if(mysqli_num_rows($cek) > 0) echo "<span class='badge bg-danger rounded-pill ms-1'>".mysqli_num_rows($cek)."</span>";
            ?>
        </a>
        <a href="my_bundles.php" class="btn-menu active">
            <i class="fa fa-handshake"></i> <span class="d-none d-sm-inline">Kolaborasi Aktif</span> <span class="d-inline d-sm-none">Aktif</span>
        </a>
        <a href="agreements.php" class="btn-menu">
            <i class="fa fa-handshake"></i> Produk Deal
        </a>
        <a href="history.php" class="btn-menu">
            <i class="fa fa-history"></i> Riwayat
        </a>
    </div>

    <div class="row g-3 g-md-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php 
                    // --- LOGIKA FILTER DUPLIKAT ---
                    if (in_array($row['partner_id_real'], $displayed_partners)) {
                        continue; 
                    }
                    $displayed_partners[] = $row['partner_id_real'];
                ?>

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="bundle-card h-100 shadow-sm border">
                        <div class="bundle-img-wrapper position-relative">
                            <img src="<?= !empty($row['foto_partner']) && file_exists('../assets/uploads/'.$row['foto_partner']) 
                                        ? '../assets/uploads/'.$row['foto_partner'] 
                                        : 'https://ui-avatars.com/api/?name='.urlencode($row['nama_partner']).'&background=random&size=300&bold=true' ?>" 
                                 alt="<?= htmlspecialchars($row['nama_partner']) ?>"
                                 class="w-100 h-100 object-fit-cover">
                                 
                            <div class="card-category bg-success shadow-sm position-absolute top-0 end-0 m-2 px-2 py-1 rounded text-white" style="font-size: 0.8rem;">
                                <i class="fa fa-check-circle me-1"></i> Aktif
                            </div>
                        </div>
                        
                        <div class="card-body-custom p-3">
                            <h5 class="fw-bold mb-2 text-dark text-truncate">
                                <?= htmlspecialchars($row['nama_partner']) ?>
                            </h5>
                            
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border me-1">
                                    <i class="fa fa-tag me-1"></i>
                                    <?= htmlspecialchars($row['kategori_partner'] ?? 'Umum') ?>
                                </span>
                            </div>
                            
                            <p class="small text-muted mb-3 text-truncate">
                                <i class="fa fa-map-marker-alt text-danger me-1"></i> 
                                <?= htmlspecialchars($row['alamat_partner'] ?? 'Lokasi tidak tersedia') ?>
                            </p>
                            
                            <div class="alert alert-light border small mb-3 py-2">
                                <i class="fa fa-calendar-alt me-2 text-primary"></i> 
                                <strong>Kolaborasi sejak:</strong><br>
                                <span class="text-muted"><?= date('d F Y', strtotime($row['created_at'])) ?></span>
                            </div>

                            <div class="collaboration-cta bg-light p-2 rounded mb-3 border-start border-4 border-warning">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-comments text-warning me-2"></i>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Mari Berkolaborasi!</h6>
                                </div>
                                <p class="small mb-0 text-muted" style="font-size: 0.8rem;">Diskusikan strategi dan tingkatkan penjualan bersama mitra Anda</p>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="chat_room.php?partner_id=<?= $row['partner_id_real'] ?>" class="btn btn-cari rounded-pill">
                                    <i class="fa fa-comments me-2"></i> Buka Ruang Diskusi
                                </a>
                                
                                <div class="d-grid gap-2">
                                    <a href="manage_deal.php?bundle_id=<?= $row['id'] ?>" class="btn btn-cari rounded-pill btn-sm">
                                        <i class="fa-solid fa-file-contract me-1"></i> Atur Kolaborasi
                                    </a>
                                </div>
                                
                                <a href="detail.php?bundle_id=<?= $row['id'] ?>" class="btn btn-outline-secondary rounded-pill">
                                    <i class="fa fa-info-circle me-2"></i> Detail Kolaborasi
                                </a>
                                
                                <form action="proses_partner.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan kolaborasi ini? Tindakan ini tidak dapat dibatalkan.');">
                                    <input type="hidden" name="action" value="cancel_bundle">
                                    <input type="hidden" name="bundle_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill mt-2">
                                        <i class="fa fa-times-circle me-2"></i> Batalkan Kolaborasi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if (empty($displayed_partners)): ?>
                <div class="col-12 text-center text-muted py-5">Tidak ada kolaborasi aktif.</div>
            <?php endif; ?>

        <?php else: ?>
            <div class="col-12">
                <div class="empty-state-wrapper bg-white rounded-4 border shadow-sm text-center p-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-partnership-4439199-3687396.png" 
                         alt="Belum Ada Kolaborasi" 
                         class="img-fluid mb-3" style="max-width: 250px;">
                    <h4 class="fw-bold text-dark mb-2">Belum Ada Kolaborasi Aktif</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                        Mulai perjalanan bisnis Anda dengan menemukan mitra yang tepat. 
                        Terima request yang masuk atau jelajahi katalog mitra untuk kolaborasi baru!
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="index.php" class="btn btn-cari px-4 py-2 rounded-pill">
                            <i class="fa fa-search me-2"></i> Cari Mitra Sekarang
                        </a>
                        <a href="request.php" class="btn btn-outline-warning text-dark px-4 py-2 rounded-pill">
                            <i class="fa fa-envelope me-2"></i> Cek Request Masuk
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>