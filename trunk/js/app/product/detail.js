$(function() {
    var detail_params = {
        pageCount: Math.ceil($('#userLength').val() / 50),
        width: 900,
        i: 0,
        h: 30,
        isAdded: 0,
        appID: $('#appID').val(),
        users: new Array(),
        userName: []
    }
    var product_detail = {
        have: function(obj) {
            obj.css({
                'left': $('.face-btn').position().left,
                'top': $('.face-btn').position().top + detail_params.h,
                'display': 'block'
            })
            obj.animate({
                'opacity': 1
            });
        },
        noHave: function(obj) {
            obj.animate({
                'opacity': 0
            }, function() {
                obj.hide();
            });
        },
        changeSize: function(o) {
            var minh = o.getAttribute('default_height');
            var maxh = o.getAttribute('max_height');
            o.style.height = minh + "px";
            var height = o.scrollHeight;
            if (height >= minh && height < maxh) {
                o.style.height = height + "px";
            } else if (height >= maxh) {
                o.style.height = maxh + "px";
            } else {
                o.style.height = minh + "px";
            }
        },
        btnPlay: function(obj1, obj2, leftRight) {
            if (detail_params.i <= 0) {
                detail_params.i = 0;
                obj1.addClass('old');
            } else {
                obj1.removeClass('old');
            }
            if (detail_params.i >= detail_params.pageCount - 1) {
                detail_params.i = detail_params.pageCount - 1;
                obj2.addClass('old');
            } else {
                obj2.removeClass('old');
            }
            if (!leftRight) {
                if (detail_params.i > detail_params.isAdded) {
                    var start = detail_params.i * 50;
                    window.appgrubAjax.request(
                        "/produce/getlikedpeople", function(data) {
                            if(data.length){
                                var content = '<li class="pull-left" style="width:900px;"><div class="good-people-area">';
                                for (var j = 0; j < data.length; j++) {
                                    content += '<div class="good-people">';
                                    content += "<a href='/user/myzone?memberid=" + data[j].ID + "' class='img like-user-thumbnail' target='_blank' _username='" + encodeURIComponent(data[j].username) + "'>";
                                    content += "<img src='" + data[j].userurl + "' class='img-circle'/>";
                                    content += '</a></div>';
                                }
                                content += '</div></li>';
                                $('#iconArea').append(content);
                            }
                            detail_params.isAdded = detail_params.i;
                        },{
                            appID: detail_params.appID,
                            start: start
                        }, "post");
                }
            }
            $('.good-wrap ul').css({
                '-webkit-transform': 'translateX(-' + detail_params.i * detail_params.width + 'px)',
                '-ms-transform': 'translateX(-' + detail_params.i * detail_params.width + 'px)',
                'transform': 'translateX(-' + detail_params.i * detail_params.width + 'px)'
            });
        },
        getAtWho: function(textareaID) {
            window.appgrubAjax.request(
                '/produce/searchuser',
                function(request) {
                    var names = $.map(request, function(value) {
                        return {
                            'img': value['icon'],
                            'name': value['name'],
                            'insert': value['encode_name']
                        };
                    });
                    $('#' + textareaID).atwho({
                        at: "@",
                        searchKey: "name",
                        displayTpl: '<li><small><img src="${img}" class="atIcon"/></small> <div class="aTagWrapName">${insert}</div></li>',
                        insertTpl: '@${name}',
                        data: names
                    });
                }, {
                    appID: detail_params.appID
                },
                'post'
            );
        },
        getFaceIcon: function() {
            $('#layer_faces').html('<img src="/img/loading.gif"/>');
            window.appgrubAjax.request(
                '/produce/getFaceIcon',
                function(request) {
                    $('#layer_faces').html('');
                    var html = '';
                    html += '<div class="WB_minitab clearfix">' +
                        '<ul class="minitb_ul S_line1 S_bg1 pull-left">';
                    for (var i = 0; i < request['faceTab'].length; i++) {
                        html += "<li class='pull-left current' title='$title'>" +
                            "<a href='javascript:void(0);'>" + request['faceTab'][i] + "</a>" +
                            "<span></span></li>";
                    }
                    html += '</ul>' +
                        '</div>' +
                        '<div class="faces_list_box">' +
                        '<div class="faces_list UI_scrollView" node-type="scrollView">' +
                        '<div class="UI_scrollContainer">' +
                        '<div class="UI_scrollContent">' +
                        '<div class="node-type-list">' +
                        '<ul class="faces_list faces_list_hot clearfix" id="hotFace">';
                    for (var i in request['hotFace']) {
                        html += "<li id='hotFaceLi' class='pull-left' class='pull-left' title='" + i + "'><img src='" + request['hotFace'][i] + "'></li>";
                    }
                    html += '</ul>';
                    for (var j in request['faceIcons']) {
                        if (j != 'default') {
                            html += '<div class="node-type-list hide">';
                        }
                        html += '<ul class="faces_list clearfix" id="' + j + '">';
                        for (var k in request['faceIcons'][j]) {
                            html += "<li id='defaultFaceLi'class='pull-left' class='pull-left' title='" + k + "'><img src='" + request['faceIcons'][j][k] + "'></li>";
                        }
                        html += '</ul>' +
                            '</div>';
                    }
                    html += '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $('#layer_faces').html(html);
                }, {},
                'post'
            );
        },
        init: function() {
            var self = this;
            var obj = document.getElementsByTagName('textarea');
            var len = obj.length;
            //点击轮播图，放大
            $('.fancybox').fancybox({
                beforeShow: function () {
                    // set new fancybox width
                    var new_height = $(".fancybox-image").innerHeight();
                    if (this.height < 400) {
                        new_height = 400;
                    }
                    // apply new size to img
                    $(".fancybox-image").css({
                        "width": "auto",
                        "height": new_height
                    });
                    // set new values for parent container
                    this.width = $(".fancybox-image").innerWidth();
                    this.height = new_height;
                },
                type: 'image',
                prevEffect: 'none',
                nextEffect: 'none'
            });
            //获取@的评论人
            self.getAtWho('content');

            for (var i = 0; i < len; i++) {
                self.changeSize(obj[i]);
            }
            if (document.URL.indexOf('#comment-list') != -1) {
                location.href = "#comment-list";
            }
            //轮播
            var l = $('.img-list li').length;
            var w = $('.img-list li').outerWidth(true);
            var n = 0;
            $('.img-list').width(l * w);
            $('.good-img a:first').on('click', function() {
                n--;
                if (n < 0) {
                    n = l - 1;
                }
                $('.img-list').css('left', -n * w);
            });
            $('.good-img a:last').on('click', function() {
                n++;
                if (n >= l) {
                    n = 0;
                }
                $('.img-list').css('left', -n * w);
            });
            if ($('.face-btn').length > 0) {
                $('.W_layer').css('top', $('.face-btn').position().top);
            }
            $('.face-btn').on('click', function(e) {
                if ($('#layer_faces').html() == '') {
                    self.getFaceIcon();
                }
                var e = e || event;
                e.stopPropagation();
                $('.face-btn').removeClass('open');
                $(this).addClass('open');
                self.have($('.W_layer'));
            });
            $(document).on('click', function(e) {
                var e = e || event;
                self.noHave($('.W_layer'));
            })
            $('.W_layer').on('click', function(e) {
                var e = e || event;
                e.stopPropagation();
            });
            $('.W_layer_close').on('click', function(e) {
                var e = e || event;
                e.stopPropagation();
                self.noHave($('.W_layer'));
            });
            //如果原来不存在的元素，要通过已经存在的父一级元素去找
            $('#layer_faces').on('click', '.minitb_ul li', function() {
                var index = $(this).index();
                $(this).addClass('current').siblings().removeClass('current');
                $('.node-type-list').eq(index).show().siblings().hide();
            });
            $('#layer_faces').on('click', '.faces_list li', function() {
                var vale = $('.open').parent().prev().val();
                var title = $(this).attr('title');
                $('.open').parent().prev().val(vale + '[' + title + ']');
            });

            if($('#iconArea').length) {
                var tooltip = $('#like_user_tooltip');
                $('#iconArea').width(detail_params.pageCount * detail_params.width).on('mouseover', '.like-user-thumbnail', function() {
                    var w1 = tooltip.width() / 2;
                    var w2 = $(this).parent().position().left + $(this).width() / 2;
                    var h = $(this).parent().position().top + $(this).height();
                    var img = $(this).find('img').attr('src');
                    var username = decodeURIComponent($(this).attr('_username'));
                    tooltip.css({
                        'left': w2 - w1,
                        'top': h
                    }).find('img').attr('src', img).end()
                    .find('p').html(username).end()
                    .show();
                }).on('mouseout', '.like-user-thumbnail', function() {
                    tooltip.hide();
                });
                tooltip.on('mouseover', function(){
                    $(this).show();
                }).on('mouseout', function(){
                    $(this).hide();
                });
            }

            $('.btn-lr span').eq(0).click(function() {
                detail_params.i--;
                self.btnPlay($('.btn-lr span').eq(0), $('.btn-lr span').eq(1), true);
            });
            $('.btn-lr span').eq(1).click(function() {
                detail_params.i++;
                self.btnPlay($('.btn-lr span').eq(0), $('.btn-lr span').eq(1), false);
            });
            $(window).resize(function() {
                $('.good-wrap ul li').width($('.good').width())
                var len = $('.good-wrap ul li').length;
                var width = $('.good-wrap ul li').width();
                $('.good-wrap ul').width(len * width);
            })
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
            $('#comment_wrap').on('click', '.reply', function() {
                var _this = $(this);
                var h = 40;
                if ($(this).next().next().hasClass('btn-say')) return;
                $("#replyDiv").remove();
                var _id = $(this).attr("_id");
                $(this).next().after('<div id="replyDiv" _attr=""><textarea default_height="23" max_height="70" class="replyContent" name="replyContent" id="replyContent" _id="' + _id + '" cols="30" rows="10"></textarea><div style="max-width:464px;" class="clearfix"><a href="javascript:;" class="face-ico face-btn" style="margin-top:10px;"><em></em>表情</a><a href="javascript:;" id="replyCanncel" class="btn-canncel pull-right" _index="0" style="margin-top:10px;">取消</a><a href="javascript:;" id= "replySubmit" class="btn-primary btn-say pull-right sweet-2" _index="1" style="margin-top:10px;">发表回复</a></div></div>');
                self.getAtWho('replyContent');
                $("#replyDiv textarea").on('propertychange', function() {
                    self.changeSize(this);
                });
                $("#replyDiv textarea").on('input', function() {
                    self.changeSize(this);
                });
                $('.btn-say[_index=0]').comment();
                $('#replyCanncel').click(function() {
                    $('.reply').hide();
                    $("#replyDiv").remove();
                });
                $('.face-btn').click(function(e) {
                    if ($('#layer_faces').html() == '') {
                        //$('#layer_faces').html('<img src="/img/loading.gif"/>').show();
                        self.getFaceIcon();
                    }
                    var e = e || event;
                    var that = $(this);
                    e.stopPropagation();
                    $('.face-btn').removeClass('open');
                    $(this).addClass('open');
                    have($('.W_layer'), that);
                });

                function have(obj, that) {
                    obj.css({
                        'left': that.position().left,
                        'top': that.position().top + h,
                        'display': 'block'
                    })
                    obj.animate({
                        'opacity': 1
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
                            var commentCount = +$('#commentCount').html();
                            $('#commentCount').html(commentCount-delete_num);
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
                    window.appgrubAjax.request(
                        "/produce/comment", function(data) {
                            var html = '';
                            html += '<div class="comment clearfix" id="comment_' + data.id + '">' +
                                '<div class="user-info comment-line" id="userInfo-' + data.id + '">' +
                                '<div class="good-people">' +
                                '<a href="/user/myzone?memberid=' + data.authorID + '" class="img user-thumbnail" target="_blank" _username="'+encodeURIComponent(data.username)+'">' +
                                '<img src="' + data.authorIcon + '" class="img-circle"/>' +
                                '</a>' +
                                '</div>' +
                                '<a class="aTagWrapName" title="' + data.username + '" href="/user/myzone?memberid=' + data.authorID + '" >' + data.username + '</a>&nbsp;<small> 刚刚</small>' +
                                '<a href="javascript:;" class="delete" _id="' + data.id + '">删除</a>' +
                                '<div class="user-content clearfix" id="replyNode-' + data.id + '">' +
                                '<p>' + data.content +
                                '</p>' +
                                '</div>' +
                                '</div>';
                            $('#comment_wrap').prepend(html);
                            var commentCount = $('#commentCount').html();
                            $('#commentCount').html(++commentCount);
                            $('#content').val('');
                        },{
                            'content': content,
                            'appID': detail_params.appID
                        }, 'post');
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

            $('body').on('click', '#replySubmit',function() {
                var _id = $(this).parents('.comment-line').find('.reply').attr('_id');
                var content = $('#replyContent').val().trim();
                if (content != '') {
                    window.appgrubAjax.request(
                        "/produce/comment", function(data) {
                            var html = '<li id="comment_' + data.id + '">';
                            html += '<div class="comment-comment comment-line">' +
                            '<div class="good-people">' +
                            '<a href="/user/myzone?memberid=' + data.authorID + '" class="img user-thumbnail" target="_blank" _username="'+encodeURIComponent(data.username)+'">' +
                            '<img src="' + data.authorIcon + '" class="img-circle"/>' +
                            '</a>' +
                            '</div>' +
                            '<a class="aTagWrapName" title="' + data.username + '" href="/user/myzone?memberid=' + data.authorID + '">' + data.username + '</a> 回复了 <a href="/user/myzone?memberid=' + data.toAuthorID + '" class="aTagWrapName" target="_blank" id="aTag" title="' + data.toAuthorUserName + '" >' + data.toAuthorUserName + '</a>:&nbsp;<small class="reply-time"> 刚刚</small>' +
                            '<a href="javascript:;" class="delete" _id="' + data.id + '">删除</a>' +
                            '<div class="user-content clearfix">' +
                            '<p>' + data.content + '</p>';
                            html += '</div></div></li>';
                            $('#userInfo-' + data.pid).next().append(html);
                            var commentCount = $('#commentCount').html();
                            $('#commentCount').html(++commentCount);
                            $("#replyDiv").remove();
                        }, {
                            'content': content,
                            'appID': detail_params.appID,
                            'replayId': _id
                        }, 'post');
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

            //分享到新浪微博
            $('#share_weibo').on('click', function() {
                var img_obj = $('.soft-img').find('img').eq(0);
                var jiathis_config = {
                    webid: 'tsina',
                    url: window.location.href,
                    title: encodeURIComponent('#App哥伦部#'),
                    summary: encodeURIComponent('我在App哥伦部网站上发现了一个很好玩的App《' + $('#app_name').text() + '》，你们也来看看吧！'),
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
        }
    };
    //product_detail.init();
    window.onload = function() {
        product_detail.init();
    }
});