<?php
include "../config/koneksi.php";      // koneksi database
include "../layouts/header.php";      // header global
if (!isset($conn)) {
    die("Koneksi database gagal. Pastikan koneksi.php menggunakan variabel \$conn");
}

// Search filter
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Query join vouchers & bundles
$sql = "SELECT vouchers.*, bundles.nama_bundle
        FROM vouchers
        JOIN bundles ON vouchers.bundle_id = bundles.id
        WHERE vouchers.status = 'used'
        AND vouchers.kode_unik LIKE '%$search%'
        ORDER BY vouchers.created_at DESC";

$q = $conn->query($sql);
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="container">
    <h2>Laporan Penggunaan Voucher</h2>

    <!-- Filter & Export -->
    <div class="top-bar">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Cari kode voucher..." value="<?php echo $search; ?>" class="input-search">
            <button type="submit" class="btn-search">Cari</button>
        </form>

        <a href="export_excel.php" class="btn-export">Export Excel</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Kode Voucher</th>
                <th>Bundle</th>
                <th>Status</th>
                <th>Tanggal Digunakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($q && $q->num_rows > 0) {
                while ($row = $q->fetch_assoc()) {

                    // Format tanggal Indonesia
                    $tanggal = date('d M Y H:i', strtotime($row['created_at']));

                    echo "<tr>
                            <td>{$row['kode_unik']}</td>
                            <td>{$row['nama_bundle']}</td>
                            <td>{$row['status']}</td>
                            <td>{$tanggal}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>Belum ada voucher yang digunakan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include_once "../layouts/footer.php"; ?>
