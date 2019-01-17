$(document).ready(function() {
    $('#link').tooltip({ placement: "bottom" });
    $('#back-to-top').tooltip({ placement: "right" });

    $('#back-to-top').on('click', function (event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, 1500);
        return false;
    });
});
