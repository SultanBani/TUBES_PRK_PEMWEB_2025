<?php
include "../config/koneksi.php";

$kode = $_POST['kode'];

$q = $conn->query("SELECT * FROM vouchers WHERE kode_unik='$kode'");

if ($q->num_rows > 0) {
    $data = $q->fetch_assoc();

    if ($data['status'] === "used") {
        echo json_encode(["result" => "used"]);
    } else {
        echo json_encode(["result" => "valid"]);
    }
} else {
    echo json_encode(["result" => "notfound"]);
}
?>