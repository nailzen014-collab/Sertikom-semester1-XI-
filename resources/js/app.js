

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const adminSectionLinks = document.querySelectorAll('.admin-sidebar-nav a, .admin-mobile-menu nav a');

const activateAdminSection = () => {
    const activeHash = window.location.hash || '#ringkasan';

    adminSectionLinks.forEach((link) => {
        const isActive = link.hash === activeHash;
        link.classList.toggle('is-active', isActive);

        if (isActive) {
            link.setAttribute('aria-current', 'location');
        } else {
            link.removeAttribute('aria-current');
        }
    });
};

adminSectionLinks.forEach((link) => {
    link.addEventListener('click', () => link.closest('details')?.removeAttribute('open'));
});

window.addEventListener('hashchange', activateAdminSection);
activateAdminSection();
