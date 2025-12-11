<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// 1. Cek Sesi
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>window.location='../auth/login.php';</script>";
    exit;
}

// 2. LOGIKA MENENTUKAN PARTNER ID
$partner_id = null;

if (isset($_GET['partner_id'])) {
    $partner_id = $_GET['partner_id'];
} elseif (isset($_GET['bundle_id'])) {
    // Fallback: Jika link dari tempat lain masih pakai bundle_id
    $bid = mysqli_real_escape_string($koneksi, $_GET['bundle_id']);
    $q_temp = mysqli_query($koneksi, "SELECT pembuat_id, mitra_id FROM bundles WHERE id='$bid'");
    $d_temp = mysqli_fetch_assoc($q_temp);
    if ($d_temp) {
        $partner_id = ($d_temp['pembuat_id'] == $my_id) ? $d_temp['mitra_id'] : $d_temp['pembuat_id'];
    }
}

if (!$partner_id) {
    echo "<script>alert('Partner tidak ditemukan.'); window.location='my_bundles.php';</script>";
    exit;
}

// 3. AMBIL DATA PARTNER (Sidebar)
$q_partner = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$partner_id'");
$partner   = mysqli_fetch_assoc($q_partner);

// 4. AMBIL SEMUA DEAL/BUNDLE DENGAN PARTNER INI
$query_deals = "SELECT b.*, 
                p1.nama_produk as produk_saya,
                p2.nama_produk as produk_mitra,
                v.kode_voucher, v.kuota_maksimal, v.kuota_terpakai, v.potongan_harga, v.expired_at
                FROM bundles b
                LEFT JOIN products p1 ON b.produk_pembuat_id = p1.id
                LEFT JOIN products p2 ON b.produk_mitra_id = p2.id
                LEFT JOIN vouchers v ON v.bundle_id = b.id
                WHERE ((b.pembuat_id = '$my_id' AND b.mitra_id = '$partner_id') 
                    OR (b.pembuat_id = '$partner_id' AND b.mitra_id = '$my_id'))
                AND b.status = 'active'
                ORDER BY b.created_at DESC";

$res_deals = mysqli_query($koneksi, $query_deals);

// Simpan hasil query ke Array agar bisa dipakai berkali-kali
$all_deals = [];
while ($row = mysqli_fetch_assoc($res_deals)) {
    $all_deals[] = $row;
}

// 5. LOGIKA MEMILIH BUNDLE MANA YANG DITAMPILKAN
$selected_bundle = null;
$selected_id = $_GET['selected_id'] ?? null;

if (!empty($all_deals)) {
    if ($selected_id) {
        // Jika user memilih dari dropdown, cari datanya di array
        foreach ($all_deals as $deal) {
            if ($deal['id'] == $selected_id) {
                $selected_bundle = $deal;
                break;
            }
        }
    } 
    
    // Jika tidak ada yang dipilih (atau ID salah), tampilkan yang paling baru (pertama di array)
    if ($selected_bundle === null) {
        $selected_bundle = $all_deals[0];
        $selected_id = $selected_bundle['id'];
    }
}
?>

<link rel="stylesheet" href="../assets/css/style_partner.css?v=<?= time(); ?>">
<style>
    /* UI Style Existing */
    .detail-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    }
    .detail-header {
        background: #fff;
        padding: 25px;
        border-bottom: 1px solid #eee;
    }
    .status-badge-active {
        background: #E8F5E9; color: #2E7D32;
        padding: 5px 15px; border-radius: 50px; font-weight: bold; font-size: 0.85rem;
    }
    
    /* Style untuk Dropdown Selector */
    .bundle-selector {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .form-select-lg {
        font-weight: 600;
        color: #4F4A45;
        border: 2px solid #eee;
    }
    .form-select-lg:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(237, 125, 49, 0.1);
    }
    
    /* Voucher Box */
    .voucher-box {
        background: #FDFBF7;
        border: 2px dashed var(--primary);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        margin-top: 20px;
    }
</style>

<div class="container py-5">
    
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="my_bundles.php" class="btn btn-light rounded-circle border shadow-sm" style="width: 40px; height: 40px;">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0 text-dark">Detail Kolaborasi</h4>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            
            <?php if (!empty($all_deals)): ?>
                
                <div class="bundle-selector d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <label class="small text-muted fw-bold mb-1">Pilih Paket Bundling:</label>
                        <form action="" method="GET">
                            <input type="hidden" name="partner_id" value="<?= $partner_id ?>">
                            <select name="selected_id" class="form-select form-select-lg" onchange="this.form.submit()">
                                <?php foreach ($all_deals as $deal): ?>
                                    <option value="<?= $deal['id'] ?>" <?= ($deal['id'] == $selected_id) ? 'selected' : '' ?>>
                                        #<?= $deal['id'] ?> - <?= htmlspecialchars($deal['nama_bundle']) ?> 
                                        (Rp <?= number_format($deal['harga_bundle']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary rounded-pill"><?= count($all_deals) ?> Paket</span>
                    </div>
                </div>

                <div class="detail-card mb-4">
                    <div class="detail-header d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-muted d-block mb-1">ID Bundle: #<?= $selected_bundle['id'] ?></small>
                            <h2 class="fw-bold text-dark mb-0"><?= htmlspecialchars($selected_bundle['nama_bundle']) ?></h2>
                        </div>
                        <span class="status-badge-active"><i class="fa fa-check-circle me-1"></i> Kolaborasi Aktif</span>
                    </div>

                    <div class="p-4">
                        <h6 class="fw-bold text-secondary mb-3">Produk Bundling</h6>
                        
                        <div class="row g-0 border rounded overflow-hidden mb-3">
                            <div class="col-md-6 bg-light border-end p-4 text-center">
                                <small class="text-muted d-block mb-1">Produk Saya</small>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($selected_bundle['produk_saya'] ?? '-') ?></h5>
                            </div>
                            <div class="col-md-6 bg-light p-4 text-center">
                                <small class="text-muted d-block mb-1">Produk Partner</small>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($selected_bundle['produk_mitra'] ?? '-') ?></h5>
                            </div>
                        </div>

                        <?php if(empty($selected_bundle['produk_saya']) || empty($selected_bundle['produk_mitra'])): ?>
                            <div class="alert alert-info small">
                                <i class="fa fa-info-circle me-1"></i> 
                                Saat ini fitur pemilihan produk spesifik sedang dalam pengembangan. Anda bisa mendiskusikan produk mana yang akan dibundling melalui chat.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-card p-4">
                    <h5 class="fw-bold mb-3 text-primary"><i class="fa fa-ticket-alt me-2"></i> Voucher Promo</h5>
                    
                    <?php if (empty($selected_bundle['kode_voucher'])): ?>
                        <div class="text-center py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/1162/1162951.png" width="80" class="mb-3 opacity-50">
                            <h5>Belum ada Voucher</h5>
                            <p class="text-muted">Buat kode voucher khusus untuk paket <strong><?= htmlspecialchars($selected_bundle['nama_bundle']) ?></strong> ini.</p>
                            
                            <button class="btn btn-primary rounded-pill px-4 shadow-sm" 
                                    data-bs-toggle="modal" data-bs-target="#modalVoucher"
                                    onclick="setVoucherBundle(<?= $selected_bundle['id'] ?>)">
                                <i class="fa fa-plus me-1"></i> Buat Voucher Sekarang
                            </button>
                        </div>

                    <?php else: ?>
                        <div class="voucher-box">
                            <small class="text-uppercase text-muted fw-bold ls-1">KODE PROMO</small>
                            <span class="d-block display-5 fw-bold text-primary my-2" style="letter-spacing: 3px;">
                                <?= htmlspecialchars($selected_bundle['kode_voucher']) ?>
                            </span>
                            <div class="d-flex justify-content-center gap-4 text-muted mt-3">
                                <span><i class="fa fa-tag me-1"></i> Potongan: <b>Rp <?= number_format($selected_bundle['potongan_harga']) ?></b></span>
                                <span><i class="fa fa-users me-1"></i> Terpakai: <b><?= $selected_bundle['kuota_terpakai'] ?>/<?= $selected_bundle['kuota_maksimal'] ?></b></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-4 border">
                    <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada paket deal dengan mitra ini.</h5>
                    <a href="chat_room.php?partner_id=<?= $partner_id ?>" class="btn btn-primary rounded-pill mt-2">Buka Chat & Buat Deal</a>
                </div>
            <?php endif; ?>

        </div>

        <div class="col-lg-4">
            <div class="detail-card p-4 mb-4 text-center sticky-top" style="top: 20px;">
                <img src="<?= !empty($partner['foto_profil']) && file_exists('../assets/uploads/'.$partner['foto_profil']) 
                            ? '../assets/uploads/'.$partner['foto_profil'] 
                            : 'https://ui-avatars.com/api/?name='.urlencode($partner['nama_toko']).'&background=random' ?>" 
                     class="rounded-circle border mb-3" width="90" height="90" style="object-fit: cover;">
                
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($partner['nama_toko']) ?></h5>
                <span class="badge bg-light text-dark border mb-3"><?= htmlspecialchars($partner['kategori_bisnis']) ?></span>
                
                <p class="text-muted small mb-3 text-start">
                    <i class="fa fa-map-marker-alt text-danger me-2"></i> <?= htmlspecialchars($partner['alamat_toko'] ?? '-') ?>
                </p>

                <div class="d-grid gap-2 mb-4">
                    <a href="chat_room.php?partner_id=<?= $partner['id'] ?>" class="btn btn-primary rounded-pill">
                        <i class="fa fa-comments me-2"></i> Chat Partner
                    </a>
                </div>

                <div class="border-top pt-3 text-start">
                    <h6 class="fw-bold mb-3">Tindakan</h6>
                    <form action="proses_partner.php" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri paket <?= htmlspecialchars($selected_bundle['nama_bundle'] ?? '') ?>?');">
                        <input type="hidden" name="action" value="cancel_bundle">
                        <input type="hidden" name="bundle_id" value="<?= $selected_id ?>">
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                            <i class="fa fa-ban me-2"></i> Batalkan / Akhiri
                        </button>
                    </form>
                </div>
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
                <input type="hidden" name="bundle_id" id="inputBundleId" value="">
                
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

<script>
    // Fungsi untuk mengirim ID Bundle yang sedang dipilih ke dalam Modal
    function setVoucherBundle(id) {
        document.getElementById('inputBundleId').value = id;
    }
</script>

<?php include '../layouts/footer.php'; ?>