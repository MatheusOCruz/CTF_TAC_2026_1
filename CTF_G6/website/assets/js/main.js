document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.nav-toggle');
    const links = document.querySelector('#primary-links');

    if (toggle && links) {
        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            links.classList.toggle('open');
        });
    }

    document.querySelectorAll('.card, .module-card, .stat-card').forEach((element, index) => {
        element.style.setProperty('--delay', `${Math.min(index * 45, 300)}ms`);
        element.classList.add('reveal');
    });
});
