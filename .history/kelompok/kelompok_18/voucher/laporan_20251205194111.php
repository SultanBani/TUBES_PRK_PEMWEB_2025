<?php
// Pastikan include_once agar tidak double load
include_once "../config/koneksi.php";      // koneksi database
include_once "../layouts/header.php";      // header global

if (!isset($conn)) {
    die("Koneksi database gagal. Pastikan koneksi.php menggunakan variabel \$conn");
}
?>
<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="container">
    <h2>Laporan Penggunaan Voucher</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Kode Voucher</th>
                <th>Status</th>
                <th>Tanggal Digunakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM vouchers WHERE status='used' ORDER BY created_at DESC";
            $q = $conn->query($sql);

            if ($q && $q->num_rows > 0) {
                while ($row = $q->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['kode_unik']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['created_at']}</td>
                         </tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center;'>Belum ada voucher yang digunakan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include_once "../layouts/footer.php"; ?>
