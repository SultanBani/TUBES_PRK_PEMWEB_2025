// Fungsi Preview Gambar
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validasi Tipe
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file harus JPG atau PNG!');
            input.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        // Validasi Ukuran (2MB)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            input.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        // Tampilkan Preview
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

// Auto Hide Alert
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);
});