$(function(){
    $('#login-me').on('click', function () {
        var menu_obj = $(this).next();
        if(menu_obj.css('visibility') == 'hidden') {
            $(this).css('background-color', "#4a4949");
            menu_obj.css({
                'top': 65,
                'opacity': 1,
                'visibility': 'visible'
            });
        } else {
            $(this).css('background-color', "");
            menu_obj.css({
                'top': 50,
                'opacity': 0,
                'visibility': 'hidden'
            });
        }
    });
    $('body').on('click', '.isLiked', function(e) {
        window.appgrub_common.up($(this));
        e.stopPropagation();
        e.preventDefault();
    });
    //通知
    if (!not_login_flag) {
        $.ajax({
            type: 'get',
            cache: false,
            url: "/msg/noticecount",
            success: function(data) {
                if (data != 0) {
                    $('#login-me').append('<span class="notice-mark"></span>');
                    $('#msg_menu').append('<span class="notice-badge badge"></span>').find('.notice-badge').text(data);
                }
            }
        });
    }
});

