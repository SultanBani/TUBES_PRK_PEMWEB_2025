<?php
$pageTitle = "Set Tanggal Expired Voucher";

include "../layouts/header.php";   // template header
include "../config/koneksi.php";   // koneksi database
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="container">
    <h2>Set Tanggal Expired Voucher</h2>
    <p>Silakan tentukan tanggal expired untuk setiap voucher:</p>

    <table class="table" border="1" cellpadding="6" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Voucher</th>
                <th>Expired Saat Ini</th>
                <th>Set Tanggal Baru</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php
        // Ambil semua voucher
        $sql = "SELECT id, kode_unik, expired_at FROM vouchers ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <form action="proses_voucher.php" method="POST">
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['kode_unik'] ?></td>

                    <td>
                        <?php 
                        echo ($row['expired_at'] == null || $row['expired_at'] == "0000-00-00") 
                             ? "<span style='color:red;'>Belum diset</span>" 
                             : $row['expired_at'];
                        ?>
                    </td>

                    <td>
                        <input type="date" name="expired_at" required>
                    </td>

                    <td>
                        <input type="hidden" name="voucher_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="set_expired" class="btn btn-add">Simpan</button>
                    </td>
                </form>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../layouts/footer.php"; ?>
