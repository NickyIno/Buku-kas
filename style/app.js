document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("mobile-menu-toggle");
    const sidebar = document.getElementById("app-sidebar");

    if (menuToggle && sidebar) {
        let overlay = document.createElement("div");
        overlay.className = "overlay";
        document.body.appendChild(overlay);

        function toggleSidebar() {
            sidebar.classList.toggle("mobile-open");
            overlay.classList.toggle("show");
        }

        menuToggle.addEventListener("click", toggleSidebar);
        overlay.addEventListener("click", toggleSidebar);
    }
});

if (typeof Swal !== 'undefined') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#FFFFFF',
        color: '#212121',
        iconColor: '#217346'
    });

    function showSuccess(msg) {
        Toast.fire({ icon: 'success', title: msg });
    }

    function showError(msg) {
        Toast.fire({ icon: 'error', title: msg, iconColor: '#C62828' });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    if (typeof Swal === 'undefined') return;

    const key = new URLSearchParams(window.location.search).get('pesan');
    if (!key) return;

    const map = {
        hapus_berhasil:         { icon: 'success', title: 'Data berhasil dihapus.' },
        hapus_gagal:            { icon: 'error',   title: 'Gagal menghapus data.' },
        tanggal_tidak_valid:    { icon: 'error',   title: 'Format tanggal tidak valid.' },
        jumlah_tidak_valid:     { icon: 'error',   title: 'Jumlah tidak boleh negatif.' },
        simpan_gagal:           { icon: 'error',   title: 'Gagal menyimpan data.' },
        tidak_boleh_hapus_self: { icon: 'warning', title: 'Anda tidak dapat menghapus akun sendiri.' }
    };

    const cfg = map[key];
    if (!cfg) return;

    Swal.fire({
        icon: cfg.icon,
        title: cfg.title,
        confirmButtonColor: '#217346'
    });
});

document.querySelectorAll('.nav-item').forEach(link => {
    if (window.location.href.includes(link.getAttribute('href'))) {
        link.classList.add('active');
    }
});
