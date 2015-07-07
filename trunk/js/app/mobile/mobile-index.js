$(function() {
    var app_index_params = {
        page: 0,
        onOff: true,
        next: 0,
        page_count: 0,
        category: 0,
        type: 0,
        maxid: 0,
        order: 1
    };
    var app_index = {
        fit_data: function(data) {
            var h = '';
            var length = data.length;
            for (var i = 0; i < length; i++) {
                var up_class = '';
                if (data[i].isUpped) {
                    up_class = 'link';
                }
                var typeClass = 'd-type-' + data[i].OS.toLowerCase();
                var summary = data[i].Remarks == '' ? data[i].AppInfo : data[i].Remarks;
                h += '<dd class="list clearfix">';
                h += '<div class="content-list clearfix row">';
                h += '<div class="col-md-8 clearfix"><div class="top pull-left">';
                h += '<a href="javascript:;" class="isLiked ' + up_class + '" _id="' + data[i].Id + '">';
                h += '<span class="arrow"></span>';
                h += '<span class="like-num">' + data[i].count + '</span></a></div>';
                h += '<div class="detail pull-left"><div class="d-img"><a href="javascript:;" class="dimg"><img src="' + data[i].IconUrl + '" class="img-circle img-radius"/></a></div><div class="limit clearfix"><a href="/produce/index/' + data[i].Id + '" target="_blank" class="title">' + data[i].AppName + '</a><div class="say pull-right"><i class="' + typeClass + '"></i></div></div>';
                h += '<div class="limit-auto clearfix"><p>' + summary + '</p></div>';
                h += '<div class="shareDateMobile clearfix"><div class="pull-right">' + data[i].CommitTime + '</div><div class="say pull-right" style="margin-right:10px;">' + data[i].CommentCount + '<i></i></div></div></div></div></div>';
                h += '<a href="/produce/index/' + data[i].Id + '" class="content-link" target="_blank"></a></dd>';
            }
            return h;
        },
        set_init_page_condition: function() {
            app_index_params.next = 0;
            app_index_params.page_count = 0;
            app_index_params.maxid = 0;
        },
        get_search_cond: function() {
            var search_cond = {
                page: app_index_params.next,
                order: app_index_params.order,
                category: app_index_params.category,
                type: app_index_params.type,
                search: $('#search').val().trim(),
                maxid: app_index_params.maxid
            };
            return search_cond;
        },
        mobile_load: function() {
            var self = this;
            $('#container h4').html('<img src="/img/loading.gif"/>');
            window.appgrubAjax.request("/app/list", function(data) {
                    app_index_params.next++;
                    $('#container h4').html('');
                    if (app_index_params.maxid == 0) {
                        app_index_params.maxid = +data.maxid;
                    }
                    if (app_index_params.page_count == 0) {
                        app_index_params.page_count = data.pageCount;
                    }
                    var list = data.list;
                    if (list.length > 0) {
                        $('#appList').append(self.fit_data(list));
                    } else {
                        $('#container h4').html('没有更多了').show();
                    }
                    if (app_index_params.next >= app_index_params.page_count) {
                        $('#container h4').html('没有更多了').show();
                    } else {
                        app_index_params.onOff = true;
                    }
                }, self.get_search_cond(), 'post');
        },
        reload: function() {
            var self = this;
            $('#appList').html('');
            self.mobile_load();
        },
        init: function() {
            var self = this;
            //侧边标签
            $('.title-left ul li').on('click', function(e) {
                $('.section-list').toggle();
                e.stopPropagation();
                e.preventDefault();
            });

            $('.section-list').find('a').on('click', function(e) {
                var _value = $(this).attr('_value');
                var _key = $(this).parent().attr('_key');
                $(this).addClass('active').siblings().removeClass('active');
                self.set_init_page_condition();
                app_index_params[_key] = _value;
                var type_category_name = $('.section-list').find('a.active').map(function() {
                    return $(this).text();
                }).get().join('/');
                $('#type_category_name').text(type_category_name);
                $('.section-list').toggle();
                self.reload();
                e.stopPropagation();
                e.preventDefault();
            });

            $('.nav_list li').on('click', function() { //点击手机端下方排序
                var order = $(this).attr('_value');
                $(this).addClass('active').siblings().removeClass('active');;
                self.set_init_page_condition();
                app_index_params.order = order;
                self.reload();
            });
            $(window).on('scroll', function() { //滚动下拉加载数据
                if ($(document).scrollTop() > 100) {
                    $('#return').show();
                } else {
                    $('#return').hide();
                }
                if ($(document).scrollTop() + $(window).height() >= $('#appList').height()) {
                    if (app_index_params.onOff) {
                        app_index_params.onOff = false;
                        if (app_index_params.page_count && app_index_params.page_count >= app_index_params.next) {
                            self.mobile_load();
                        } else {
                            return;
                        }
                    }
                }
            });
            //返回顶部
            $('#return').on('click', function() {
                $('html,body').animate({
                    scrollTop: '0px'
                }, 500);
            });
            //搜索框
            $('.search i').click(function() {
                $(this).next().css({
                    '-webkit-transform': 'translateX(-40px)',
                    '-ms-transform': 'translateX(-40px)',
                    'transform': 'translateX(-40px)'
                }).focus();
            });
            $('.search input').width($('#header h2 p a').offset().left - 50);
            $('.search input').blur(function() {
                $(this).css({
                    '-webkit-transform': 'translateX(-300px)',
                    '-ms-transform': 'translateX(-300px)',
                    'transform': 'translateX(-300px)'
                })
            }).keypress(function(e) { //搜索
                if (e.keyCode == 13) {
                    self.set_init_page_condition();
                    self.reload();
                }
            });
            var user_id = +$('#userId').val();
            var is_follow = +$('#isFollow').val();
            if (!is_follow) {
                var follow_obj = $('<div />').addClass('alert alert-follow hide').html('<button type="button" class="close">&times;</button>关注<strong><a href="http://mp.weixin.qq.com/s?__biz=MzAxOTI5NzE1Nw==&mid=204887815&idx=1&sn=d645febddafba1007584c6758552ee4a#rd">appgrub</a></strong>，随时随地发现好玩的App！');
                setTimeout(function() {
                    follow_obj.prependTo('body').slideDown(500).find('.close').on('click', function() {
                        follow_obj.slideUp(500, function() {
                            follow_obj.remove();
                        });
                    });
                }, 1000);
            }
            $('#share_app').on('click', function() {
                if (!user_id) {
                    swal({
                        title: "请登录后再分享App",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-warning',
                        confirmButtonText: '确定'
                    });
                    return;
                }
                window.location.href = '/share/index';
            });

            self.mobile_load(); //第一次加载首页数据
        }
    };
    app_index.init();
});

//微信分享
wx.ready(function() {
    wx.onMenuShareTimeline({
        title: 'App哥伦部，发现好玩的App！', // 分享标题
        link: window.location.href, // 分享链接
        imgUrl: 'http://www.appgrub.com/img/logo.png', // 分享图标
        success: function() {
            // 用户确认分享后执行的回调函数
        },
        cancel: function() {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: 'App哥伦部', // 分享标题
        desc: 'App哥伦部，发现好玩的App！', // 分享描述
        link: window.location.href, // 分享链接
        imgUrl: 'http://www.appgrub.com/img/logo.png', // 分享图标
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
        title: 'App哥伦部', // 分享标题
        desc: 'App哥伦部，发现好玩的App！', // 分享描述
        link: window.location.href, // 分享链接
        imgUrl: 'http://www.appgrub.com/img/logo.png', // 分享图标
        success: function() {
            // 用户确认分享后执行的回调函数
        },
        cancel: function() {
            // 用户取消分享后执行的回调函数
        }
    });
});