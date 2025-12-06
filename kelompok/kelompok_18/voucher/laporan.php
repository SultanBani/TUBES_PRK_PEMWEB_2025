<?php
$pageTitle = "Laporan Voucher";
include "../layouts/header.php";
include "../config/koneksi.php";

// Jika koneksi error
if (!$conn) {
    die("<p style='color:red;'>Koneksi database gagal!</p>");
}

$search = isset($_GET['search']) ? $_GET['search'] : "";

// Query voucher
$query = "SELECT v.*, b.nama_bundle FROM vouchers v 
          JOIN bundles b ON v.bundle_id = b.id
          WHERE v.kode_unik LIKE '%$search%'
          ORDER BY v.id DESC";

$result = mysqli_query($conn, $query);
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="page-wrapper">
    <h2>Laporan Voucher</h2>

    <div class="top-bar">
        <form method="GET">
            <input type="text" name="search" class="input-search" placeholder="Cari kode voucher..." value="<?= $search ?>">
            <button type="submit" class="btn-search">Cari</button>
        </form>

        <div class="export-buttons">
            <a href="export_excel.php" class="btn-export">Export Excel</a>
            <a href="export_pdf.php" class="btn-pdf">Export PDF</a>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kode Voucher</th>
            <th>Bundle</th>
            <th>Status</th>
            <th>Tanggal Dibuat</th>
        </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['kode_unik'] ?></td>
                    <td><?= $row['nama_bundle'] ?></td>

                    <td>
                        <?php if ($row['status'] == "used") { ?>
                            <span class="status-badge status-used">Used</span>
                        <?php } else { ?>
                            <span class="status-badge status-available">Available</span>
                        <?php } ?>
                    </td>

                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5" class="table-empty">Tidak ada data voucher ditemukan</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../layouts/footer.php"; ?>