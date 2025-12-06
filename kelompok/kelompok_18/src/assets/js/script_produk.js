function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan JPG atau PNG.');
            input.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            input.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const hargaInput = document.getElementById('harga');
    
    if (hargaInput) {
        hargaInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }

        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-produk');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const namaProduk = document.getElementById('nama_produk').value.trim();
            const kategori = document.getElementById('kategori').value;
            const satuan = document.getElementById('satuan').value.trim();
            const harga = document.getElementById('harga').value;
            
            if (!namaProduk) {
                e.preventDefault();
                alert('Nama produk harus diisi!');
                return false;
            }
            
            if (!kategori) {
                e.preventDefault();
                alert('Kategori harus dipilih!');
                return false;
            }
            
            if (!satuan) {
                e.preventDefault();
                alert('Satuan harus diisi!');
                return false;
            }
            
            if (!harga || harga <= 0) {
                e.preventDefault();
                alert('Harga harus diisi dengan nilai positif!');
                return false;
            }
            
            return true;
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});