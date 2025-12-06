<?php
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Query yang AMAN (Hanya menampilkan voucher milik user yang login)
$query = "SELECT v.*, b.nama_bundle 
          FROM vouchers v 
          JOIN bundles b ON v.bundle_id = b.id
          WHERE (b.pembuat_id = '$id_user' OR b.mitra_id = '$id_user')
          AND v.kode_voucher LIKE '%$search%'
          ORDER BY v.id DESC";

$result = mysqli_query($koneksi, $query);
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_voucher.css">

<div class="page-wrapper container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="title-section mb-0">Laporan Voucher Saya</h2>
        <a href="export_excel.php" class="btn-pdf text-decoration-none">
            <i class="fa-solid fa-file-excel me-2"></i> Export Excel
        </a>
    </div>

    <!-- Form Cari -->
    <div class="mb-4">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari kode..." value="<?= $search ?>" style="max-width: 300px;">
            <button type="submit" class="btn-search">Cari</button>
        </form>
    </div>

    <div class="card-table">
        <table class="table-voucher">
            <thead>
            <tr>
                <th>Kode Voucher</th>
                <th>Bundle</th>
                <th>Kuota Terpakai</th>
                <th>Status</th>
                <th>Tgl Dibuat</th>
            </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $row['kode_voucher'] ?></td>
                        <td><?= $row['nama_bundle'] ?></td>
                        <td>
                            <span class="badge bg-info text-dark">
                                <?= $row['kuota_terpakai'] ?> / <?= $row['kuota_maksimal'] ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                if($row['status'] == 'available') {
                                    echo '<span class="badge bg-success">Aktif</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">Non-Aktif</span>';
                                }
                            ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5" class="empty-data">Tidak ada data voucher ditemukan</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>