// Skeleton loading and lazy images - Critical for UX
(function () {
    "use strict";

    const HT = window.HT || {};

    HT.skeleton = () => {
        // Lazy load images with Intersection Observer
        const lazyImages = document.querySelectorAll('.lazy-image');

        if (lazyImages.length === 0) return;

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;

                    if (!src) {
                        observer.unobserve(img);
                        return;
                    }

                    const newImg = new Image();
                    newImg.onload = function () {
                        img.src = src;
                        img.classList.add('loaded');

                        const parent = img.closest('.image');
                        if (parent) {
                            const skeleton = parent.querySelector('.skeleton-loading');
                            if (skeleton) {
                                skeleton.style.display = 'none';
                            }
                        }

                        observer.unobserve(img);
                    };

                    newImg.src = src;
                }
            });
        }, {
            rootMargin: '0px 0px 50px 0px',
            threshold: 0.1
        });

        lazyImages.forEach(img => observer.observe(img));
    };

    // Initialize immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', HT.skeleton);
    } else {
        HT.skeleton();
    }

    window.HT = HT;

})();
