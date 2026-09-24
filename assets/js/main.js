document.addEventListener('DOMContentLoaded', function () {
    var navToggle = document.querySelector('.nav-toggle');
    var navMenu = document.getElementById('primary-navigation');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            var expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!expanded));
            navMenu.classList.toggle('nav-open');
        });
    }

    function scrollHeader() {
        var header = document.querySelector('.site-header');
        if (!header) {
            return;
        }
        if (window.scrollY > 12) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
    scrollHeader();
    window.addEventListener('scroll', scrollHeader);

    var internalLinks = document.querySelectorAll('a[href^="/"]:not([href^="//"])');
    if (internalLinks.length) {
        var speed = 0.00022;
        internalLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                var href = link.getAttribute('href');
                if (!href || href.charAt(0) !== '/' || href.indexOf('#') === 0 || link.hostname !== window.location.hostname) {
                    return;
                }
                event.preventDefault();
                var target = document.querySelector(href);
                if (!target) {
                    window.location.href = href;
                    return;
                }
                var offset = target.getBoundingClientRect().top + window.pageYOffset;
                var start = null;
                var duration = Math.min(Math.max(400, Math.abs(offset - window.pageYOffset) * speed), 800);
                function step(timestamp) {
                    if (!start) start = timestamp;
                    var progress = timestamp - start;
                    var percent = Math.min(progress / duration, 1);
                    var ease = 1 - Math.pow(1 - percent, 2);
                    window.scrollTo(0, window.pageYOffset + (offset - window.pageYOffset) * ease);
                    if (progress < duration) {
                        requestAnimationFrame(step);
                    }
                }
                requestAnimationFrame(step);
            });
        });
    }
});
