(function($) {
	"use strict";
	var HT = {}; 

    HT.slide = () => {
        if($('.owl-carousel').length > 0){
            $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: 3
                    }
                }
            });
        }
    }
    console.log("Slide initialized");

	$(document).ready(function(){
        HT.slide()
	});

})(jQuery);
