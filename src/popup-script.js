jQuery(document).ready(function($) {
    var popup = $('#my-popup');
    var close = $('.close');

    // Show the popup (you can customize how to trigger this)
    setTimeout(function() {
        popup.show();
    }, 1000); // Show after 1 second

    // Close the popup when the 'x' is clicked
    close.on('click', function() {
        popup.hide();
    });

    // Close the popup when clicking outside of it
    $(window).on('click', function(event) {
        if ($(event.target).is(popup)) {
            popup.hide();
        }
    });
});