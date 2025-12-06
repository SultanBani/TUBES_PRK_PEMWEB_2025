function checkVoucher() {
    let kode = document.getElementById("kode").value;

    fetch("validasi.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "kode=" + kode
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === "valid") {
            alert("Voucher Valid & Bisa Digunakan");
        } else if (data.result === "used") {
            alert("Voucher sudah digunakan sebelumnya!");
        } else {
            alert("Kode Voucher tidak ditemukan!");
        }
    });
}
