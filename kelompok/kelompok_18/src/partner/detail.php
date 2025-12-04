<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// 1. Cek Sesi
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>window.location='../auth/login.php';</script>";
    exit;
}

// 2. Ambil Bundle ID
$bundle_id = $_GET['bundle_id'] ?? null;
if (!$bundle_id) {
    echo "<script>alert('ID Bundle tidak ditemukan.'); window.location='my_bundles.php';</script>";
    exit;
}

// 3. Query Data Bundle & Partner
// Kita gunakan logika yang sama untuk mendeteksi siapa partnernya
$query = "SELECT b.*, 
          u.nama_toko as mitra_nama, 
          u.foto_profil as mitra_foto, 
          u.no_hp as mitra_hp,
          u.alamat_toko as mitra_alamat,
          p1.nama_produk as produk_saya,
          p2.nama_produk as produk_mitra,
          v.kode_voucher, v.kuota_maksimal, v.kuota_terpakai, v.potongan_harga, v.expired_at, v.status as status_voucher
          FROM bundles b
          JOIN users u ON (u.id = CASE WHEN b.pembuat_id = '$my_id' THEN b.mitra_id ELSE b.pembuat_id END)
          LEFT JOIN products p1 ON p1.id = b.produk_pembuat_id
          LEFT JOIN products p2 ON p2.id = b.produk_mitra_id
          LEFT JOIN vouchers v ON v.bundle_id = b.id
          WHERE b.id = '$bundle_id' AND (b.pembuat_id = '$my_id' OR b.mitra_id = '$my_id')";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan atau Anda tidak memiliki akses.'); window.location='my_bundles.php';</script>";
    exit;
}

// Tentukan apakah user ini adalah Pengirim Request (Pembuat) atau Penerima (Mitra)
$is_creator = ($data['pembuat_id'] == $my_id);
?>

<link rel="stylesheet" href="../assets/css/style_partner.css?v=<?= time(); ?>">
<style>
    /* Style Khusus Halaman Detail */
    .detail-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    }
    .detail-header {
        background: linear-gradient(135deg, var(--bg-light), #fff);
        padding: 25px;
        border-bottom: 1px solid #eee;
    }
    .status-badge-lg {
        font-size: 0.9rem;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .status-pending { background: #FFF3E0; color: #EF6C00; }
    .status-active { background: #E8F5E9; color: #2E7D32; }
    .status-rejected { background: #FFEBEE; color: #C62828; }
    .status-cancelled { background: #F5F5F5; color: #757575; }
    
    .voucher-box {
        background: #FDFBF7; /* Cream lembut */
        border: 2px dashed var(--primary);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        position: relative;
    }
    .voucher-code {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 2px;
        display: block;
        margin: 10px 0;
    }
    .progress-custom {
        height: 10px;
        border-radius: 10px;
        background-color: #eee;
    }
</style>

<div class="container py-5">
    
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="javascript:history.back()" class="btn btn-light rounded-circle border shadow-sm" style="width: 40px; height: 40px;">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0 text-dark">Detail Kolaborasi</h4>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="detail-card mb-4">
                <div class="detail-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <small class="text-muted d-block mb-1">ID Bundle: #<?= $data['id'] ?></small>
                        <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($data['nama_bundle']) ?></h3>
                    </div>
                    
                    <?php if($data['status'] == 'pending'): ?>
                        <span class="status-badge-lg status-pending"><i class="fa fa-clock"></i> Menunggu Konfirmasi</span>
                    <?php elseif($data['status'] == 'active'): ?>
                        <span class="status-badge-lg status-active"><i class="fa fa-check-circle"></i> Kolaborasi Aktif</span>
                    <?php elseif($data['status'] == 'rejected'): ?>
                        <span class="status-badge-lg status-rejected"><i class="fa fa-times-circle"></i> Ditolak</span>
                    <?php else: ?>
                        <span class="status-badge-lg status-cancelled"><i class="fa fa-ban"></i> Dibatalkan</span>
                    <?php endif; ?>
                </div>

                <div class="p-4">
                    <?php if($data['status'] == 'active' && !empty($data['kode_voucher'])): 
                        $persen = ($data['kuota_terpakai'] / $data['kuota_maksimal']) * 100;
                    ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold small text-secondary">Penggunaan Voucher</span>
                                <span class="fw-bold small text-primary"><?= $data['kuota_terpakai'] ?> / <?= $data['kuota_maksimal'] ?></span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $persen ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">Produk Bundling</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded text-center h-100 border">
                                <small class="text-muted d-block">Produk Saya</small>
                                <strong class="text-dark"><?= htmlspecialchars($data['produk_saya'] ?? 'Belum dipilih') ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded text-center h-100 border">
                                <small class="text-muted d-block">Produk Partner</small>
                                <strong class="text-dark"><?= htmlspecialchars($data['produk_mitra'] ?? 'Belum dipilih') ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 small">
                        <i class="fa fa-info-circle me-1"></i> 
                        Saat ini fitur pemilihan produk spesifik sedang dalam pengembangan. Anda bisa mendiskusikan produk mana yang akan dibundling melalui chat.
                    </div>
                </div>
            </div>

            <?php if ($data['status'] == 'active'): ?>
                <div class="detail-card p-4">
                    <h5 class="fw-bold mb-3"><i class="fa fa-ticket-alt me-2 text-primary"></i> Voucher Promo</h5>
                    
                    <?php if (empty($data['kode_voucher'])): ?>
                        <div class="text-center py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/1162/1162951.png" width="80" class="mb-3 opacity-50">
                            <h5>Belum ada Voucher</h5>
                            <p class="text-muted">Buat kode voucher agar pelanggan bisa mengklaim promo bundling ini.</p>
                            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalVoucher">
                                <i class="fa fa-plus me-1"></i> Buat Voucher Sekarang
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="voucher-box">
                            <small class="text-uppercase text-muted fw-bold ls-1">Kode Promo</small>
                            <span class="voucher-code"><?= htmlspecialchars($data['kode_voucher']) ?></span>
                            <div class="d-flex justify-content-center gap-3 mt-2 text-muted small">
                                <span><i class="fa fa-tag me-1"></i> Diskon: Rp <?= number_format($data['potongan_harga']) ?></span>
                                <span><i class="fa fa-clock me-1"></i> Exp: <?= date('d M Y', strtotime($data['expired_at'])) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="detail-card p-4 mb-4 text-center">
                <img src="<?= !empty($data['mitra_foto']) && file_exists('../assets/uploads/'.$data['mitra_foto']) 
                            ? '../assets/uploads/'.$data['mitra_foto'] 
                            : 'https://ui-avatars.com/api/?name='.urlencode($data['mitra_nama']).'&background=random' ?>" 
                     class="rounded-circle border mb-3" width="80" height="80" style="object-fit: cover;">
                
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($data['mitra_nama']) ?></h5>
                <p class="text-muted small mb-3"><?= htmlspecialchars($data['mitra_alamat'] ?? 'Alamat tidak tersedia') ?></p>

                <div class="d-grid gap-2">
                    <a href="chat_room.php?bundle_id=<?= $data['id'] ?>" class="btn btn-primary rounded-pill">
                        <i class="fa fa-comments me-2"></i> Chat Partner
                    </a>
                </div>
            </div>

            <div class="detail-card p-4">
                <h6 class="fw-bold mb-3">Tindakan</h6>

                <?php if ($data['status'] == 'pending'): ?>
                    <?php if (!$is_creator): ?> 
                        <div class="d-grid gap-2">
                            <form action="proses_partner.php" method="POST">
                                <input type="hidden" name="action" value="accept">
                                <input type="hidden" name="bundle_id" value="<?= $data['id'] ?>">
                                <button type="submit" class="btn btn-success w-100 rounded-pill mb-2">
                                    <i class="fa fa-check me-1"></i> Terima Tawaran
                                </button>
                            </form>
                            <form action="proses_partner.php" method="POST">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="bundle_id" value="<?= $data['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                                    <i class="fa fa-times me-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning small">
                            <i class="fa fa-hourglass-half me-1"></i> Menunggu konfirmasi dari mitra.
                        </div>
                        <form action="proses_partner.php" method="POST" onsubmit="return confirm('Batalkan permintaan ini?');">
                            <input type="hidden" name="action" value="cancel_bundle">
                            <input type="hidden" name="bundle_id" value="<?= $data['id'] ?>">
                            <button type="submit" class="btn btn-outline-secondary w-100 rounded-pill">
                                <i class="fa fa-trash me-1"></i> Batalkan Request
                            </button>
                        </form>
                    <?php endif; ?>

                <?php elseif ($data['status'] == 'active'): ?>
                    <form action="proses_partner.php" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri kolaborasi ini?');">
                        <input type="hidden" name="action" value="cancel_bundle"> <input type="hidden" name="bundle_id" value="<?= $data['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                            <i class="fa fa-ban me-1"></i> Batalkan / Akhiri
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center text-muted small">
                        Kolaborasi ini telah selesai atau dibatalkan. Tidak ada tindakan tersedia.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVoucher" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Buat Voucher Promo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="create_voucher">
                <input type="hidden" name="bundle_id" value="<?= $data['id'] ?>">
                
                <div class="modal-body bg-light">
                    <div class="mb-3">
                        <label class="fw-bold small">Kode Voucher (Unik)</label>
                        <input type="text" name="kode_voucher" class="form-control text-uppercase" placeholder="CONTOH: PROMO123" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold small">Potongan (Rp)</label>
                            <input type="number" name="potongan_harga" class="form-control" placeholder="10000" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold small">Kuota Maksimal</label>
                            <input type="number" name="kuota_maksimal" class="form-control" value="50" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold small">Berlaku Sampai</label>
                        <input type="date" name="expired_at" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill">Simpan Voucher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>