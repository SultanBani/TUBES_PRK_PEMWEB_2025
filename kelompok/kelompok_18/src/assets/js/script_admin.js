document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Efek Smooth Scroll untuk tombol "Pelajari Dulu"
    const scrollBtn = document.querySelector('a[href="#fitur"]');
    if (scrollBtn) {
        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    }

    // 2. Simple Scroll Reveal Animation
    // Fungsi untuk mengecek apakah elemen masuk layar
    function revealOnScroll() {
        var reveals = document.querySelectorAll(".reveal");

        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 150;

            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            }
        }
    }

    // Tambahkan class CSS untuk animasi ini secara dinamis (biar gak ngotorin file CSS utama)
    const style = document.createElement('style');
    style.innerHTML = `
        .reveal {
            position: relative;
            transform: translateY(50px);
            opacity: 0;
            transition: 1s all ease;
        }
        .reveal.active {
            transform: translateY(0);
            opacity: 1;
        }
    `;
    document.head.appendChild(style);

    // Jalankan fungsi saat discroll
    window.addEventListener("scroll", revealOnScroll);
    // Jalankan sekali saat load biar yang paling atas langsung muncul
    revealOnScroll();
});