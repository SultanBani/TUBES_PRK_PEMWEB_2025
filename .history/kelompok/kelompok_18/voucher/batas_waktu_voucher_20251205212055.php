<?php
$pageTitle = "Set Tanggal Expired Voucher";

include "../layouts/header.php";   // header global
include "../config/koneksi.php";   // koneksi db

// Ambil voucher
$sql = "SELECT id, kode_unik, expired_at FROM vouchers ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="container">

    <?php if (isset($_GET['success'])) { ?>
        <div style="background: #4CAF50; color:white; padding:10px; margin-bottom:15px; border-radius:6px;">
            ✔ Tanggal expired berhasil diperbarui.
        </div>
    <?php } ?>

    <?php if (isset($_GET['error'])) { ?>
        <div style="background: #f44336; color:white; padding:10px; margin-bottom:15px; border-radius:6px;">
            ✖ Terjadi kesalahan saat menyimpan data.
        </div>
    <?php } ?>

    <h2>Set Tanggal Expired Voucher</h2>
    <p>Silakan tentukan tanggal kedaluwarsa voucher di bawah ini:</p>

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
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
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
                        <button type="submit" name="set_expired" class="btn btn-add">
                            Simpan
                        </button>
                    </td>
                </form>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../layouts/footer.php"; ?>
