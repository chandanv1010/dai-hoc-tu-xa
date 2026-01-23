// Swiper initializations - Lazy loaded
(function ($) {
    "use strict";

    const HT = window.HT || {};

    // Check if Swiper is available
    if (typeof Swiper === 'undefined') {
        console.warn('Swiper library not loaded');
        return;
    }

    // Helper to check slide count and enable loop
    const shouldEnableLoop = (container, minSlides = 2) => {
        if (!container) return false;
        const slides = container.querySelectorAll('.swiper-slide');
        return slides.length >= minSlides;
    };

    // Main slide swiper
    HT.swiper = () => {
        const container = document.querySelector(".panel-slide .swiper-container");
        if (!container) return;

        new Swiper(".panel-slide .swiper-container", {
            loop: shouldEnableLoop(container, 2),
            pagination: { el: '.swiper-pagination' },
            autoplay: shouldEnableLoop(container, 2) ? { delay: 3000 } : false,
            spaceBetween: 15,
            slidesPerView: 1,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    };

    // Category swiper
    HT.category = () => {
        const container = document.querySelector(".panel-category .swiper-container");
        if (!container) return;

        new Swiper(".panel-category .swiper-container", {
            loop: shouldEnableLoop(container, 6),
            autoplay: shouldEnableLoop(container, 6) ? { delay: 2000 } : false,
            spaceBetween: 15,
            slidesPerView: 1.5,
            breakpoints: {
                500: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                1280: { slidesPerView: 3 }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    };

    // Feedback swiper
    HT.feedbackSwiper = () => {
        const container = document.querySelector(".panel-student-feedback .feedback-swiper");
        if (!container) return;

        new Swiper(".panel-student-feedback .feedback-swiper", {
            loop: shouldEnableLoop(container, 2),
            pagination: { el: '.swiper-pagination', clickable: true },
            autoplay: shouldEnableLoop(container, 2) ? { delay: 3000, disableOnInteraction: false } : false,
            spaceBetween: 30,
            slidesPerView: 1,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                960: { slidesPerView: 3, spaceBetween: 30 }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    };

    // Schools swiper
    HT.schoolsSwiper = () => {
        const container = document.querySelector(".panel-major-choose-school .schools-swiper");
        if (!container) return;

        new Swiper(".panel-major-choose-school .schools-swiper", {
            loop: shouldEnableLoop(container, 4),
            spaceBetween: 30,
            slidesPerView: 2,
            autoplay: shouldEnableLoop(container, 4) ? { delay: 5000, disableOnInteraction: false } : false,
            navigation: {
                nextEl: '.panel-major-choose-school .swiper-button-next',
                prevEl: '.panel-major-choose-school .swiper-button-prev',
            },
            breakpoints: {
                0: { slidesPerView: 1, spaceBetween: 15 },
                768: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 2, spaceBetween: 30 }
            }
        });
    };

    // Schools list swiper
    HT.schoolsListSwiper = () => {
        const container = document.querySelector(".schools-list-swiper");
        if (!container) return;

        new Swiper(".schools-list-swiper", {
            loop: shouldEnableLoop(container, 3),
            navigation: {
                nextEl: '.schools-list-swiper .swiper-button-next',
                prevEl: '.schools-list-swiper .swiper-button-prev',
            },
            spaceBetween: 30,
            slidesPerView: 1,
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 30 },
                1024: { slidesPerView: 3, spaceBetween: 30 }
            }
        });
    };

    // Majors swiper
    HT.majorsSwiper = () => {
        const container = document.querySelector(".panel-school-majors .majors-swiper-container");
        if (!container) return;

        new Swiper(".panel-school-majors .majors-swiper-container", {
            loop: shouldEnableLoop(container, 3),
            pagination: { el: '.panel-school-majors .swiper-pagination', clickable: true },
            navigation: {
                nextEl: '.panel-school-majors .swiper-button-next',
                prevEl: '.panel-school-majors .swiper-button-prev',
            },
            autoplay: shouldEnableLoop(container, 3) ? { delay: 3000, disableOnInteraction: false } : false,
            spaceBetween: 30,
            slidesPerView: 1,
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 30 }
            }
        });
    };

    // Event swiper
    HT.eventSwiper = () => {
        const container = document.querySelector(".panel-news-outstanding .event-swiper");
        if (!container) return;

        new Swiper(".panel-news-outstanding .event-swiper", {
            loop: shouldEnableLoop(container, 3),
            pagination: { el: '.event-swiper .swiper-pagination', clickable: true },
            navigation: {
                nextEl: '.event-swiper .swiper-button-next',
                prevEl: '.event-swiper .swiper-button-prev',
            },
            autoplay: shouldEnableLoop(container, 3) ? { delay: 3000, disableOnInteraction: false } : false,
            spaceBetween: 30,
            slidesPerView: 1,
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 30 }
            }
        });
    };

    // Service swiper
    HT.serviceSwiper = () => {
        const container = document.querySelector(".panel-vstep-service .service-swiper");
        if (!container) return;

        new Swiper(".panel-vstep-service .service-swiper", {
            loop: shouldEnableLoop(container, 4),
            spaceBetween: 30,
            slidesPerView: 2,
            breakpoints: {
                640: { slidesPerView: 1 },
                960: { slidesPerView: 2 },
                1280: { slidesPerView: 2 }
            },
            navigation: {
                nextEl: '.panel-vstep-service .service-next',
                prevEl: '.panel-vstep-service .service-prev',
            },
        });
    };

    // Partners swiper
    HT.partnersSwiper = () => {
        const container = document.querySelector(".panel-partners .partners-swiper");
        if (!container) return;

        new Swiper(".panel-partners .partners-swiper", {
            loop: shouldEnableLoop(container, 2),
            spaceBetween: 30,
            slidesPerView: 1,
            navigation: {
                nextEl: '.panel-partners .partners-next',
                prevEl: '.panel-partners .partners-prev',
            },
        });
    };

    // Majors list swiper (for homepage - CRITICAL FIX)
    HT.majorsListSwiper = () => {
        const container = document.querySelector(".majors-list-swiper");
        if (!container) return;

        // Check if already initialized (inline script might have done it)
        if (container.swiper) return;

        new Swiper('.majors-list-swiper', {
            slidesPerView: 3,
            spaceBetween: 30,
            navigation: {
                nextEl: '.majors-list-swiper .swiper-button-next',
                prevEl: '.majors-list-swiper .swiper-button-prev',
            },
            watchOverflow: true,
            breakpoints: {
                0: {
                    slidesPerView: 1,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                }
            }
        });
    };

    // Initialize all swipers
    HT.initAllSwipers = () => {
        HT.swiper();
        HT.category();
        HT.feedbackSwiper();
        HT.schoolsSwiper();
        HT.schoolsListSwiper();
        HT.majorsSwiper();
        HT.eventSwiper();
        HT.serviceSwiper();
        HT.partnersSwiper();
        HT.majorsListSwiper(); // CRITICAL: Initialize homepage majors slider
    };

    // Auto-initialize when loaded
    $(document).ready(HT.initAllSwipers);

    window.HT = HT;

})(jQuery);
