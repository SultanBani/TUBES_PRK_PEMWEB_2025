<?php
include "../config/koneksi.php";      // koneksi database
include "../layouts/header.php";      // header global
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
        $q = $conn->query("SELECT * FROM vouchers WHERE status='used'");
        while ($row = $q->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['kode_unik']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['created_at']}</td>
                 </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<?php include "../layouts/footer.php"; ?>