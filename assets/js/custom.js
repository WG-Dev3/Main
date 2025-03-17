jQuery(document).ready(function () {
    jQuery(".search_field").on("input", function () {
        let value = jQuery(this).val().trim();
        jQuery(".site-header .card-list").html('<div class="loading-message">Loading...</div>');

        jQuery.ajax({
            type: "POST",
            url: wg_ajax_object.ajaxurl,
            data: {
                action: "wg_ajax_action",
                security: wg_ajax_object.nonce,
                search_data: value
            },
            dataType: "json",
            success: function(response) {
                jQuery(".site-header .card-list").empty();

                if (response.success && response.data.status) {
                    let html = response.data.html;
                    jQuery(".site-header .card-list").append(html);
                } else {
                    jQuery(".site-header .card-list").html('<div class="error-message">Post Not Found.</div>');
                }
            },
            error: function() {
                jQuery(".site-header .card-list").html('<div class="error-message">Something went wrong. Please try again.</div>');
            }
        });
    });
});