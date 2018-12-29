$(document).ready(function() {
    $('#create_article').tooltip({ placement: "bottom" });
    $('.read_more').tooltip({ placement: "bottom" });
    $('#back').tooltip({ placement: "bottom" });
    $('#delete').tooltip({ placement: "right" });
    $('#update').tooltip({ placement: "right" });
    $('#edit').tooltip({ placement: "right" });
    $('#my_articles').tooltip({ placement: "bottom" });
    $('#my_likes').tooltip({ placement: "bottom" });
    $('#my_comments').tooltip({ placement: "bottom" });

    $('a#button-like').click(function () {
        var button = $(this);
        var params = {
            'id': $(this).attr('data-id')
        };
        $.post("/article/like", params, function (data) {
            if (data.success) {
                button.hide();
                button.siblings('#button-unlike').show();
                button.siblings('#likes-count').html(data.likesCount);
            }
        });
        return false;
    });

    $('a#button-unlike').click(function () {
        var button = $(this);
        var params = {
            'id': $(this).attr('data-id')
        };
        $.post('/article/unlike', params, function (data) {
            if (data.success) {
                button.hide();
                button.siblings('#button-like').show();
                button.siblings('#likes-count').html(data.likesCount);
            }
        });
        return false;
    });
});
