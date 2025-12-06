<?php
$pageTitle = "Atur Batas Waktu Expired Voucher";
include "../layouts/header.php";
include "../config/koneksi.php";
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="container">
    <h2>Set Expired Voucher</h2>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Voucher</th>
                <th>Expired Sekarang</th>
                <th>Set Expired Baru</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil voucher
            $sql = "SELECT id, kode_unik, expired_at FROM vouchers ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <form action="proses_voucher.php" method="POST">
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['kode_unik'] ?></td>
                        <td><?= $row['expired_at'] ? $row['expired_at'] : "<span style='color:red;'>Belum Diset</span>" ?></td>
                        <td>
                            <input type="date" name="expired_at" required>
                        </td>
                        <td>
                            <input type="hidden" name="voucher_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="set_expired" class="btn btn-update">Simpan</button>
                        </td>
                    </form>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../layouts/footer.php"; ?>
