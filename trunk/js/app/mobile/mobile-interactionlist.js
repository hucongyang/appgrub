$(function(){
    var member_id = $('#memberID').val();
    var mobile_interaction_list = {
        interaction: function (type, pane_obj) {
            pane_obj.html('<div style="text-align: center;margin-top: 10px;"><img src="/img/loading.gif"/></div>');
            window.appgrubAjax.request(
                '/user/mobileinteraction',
                function (request) {
                    pane_obj.html('');
                    var html = '<div class="app-list">';
                    if (request.length == 0) {
                        var msg = type == 1 ? '还没有点过赞哦' : '还没有评论过哦';
                        html += '<div class="alert alert-info no-content">' + msg +'</div>';
                    } else {
                        for (var i = 0;i < request.length; i++) {
                            var likeClass = request[i].isUpped ? 'link' : '';
                            var osType = 'd-type-'+request[i].OS.toLowerCase();
                            var summary = request[i].Remarks ? request[i].Remarks : request[i].AppInfo;
                            html += '<dd class="list clearfix">' +
                                        '<div class="content-list clearfix row">' +
                                            '<div class="col-md-8 clearfix">'+
                                                '<div class="top pull-left">' +
                                                    '<a href="javascript:;" class="isLiked ' + likeClass + '" _id="'+request[i].Id+'">' +
                                                    '<span class="arrow"></span>' +
                                                    '<span class="like-num">'+request[i].count+'</span>' +
                                                    '</a>'+
                                                '</div>' +//top pull-left
                                                '<div class="detail pull-left">'+
                                                    '<div class="d-img">'+
                                                        '<a href="javascript:;" class="dimg">'+
                                                            '<img src="'+request[i].IconUrl+'" class="img-circle img-radius"/>'+
                                                        '</a>'+
                                                    '</div>'+
                                                    '<div class="limit clearfix">'+
                                                        '<a href="/produce/index/'+request[i].Id+'" target="_blank" class="title">'+request[i].AppName+'</a>'+
                                                        '<div class="say pull-right">'+
                                                            '<i class="'+osType+'"></i>'+
                                                        '</div>'+
                                                    '</div>'+
                                                    '<div class="limit-auto clearfix">'+
                                                        '<p>'+summary+'</p>'+
                                                    '</div>'+
                                                    '<div class="shareDateMobile clearfix">'+
                                                        '<div class="pull-right">'+request[i].CommitTime+'</div>'+
                                                        '<div class="say pull-right" style="margin-right:10px;">'+ request[i].CommentCount+
                                                            '<i></i>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        '<a class="content-link" target="_blank" href="/produce/index/'+request[i].Id+'"></a>'+
                                    '</dd>';
                        }
                        html += '</div>';//likedApp
                    }
                    pane_obj.html(html);
                },
                {'memberid' : member_id, 'type' : type},
                'post'
            );
        },
        init: function(){
            var self = this;
            $('#interaction_change').on('shown', 'a[data-toggle="tab"]', function (e) {
                var type = $(this).attr('_type');
                var pane_obj = $('.app-list-pane[_type="'+type+'"]');
                if(pane_obj.html() === '') {
                    self.interaction(type, pane_obj);
                }
            });
            $('#interaction_change').find('a[_type="1"]').tab('show');
        }
    };
    mobile_interaction_list.init();
});
