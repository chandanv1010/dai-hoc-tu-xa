// Scroll and interaction handlers - Lazy loaded
(function ($) {
    "use strict";

    const HT = window.HT || {};

    // Scroll to contact section
    HT.scroll = () => {
        $('a[href="#panel-contact"]').on('click', function (event) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: $('#panel-contact').offset().top - 50
            }, 800);
        });
    };

    // Scroll to heading from TOC
    HT.scrollHeading = () => {
        $(document).on('click', '.widget-toc a', function (e) {
            e.preventDefault();

            const href = $(this).attr('href');
            if (href && href.startsWith('#')) {
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    $('html, body').animate({
                        scrollTop: $(targetElement).offset().top - 100
                    }, 800);

                    $('.widget-toc a').removeClass('active');
                    $(this).addClass('active');
                }
            }
        });
    };

    // Highlight TOC on scroll
    HT.highlightTocOnScroll = () => {
        $(window).on('scroll', function () {
            const scrollTop = $(window).scrollTop();

            $('.widget-toc a').each(function () {
                const href = $(this).attr('href');
                if (href && href.startsWith('#')) {
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const elementTop = $(targetElement).offset().top - 150;
                        const elementBottom = elementTop + $(targetElement).outerHeight();

                        if (scrollTop >= elementTop && scrollTop < elementBottom) {
                            $('.widget-toc a').removeClass('active');
                            $(this).addClass('active');
                        }
                    }
                }
            });
        });
    };

    // Remove pagination on filter slide
    HT.removePagination = () => {
        $('.filter-content').on('slide', function () {
            $('.uk-flex .pagination').hide();
        });
    };

    // Initialize WOW animations (lazy loaded)
    HT.wow = () => {
        if (typeof WOW === 'undefined') return;

        new WOW({
            boxClass: 'wow',
            animateClass: 'animated',
            offset: 0,
            mobile: true,
            live: true,
            resetAnimation: true,
        }).init();
    };

    // Initialize all interactions
    HT.initInteractions = () => {
        HT.scroll();
        HT.scrollHeading();
        HT.highlightTocOnScroll();
        HT.removePagination();

        // Lazy load WOW if elements exist
        if (document.querySelector('.wow')) {
            HT.wow();
        }
    };

    // Auto-initialize
    $(document).ready(HT.initInteractions);

    window.HT = HT;

})(jQuery);
