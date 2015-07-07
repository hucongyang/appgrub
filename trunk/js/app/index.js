/**
 * Created by admin on 2015/3/24.
 */
$(function() {
    var app_index_params = {
        category: $('#category').val(),
        type: $('#type').val(),
        order: $('#order').val(),
        search: $('#searchValue').val().trim(),
        page_count: $('#pagecount').val(),
        page_limit: 25,
        max_id: $('#maxid').val(),
        onOff: true,
        auto_load_times: 3,
        app_url_array : [],
        load_times: 1
    };
    var app_index = window.app_index = {
        emptyForm : function () {
            $('#appUrl').val('');
            $('#url').val('');
            $('#detail').val('');
            $('#checkUrl').text('');
        },
        loadList: function(a) {
            var h = '';
            var isUpped = '';
            var favoriteTitle = '';
            for (var i = 0; i < a.length; i++) {
                h += '<dd class="list clearfix">';
                h += '<div class="content-list clearfix row">';
                h += '<div class="col-md-8">';
                h += '<div class="top pull-left">';
                isUpped = a[i].isUpped ? 'link' : '';
                h += '<a href="javascript:;" class="isLiked ' + isUpped + '" _id="' + a[i].Id + '">';
                h += '<span class="arrow"></span>';
                h += '<span class="like-num">' + a[i].count + '</span></a>';
                h += '</div>';
                h += '<div title="' + a[i].AppName + '" class="appListContent" target="_blank" _appID="' + a[i].Id + '">';
                h += '<div class="pull-left"><a href="/produce/index/' + a[i].Id + '" target="_blank" class="thumbnail" style="width:36px;height:36px;">';
                h += '<img src="' + a[i].IconUrl + '" width="36px;"/></a>';
                h += '</div>';
                h += '<div class="detail pull-left">';
                h += '<div class="clearfix"><a class="pull-left title ellipsis-text" href="/produce/index/' + a[i].Id + '" title="' + a[i].AppName + '" target="_blank">' + a[i].AppName + '</a>';
                h += '<a class="say pull-left" title="评论数">' + a[i].CommentCount + '<i></i></a>';
                var title = a[i].hasFavorited ? '已收藏' : '收藏';
                var collection_class = a[i].hasFavorited ? 'collectioned' : '';
                var content = a[i].Remarks ? a[i].Remarks : (a[i].AppInfo ? a[i].AppInfo : '');
                h += '<a class="pull-left" href="javascript:;" title="' + title + '" _id="' + a[i].Id + '" id="favorite-' + a[i].Id + '"><span class="collection ' + collection_class + '"></span></a>';
                h += '</div>';
                h += '<div class="clearfix"><div class="pull-left remarks ellipsis-text">' + content + '</div></div>';
                h += '</div>';
                h += '<div class="pull-right col-md-4">';
                h += '<a href="javascript:;" class="phone"><img src="/img/'+a[i].OS.toLowerCase()+'Grey.png"/></a>';
                h += '<a href="/user/myzone?memberid=' + a[i].commitUser + '" class="img user-thumbnail" target="_blank" _username="'+encodeURIComponent(a[i].username)+'">';
                h += '<img src="' + a[i].userurl + '" class="img-circle" width="30px;"/></a>';
                h += '</div>';
                h += '<div class="shareDateString">分享于' + a[i].CommitTime + '</div>';
                h += '</div>';
                h += '</dd>';
            }
            return h;
        },
        get_search_cond: function(){
            var search_cond = {
                page: app_index_params.load_times,
                order: app_index_params.order,
                search: app_index_params.search,
                type: app_index_params.type,
                category: app_index_params.category,
                maxid: app_index_params.max_id
            };
            return search_cond;
        },
        load: function() {
            var _self = this;
            $('#more-a').hide();
            $('#container h4').html('<img src="/img/loading.gif"/>').show();
            var search_cond = _self.get_search_cond();
            window.appgrubAjax.request("/app/list", function(data){
                    app_index_params.load_times ++;
                    $('#container h4').html('');
                    if (data.list.length > 0) {
                        app_index_params.onOff = true;
                        $('#container dl').append(app_index.loadList(data.list));
                    } else {
                        $('#container h4').html('没有更多了').show();
                        $('#more-a').hide();
                    }
                    if (app_index_params.load_times >= app_index_params.page_count) {
                        $('#container h4').html('没有更多了').show();
                        $('#more-a').hide();
                    } else {
                        if (app_index_params.load_times >= app_index_params.auto_load_times) {
                            $('#container h4').hide();
                            $('#more-a').show();
                        }
                    }
                }, search_cond, 'post');
        },
        init: function() {
            //判断分享类容是否正确
            $('#submitCancel').on('click', function () {
                app_index.emptyForm();
            });
            $('#closeButton').on('click', function () {
                app_index.emptyForm();
            });
            $('#appUrl').on('blur', function () {
                var share_url = $('#appUrl').val().trim();
                if (share_url == '') {
                    $('#checkUrl').text('请输入App链接');
                    return;
                }
                var parser = document.createElement('a');
                parser.href = share_url;
                if (app_index_params.app_url_array.length == 0) {
                    window.appgrubAjax.request("/share/getsource", function (request) {
                        app_index_params.app_url_array = request;
                        if ($.inArray(parser.host, app_index_params.app_url_array) < 0) {
                            $('#checkUrl').text('App链接有误,请参考填写规则');
                        } else {
                            $('#checkUrl').text('');
                        }
                    }, {}, 'post');
                } else {
                    if ($.inArray(parser.host, app_index_params.app_url_array) < 0) {
                        $('#checkUrl').text('App链接有误,请参考填写规则');
                    } else {
                        $('#checkUrl').text('');
                    }
                }
            });
            //滚动下拉页面
            $(window).on('scroll', function() {
                if ($(document).scrollTop() > 100) {
                    $('#return').show();
                } else {
                    $('#return').hide();
                }
                if ($(document).scrollTop() + $(window).height() >= $('dt').offset().top + $('dl').height()) {
                    if (app_index_params.onOff) {
                        app_index_params.onOff = false;
                        if (app_index_params.load_times < app_index_params.auto_load_times && app_index_params.load_times < app_index_params.page_count) {
                            setTimeout(function() {
                                window.app_index.load();
                            }, 500);
                        }
                    }
                }
            });
            $('.more-a').click(function() {
                app_index.load();
            });
            $('#shareButton').on('click', function() {
                if (not_login_flag) {
                    swal({
                        title: "请登录后再分享App",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-warning',
                        confirmButtonText: '确定'
                    });
                    return;
                }
            });
            //分享
            $('#shareSubmit').on('click', function() {
                var url = $('#url').val().trim();
                var appUrl = $('#appUrl').val().trim();
                var explain = $('#detail').val().trim();
                if (appUrl == '') {
                    $('#checkUrl').text('请输入App链接');
                    return;
                } else if (app_index_params.app_url_array.length > 0) {
                    var parser = document.createElement('a');
                    parser.href = appUrl;
                    if ($.inArray(parser.host, app_index_params.app_url_array) < 0) {
                        $('#checkUrl').text('App链接有误,请参考填写规则');
                        return;
                    }
                }
                $('#checkUrl').text('');
                $(this).attr('disabled', true);
                window.appgrubAjax.request('/share/add', function(data) {
                    $('#shareSubmit').removeAttr("disabled");
                    if (data.code < 0) {
                        swal({
                            title: data.msg,
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonClass: 'btn-warning',
                            confirmButtonText: '确定'
                        });
                    } else {
                        $('#myModal').modal('hide');
                        $('#appUrl').val('');
                        $('#url').val('');
                        $('#detail').val('');
                        swal({
                            title: "分享成功!",
                            text: "审核通过后会展示到列表中(未通过审核的App可以在我的主页中查看)！",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonClass: 'btn-info',
                            confirmButtonText: '确定'
                        });
                    }
                }, {
                    'url': url,
                    'appUrl': appUrl,
                    'explain': explain
                }, 'post');
            });
            //返回顶部
            $('#return').click(function () {
                $('html,body').animate({scrollTop: '0px'}, 500);
            });

        }
    };
    app_index.init();
});
