function hapusVoucher(id) {
    if (confirm("Yakin hapus voucher?")) {
        window.location = "proses_voucher.php?hapus=" + id;
    }
}

function cekVoucher() {
    const kode = prompt("Masukkan Kode Voucher:");

    fetch('validasi.php', {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "kode=" + kode
    })
    .then(res => res.json())
    .then(res => {
        if (res.result === "valid") alert("Voucher VALID");
        else if (res.result === "used") alert("Voucher SUDAH TERPAKAI");
        else alert("Voucher TIDAK DITEMUKAN");
    });
}, 