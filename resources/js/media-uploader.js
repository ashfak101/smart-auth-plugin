jQuery(document).ready(function ($) {
    var logoUploader;
    var bannerUploader;

    $('#promotion_barnd_logo_button').click(function (e) {
        e.preventDefault();
        // If the uploader object has already been created, reopen the dialog
        if (logoUploader) {
            logoUploader.open();
            return;
        }
        // Extend the wp.media object
        logoUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Logo',
            button: {
                text: 'Choose Logo'
            },
            multiple: false
        });
        // When a file is selected, grab the URL and set it as the text field's value
        logoUploader.on('select', function () {
            var attachment = logoUploader.state().get('selection').first().toJSON();
            $('#promotion_barnd_logo').val(attachment.url);
            $('#promotion_barnd_logo_preview').attr('src', attachment.url);
        });
        // Open the uploader dialog
        logoUploader.open();
    });

    $('#promotion_banner_button').click(function (e) {
        e.preventDefault();
        // If the uploader object has already been created, reopen the dialog
        if (bannerUploader) {
            bannerUploader.open();
            return;
        }
        // Extend the wp.media object
        bannerUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Banner',
            button: {
                text: 'Choose Banner'
            },
            multiple: false
        });
        // When a file is selected, grab the URL and set it as the text field's value
        bannerUploader.on('select', function () {
            var attachment = bannerUploader.state().get('selection').first().toJSON();
            $('#promotion_banner').val(attachment.url);
            $('#promotion_banner_preview').attr('src', attachment.url);
        });
        // Open the uploader dialog
        bannerUploader.open();
    });
});