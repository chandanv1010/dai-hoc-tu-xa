// Core utilities - Load immediately
(function ($) {
    "use strict";

    window.HT = window.HT || {};
    const HT = window.HT;

    // CSRF Token
    HT._token = $('meta[name="csrf-token"]').attr('content');

    // Wrap tables for mobile responsiveness
    HT.wrapTable = () => {
        const width = $(window).width();
        if (width < 600) {
            $('table').wrap('<div class="uk-overflow-container"></div>');
        }
    };

    // Initialize on DOM ready
    $(document).ready(function () {
        HT.wrapTable();
    });

})(jQuery);
