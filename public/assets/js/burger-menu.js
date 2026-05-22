document.addEventListener('DOMContentLoaded', function () {
    var burgerBtn = document.getElementById('burgerBtn');
    var mobileNav = document.getElementById('mobileNav');
    var mobileOverlay = document.getElementById('mobileOverlay');
    var mobileClose = document.getElementById('mobileClose');

    if (!burgerBtn || !mobileNav || !mobileOverlay) return;

    function openMenu() {
        burgerBtn.classList.add('active');
        mobileNav.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        burgerBtn.classList.remove('active');
        mobileNav.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    burgerBtn.addEventListener('click', function () {
        if (mobileNav.classList.contains('active')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    mobileClose.addEventListener('click', closeMenu);
    mobileOverlay.addEventListener('click', closeMenu);

    var mobileLinks = mobileNav.querySelectorAll('.header__mobile-link, .header__mobile-login');
    mobileLinks.forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
            closeMenu();
        }
    });
});
