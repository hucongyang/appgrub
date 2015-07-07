//input聚焦默认value消失，离开若没有输入任何字默认value显现
$(function() {
    var arrPh = [];
    var aInput = document.getElementsByTagName('input');

    function index(obj, n) {
        obj.setAttribute('_index', n);
    }
    for (var i = 0; i < aInput.length; i++) {
        index(aInput[i], i);
    }
    $('input').each(function() {
        arrPh.push($(this).attr('placeholder'));
    })

    $('input').focus(function() {
        var t = $(this).attr('placeholder');
        $(this).attr('placeholder', '');
    });

    $('input').blur(function() {
        var index = $(this).attr('_index');
        if ($(this).val().trim() == '') {
            $(this).attr('placeholder', arrPh[index]);
        }
    });

});

//通知
$(function() {
    if (!not_login_flag) {
        $.ajax({
            type: 'get',
            cache: false,
            url: "/msg/noticecount",
            success: function(data) {
                if (data != 0) {
                    if (data > 99) {
                        data = '99+';
                    }
                    $('#message').append('<span class="message-num">' + data + '</span>');
                }
            }
        });
    }
});

$(function() {
    $('body').on('mouseover', '.user-thumbnail', function() {
        $('.user-tooltip').hide();
        var parent = $(this).parent();
        var tooltip = parent.find('.user-tooltip');
        if (!tooltip.length) {
            var user_name = $(this).attr('_username') ? decodeURIComponent($(this).attr('_username')) : '';
            tooltip = $('<div class="tooltip bottom user-tooltip"/>').attr('role', 'tooltip').append('<div class="tooltip-arrow"></div>');
            var tooltip_inner = $('<div class="tooltip-inner"/>');
            var img_obj = $('<img class="img-circle"/>').attr('src', $(this).find('img').attr('src'));
            var p_obj = $('<p />').text(user_name);
            tooltip.append(tooltip_inner.append(img_obj).append(p_obj)).appendTo(parent);
            var r = -(tooltip.width() - $(this).width()) / 2 + 10;
            tooltip.css({
                'right': r
            });
        }
        tooltip.show();
    }).on('mouseout', '.user-thumbnail', function() {
        $('.user-tooltip').hide();
    }).on('mouseover', '.user-tooltip', function() {
        $(this).show();
    }).on('mouseout', '.user-tooltip', function() {
        $(this).hide();
    }).on('click', '.isLiked', function(e) {
        window.appgrub_common.up($(this));
        e.stopPropagation();
        e.preventDefault();
    }).on('click', '.collection', function(e) {
        window.appgrub_common.collection($(this).parent().attr('_id'));
        e.stopPropagation();
        e.preventDefault();
    });
});


$(function() {
    var iTimer = null;
    var aTimer = null;
    var socket_flag = false;
    //分享,登录
    $('.dropdown-hover').hover(function() {
        clearTimeout(aTimer);
        if ($(this).hasClass('socket-login')) {
            if ($('#download_app_qrcode').length) {
                $('#download_app_qrcode').hide();
            }
            if (!socket_flag) {
                window.nodejs_socket_io.init();
                socket_flag = true;
            }
        }
        $(this).next().addClass('dropdown-fade-in');
    }, function() {
        var _this = $(this);
        iTimer = setTimeout(function() {
            _this.next().removeClass('dropdown-fade-in');
            if (_this.hasClass('socket-login')) {
                if ($('#download_app_qrcode').length) {
                    $('#download_app_qrcode').show();
                }
                window.nodejs_socket_io.disconnect();
                socket_flag = false;
            }
        }, 50);
    });

    $('.dropdown-box').hover(function() {
        clearTimeout(iTimer);
        $(this).addClass('dropdown-fade-in');
        if ($(this).prev().hasClass('socket-login')) {
            if ($('#download_app_qrcode').length) {
                $('#download_app_qrcode').hide();
            }
            if (!socket_flag) {
                window.nodejs_socket_io.init();
                socket_flag = true;
            }
        }
    }, function() {
        var _this = $(this);
        aTimer = setTimeout(function() {
            _this.removeClass('dropdown-fade-in');
            if (_this.prev().hasClass('socket-login')) {
                if ($('#download_app_qrcode').length) {
                    $('#download_app_qrcode').show();
                }
                window.nodejs_socket_io.disconnect();
                socket_flag = false;
            }
        }, 50);
    });

});