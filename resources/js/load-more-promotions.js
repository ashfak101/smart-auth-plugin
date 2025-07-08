(function ($) {
    $('#load-more-promotions').on('click', function () {
        const button = $(this);
        const page = parseInt(button.data('page')) + 1;
        const nonce = button.data('nonce');

        button.attr('disabled', true).text('Loading...');

        $.ajax({
            url: saPromotionsParams.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_more_promotions',
                page: page,
                nonce: nonce,
                atts: saPromotionsParams.atts,
            },
            success: function (response) {
                if (response) {
                    $('#promotions_list').append(response);
                    button.data('page', page).attr('disabled', false).text('Load More');
                } else {
                    button.text('No more promotions').attr('disabled', true);
                }
            },
        });
    });
})(jQuery);
