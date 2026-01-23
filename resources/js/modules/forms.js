// Form handlers and AJAX interactions - Lazy loaded
(function ($) {
    "use strict";

    const HT = window.HT || {};
    const _token = $('meta[name="csrf-token"]').attr('content');

    // Advise form handler
    HT.advise = () => {
        $(document).on('click', '.suggest-aj button', function (e) {
            e.preventDefault();

            const option = {
                name: $('#suggest input[name=name]').val(),
                gender: $('#suggest input[name=gender]').val(),
                phone: $('#suggest input[name=phone]').val(),
                address: $('#suggest input[name=address]').val(),
                post_id: $('#suggest input[name=post_id]').val(),
                product_id: $('#suggest input[name=product_id]').val(),
                _token: _token,
            };

            toastr.success('Gửi yêu cầu thành công, chúng tôi sẽ sớm liên hệ với bạn!', 'Thông báo từ hệ thống');

            $.ajax({
                url: 'ajax/contact/advise',
                type: 'POST',
                data: option,
                dataType: 'json',
                success: function (res) {
                    if (res.code === 10) {
                        setTimeout(() => location.reload(), 1000);
                    } else if (res.status === 422) {
                        const errors = res.messages;
                        for (let field in errors) {
                            $('.' + field + '-error').text(errors[field]);
                        }
                    }
                },
            });
        });
    };

    // Request consult handler
    HT.requestConsult = () => {
        $(document).on('click', '#advise button', function (e) {
            e.preventDefault();

            const phone = $('#advise input[name=phone]').val();
            if (!phone || !/^(0[3|5|7|8|9][0-9]{8})$/.test(phone)) {
                alert('Vui lòng nhập số điện thoại hợp lệ (10 chữ số, bắt đầu bằng 0).');
                return;
            }

            toastr.success('Gửi thông tin thành công. Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất', 'Thông báo từ hệ thống');

            $.ajax({
                url: 'ajax/contact/requestConsult',
                type: 'POST',
                data: { phone, _token },
                dataType: 'json',
                success: function (res) {
                    if (res.status == true) {
                        $('#advise input[name=phone]').val('');
                    }
                },
            });
        });
    };

    // Add voucher toggle
    HT.addVoucher = () => {
        $(document).on('click', '.info-voucher', function (e) {
            e.preventDefault();
            $(this).toggleClass('active');
        });
    };

    // Load distribution
    HT.loadDistribution = () => {
        $(document).on('click', '.agency-item', function () {
            const _this = $(this);

            $('.agency-item').removeClass('active');
            _this.addClass('active');

            $.ajax({
                url: 'ajax/distribution/getMap',
                type: 'GET',
                data: { id: _this.attr('data-id') },
                dataType: 'json',
                success: function (res) {
                    $('.agency-map').html(res);
                },
            });
        });
    };

    // Initialize all form handlers
    HT.initForms = () => {
        HT.advise();
        HT.requestConsult();
        HT.addVoucher();
        HT.loadDistribution();
    };

    // Auto-initialize
    $(document).ready(HT.initForms);

    window.HT = HT;

})(jQuery);
