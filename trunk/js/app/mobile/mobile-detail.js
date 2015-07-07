$(function() {
    var ua = navigator.userAgent.toLowerCase();
    var is_weixin =  ua.indexOf('micromessenger') >= 0 ? true : false;
    var detail_params = {
        appID: $('#appID').val(),
        appName: $('#app_name').text(),
        appLogo: $('#app_logo').attr('src')
    };
    var product_detail = {
        init: function() {
            var self = this;
            var obj = document.getElementsByTagName('textarea');
            var len = obj.length;

            if (document.URL.indexOf('#comment-list') != -1) {
                location.href = "#comment-list";
            }

            $.fn.extend({
                comment: function() {
                    $(this).click(function() {
                        var _this = $(this);
                        $(this).next().show();
                        setTimeout(function() {
                            _this.next().hide();
                        }, 1000);
                    });
                }
            });
            $('.detail-up').on('click', function() {
                var $obj = $(this);
                var app_id = $obj.attr('_id');
                var url, num;
                var like_num_obj = $obj.find('.like-num');
                if ($obj.hasClass('link')) {
                    url = '/produce/dislike';
                    num = (+like_num_obj.text()) - 1;
                } else {
                    url = '/produce/like';
                    num = (+like_num_obj.text()) + 1;
                }
                window.appgrubAjax.request(url, function(data) {
                    $obj.toggleClass('link');
                    like_num_obj.text(num);
                }, {
                    'id': app_id
                }, 'post');
                return;
            });
            $('.detail-collect').on('click', function(){
                var $obj = $(this);
                var app_id = $obj.attr('_id');
                var url, msg;
                if ($obj.hasClass('collected')) {
                    url = '/produce/unfavorite';
                    msg = '收藏';
                } else {
                    url = '/produce/favorite';
                    msg = '已收藏';
                }
                window.appgrubAjax.request(url, function(data) {
                    $obj.toggleClass('collected').find('.collect-msg').text(msg);
                }, {
                    'id': app_id
                }, 'post');
                return;
            });
            $('#share_weibo').on('click', function() {
                var img_obj = $('.soft-img').find('img').eq(0);
                var jiathis_config = {
                    webid: 'tsina',
                    url: window.location.href,
                    title: encodeURIComponent('#App哥伦部#'),
                    summary: encodeURIComponent('我在App哥伦部网站上发现了一个很好玩的App《' + detail_params.appName + '》，你们也来看看吧！'),
                    pic: img_obj.length ? img_obj.attr('src') : '',
                    appkey: 2861846506,
                    ralateuid: 2702377141
                };
                var share_link = 'http://www.jiathis.com/send/?';
                var params = [];
                $.each(jiathis_config, function(i, o) {
                    params.push(i + '=' + o);
                });
                share_link += params.join('&');
                window.open(share_link);
            });
            $('#comment_wrap').on('click', '.reply', function() {
                var _this = $(this);
                if ($(this).next().next().hasClass('btn-say')) return;
                $("#replyDiv").remove();
                var obj = document.getElementById("replyContent");
                var _id = $(this).attr("_id");
                if (!obj) {
                    $(this).next().after('<div id="replyDiv" _attr=""><textarea default_height="23" max_height="70" class="replyContent" name="replyContent" id="replyContent" _id="' + _id + '" cols="30" rows="10"></textarea><div style="max-width:500px;" class="clearfix"><a href="javascript:;" class="face-ico face-btn" style="margin-top:10px;"><em></em></a><a href="javascript:;" id="replyCanncel" class="btn-canncel pull-right" _index="0" style="margin-top:10px;">取消</a><a href="javascript:;" id= "replySubmit" class="btn-primary btn-say pull-right sweet-2" _index="1" style="margin-top:10px;">发表回复</a></div></div>');
                    $('.btn-say[_index=0]').comment();
                    $('#replyCanncel').click(function() {
                        $(this).find('.reply').hide();
                        $("#replyDiv").remove();
                    });
                    $('#replySubmit').click(function() {
                        var content = $('#replyContent').val().trim();
                        var appID = $('#appID').val();
                        if (content != '') {
                            window.appgrubAjax.request(
                                "/produce/comment",
                                function(data) {
                                    var html = '<li id="comment_' + data.id + '">';
                                    html += '<div class="comment-comment comment-line">' +
                                        '<div class="good-people">' +
                                        '<a href="/user/myzone?memberid=' + data.authorID + '" class="img user-thumbnail" target="_blank" _username="' + encodeURIComponent(data.username) + '">' +
                                        '<img src="' + data.authorIcon + '" class="img-circle"/>' +
                                        '</a>' +
                                        '</div>' +
                                        '<a class="aTagWrapName" title="' + data.username + '" href="/user/myzone?memberid=' + data.authorID + '">' + data.username + '</a> 回复了 <a href="/user/myzone?memberid=' + data.toAuthorID + '" class="aTagWrapName" target="_blank" id="aTag" title="' + data.toAuthorUserName + '" >' + data.toAuthorUserName + '</a>:&nbsp;<small class="reply-time"> 刚刚</small>' +
                                        '<a href="javascript:;" class="delete" _id="' + data.id + '"> 删除</a>' +
                                        '<div class="user-content clearfix">' +
                                        '<p>' + data.content + '</p>';
                                    html += '</div></div></li>';
                                    $('#userInfo-' + data.pid).next().append(html);
                                    var commentCount = $('#commentCount').html();
                                    $('#commentCount').html(++commentCount);
                                    $('#nav_bar_comment_num').text(commentCount);
                                    $("#replyDiv").remove();
                                }, {
                                    'content': content,
                                    'appID': appID,
                                    'replayId': _id
                                },
                                'post'
                            );
                        } else {
                            swal({
                                title: "请输入回复内容",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonClass: 'btn-warning',
                                confirmButtonText: '确定'
                            });
                        }
                    });
                }
            }).on('click', '.delete', function() {
                var _id = $(this).attr("_id");
                if (_id) {
                    swal({
                        title: "确定要删除评论吗?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        cancelButtonText: "取消",
                        confirmButtonText: "确定",
                        closeOnConfirm: true
                    }, function() {
                        window.appgrubAjax.request('/produce/deletereply', function(data) {
                            var delete_num = +data.num;
                            $('#comment_' + _id).slideUp(500).remove();
                            var commentCount = +$('#commentCount').text();
                            $('#commentCount').text(commentCount - delete_num);
                            $('#nav_bar_comment_num').text(commentCount - delete_num);
                        }, {
                            appID: detail_params.appID,
                            replyID: _id
                        }, 'post');
                    });
                }
            });
            $('#submit').click(function() {
                var content = $('#content').val().trim();
                if (content != '') {
                    var appID = $('#appID').val();
                    window.appgrubAjax.request(
                        "/produce/comment",
                        function(data) {
                            var html = '';
                            html += '<div class="comment clearfix" id="comment_' + data.id + '">' +
                                '<div class="user-info comment-line" id="userInfo-' + data.id + '">' +
                                '<div class="good-people">' +
                                '<a href="/user/myzone?memberid=' + data.authorID + '" class="img user-thumbnail" target="_blank" _username="' + encodeURIComponent(data.username) + '">' +
                                '<img src="' + data.authorIcon + '" class="img-circle"/>' +
                                '</a>' +
                                '</div>' +
                                '<a class="aTagWrapName" title="' + data.username + '" href="/user/myzone?memberid=' + data.authorID + '" >' + data.username + '</a>&nbsp;<small> 刚刚</small>' +
                                '<a href="javascript:;" class="delete" _id="' + data.id + '"> 删除</a>' +
                                '<div class="user-content clearfix" id="replyNode-' + data.id + '">' +
                                '<p>' + data.content +
                                '</p>' +
                                '</div>' +
                                '</div>';
                            $('#comment_wrap').prepend(html);
                            var commentCount = $('#commentCount').html();
                            $('#commentCount').text(++commentCount);
                            $('#nav_bar_comment_num').text(commentCount);
                            $('#content').val('');
                        }, {
                            'content': content,
                            'appID': appID
                        },
                        'post'
                    );
                } else {
                    swal({
                        title: '请输入评论内容',
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-warning',
                        confirmButtonText: '确定'
                    });
                }
            });

            //图片切换（支持手指滑动）
            $('#app_carousel').hammer().on('swipeleft', function(){
                $(this).carousel('next');
            });
            $('#app_carousel').hammer().on('swiperight', function(){
                $(this).carousel('prev');
            });

            if (is_weixin) {
                var weixin_type = ua.indexOf('android') < 0 ? 'ios' : 'android';
                $download_notice = $('<div id="download_notice_cover" class="download-notice-cover hide"/>')
                                    .html('<img class="img-notice pull-right" src="/img/download_notice_' + weixin_type + '.png"/>')
                                    .appendTo('body')
                                    .on('click', function(){
                                        $(this).addClass('hide');
                                    });
                $('#download_app_btn').on('click', function(e){
                    $('#download_notice_cover').removeClass('hide');
                    e.stopPropagation();
                    e.preventDefault();
                });
            }

        }
    };
    product_detail.init();
    //微信分享
    wx.ready(function() {
        wx.onMenuShareTimeline({
            title: 'App哥伦部-《' + detail_params.appName + '》', // 分享标题
            link: window.location.href, // 分享链接
            imgUrl: detail_params.appLogo, // 分享图标
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareAppMessage({
            title: 'App哥伦部-《' + detail_params.appName + '》', // 分享标题
            desc: 'App哥伦部，发现好玩的App！', // 分享描述
            link: window.location.href, // 分享链接
            imgUrl: detail_params.appLogo, // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareQQ({
            title: 'App哥伦部-《' + detail_params.appName + '》', // 分享标题
            desc: 'App哥伦部，发现好玩的App！', // 分享描述
            link: window.location.href, // 分享链接
            imgUrl: detail_params.appLogo, // 分享图标
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
    });
});