function checkVoucher() {
    let kode = document.getElementById("kode").value;
    let hasilDiv = document.getElementById("hasil-cek");

    if(kode === "") {
        alert("Harap masukkan kode voucher!");
        return;
    }

    // Tampilkan loading
    hasilDiv.style.display = 'block';
    hasilDiv.innerHTML = '<span class="text-muted"><i class="fa-solid fa-spinner fa-spin"></i> Memeriksa...</span>';

    // Panggil Backend
    fetch("validasi.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "kode=" + kode
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === "valid") {
            hasilDiv.innerHTML = `
                <div class="alert alert-success border-0 shadow-sm">
                    <h4 class="fw-bold"><i class="fa-solid fa-check-circle"></i> VALID!</h4>
                    <p class="mb-0">${data.msg}</p>
                </div>`;
        } else if (data.result === "habis") {
            hasilDiv.innerHTML = `
                <div class="alert alert-warning border-0 shadow-sm">
                    <h5 class="fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> KUOTA HABIS</h5>
                    <p class="mb-0">${data.msg}</p>
                </div>`;
        } else if (data.result === "expired") {
            hasilDiv.innerHTML = `
                <div class="alert alert-danger border-0 shadow-sm">
                    <h5 class="fw-bold"><i class="fa-solid fa-clock"></i> KADALUARSA</h5>
                    <p class="mb-0">${data.msg}</p>
                </div>`;
        } else {
            hasilDiv.innerHTML = `
                <div class="alert alert-secondary border-0 shadow-sm">
                    <h5 class="fw-bold"><i class="fa-solid fa-circle-xmark"></i> TIDAK DITEMUKAN</h5>
                    <p class="mb-0">${data.msg}</p>
                </div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hasilDiv.innerHTML = '<p class="text-danger">Terjadi kesalahan sistem.</p>';
    });
}