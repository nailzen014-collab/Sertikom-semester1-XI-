

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

// Custom SweetAlert2 Toast & Confirm Helpers
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
window.Toast = Toast;

// Flash messages from server session
document.addEventListener('DOMContentLoaded', () => {
    const flashMessage = document.querySelector('[data-flash-message]')?.dataset.flashMessage;
    const flashError = document.querySelector('[data-flash-error]')?.dataset.flashError;

    if (flashMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: flashMessage,
            confirmButtonColor: '#1c4335',
        });
    }

    if (flashError) {
        Swal.fire({
            icon: 'error',
            title: 'Perhatian!',
            text: flashError,
            confirmButtonColor: '#1c4335',
        });
    }

    // Global form confirmation with class 'swal-confirm'
    document.querySelectorAll('form.swal-confirm').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const message = form.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const confirmText = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
            const isDanger = form.getAttribute('data-confirm-danger') === 'true';

            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#dc2626' : '#1c4335',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

// Admin in-page hash links (jika ada link dengan hash #)
const adminHashLinks = document.querySelectorAll('.admin-sidebar-nav a[href*="#"], .admin-mobile-menu nav a[href*="#"]');
if (adminHashLinks.length > 0 && window.location.hash) {
    const activateAdminSection = () => {
        const activeHash = window.location.hash;
        if (!activeHash) return;

        adminHashLinks.forEach((link) => {
            if (link.hash) {
                const isActive = link.hash === activeHash;
                link.classList.toggle('is-active', isActive);
                if (isActive) {
                    link.setAttribute('aria-current', 'location');
                }
            }
        });
    };
    window.addEventListener('hashchange', activateAdminSection);
    activateAdminSection();
}

document.querySelectorAll('.admin-mobile-menu nav a').forEach((link) => {
    link.addEventListener('click', () => link.closest('details')?.removeAttribute('open'));
});

// Sticky header scroll elevation
const header = document.querySelector('.public-header');
if (header) {
    const handleScroll = () => {
        if (window.scrollY > 20) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
}

// Scroll reveal using IntersectionObserver
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                obs.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
    });

    document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
} else {
    document.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-revealed'));
}
