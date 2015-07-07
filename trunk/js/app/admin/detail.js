$(function() {
    var app_detail = {
        conditions: {
            id: 0
        },
        init: function() {
            var _self = this;

            $('.comment').on('click', function() {
                $(this).toggleClass('comment-selected');
            });

            $('.comment-edit').on('click', function(e) {
                var _id = $(this).attr('_id');
                _self.conditions.id = _id;
                $('#comment_content').val($('#comment_' + _id).text());
                $('#modal_comment_edit').modal('show');
                e.stopPropagation();
                e.preventDefault();
            });

            $('.comment-delete').on('click', function(e) {
                var _id = $(this).attr('_id');
                window.appgrubAjax.request('/admin/app/deletecomment', function(data) {
                        swal({
                            title: data,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonClass: 'btn-info',
                            confirmButtonText: '确定'
                        });
                        $('#commentInfo_' + _self.conditions.id).slideUp(500, function() {
                            $(this).remove();
                        });
                        $('#comment_count').text($('#comment_count').text() - 1);
                    }, {
                        id: _id
                    },
                    'post');
                e.stopPropagation();
                e.preventDefault();
            });

            $('#batch_delete').on('click', function() {
                var ids = $('#comment_list .comment-selected').map(function() {
                    return +$(this).attr('_id');
                }).get();
                if (ids.length) {
                    swal({
                        title: "确定要删除评论吗?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        cancelButtonText: "取消",
                        confirmButtonText: "确定",
                        closeOnConfirm: true
                    }, function() {
                        window.appgrubAjax.request('/admin/app/deletecomment', function(data) {
                            swal({
                                title: data,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonClass: 'btn-info',
                                confirmButtonText: '确定'
                            });
                            $('.comment-selected').slideUp(500, function() {
                                $('.comment-selected').remove();
                            });
                            $('#comment_count').text($('#comment_count').text() - ids.length);
                        }, {
                            id: ids
                        }, 'post');
                    });
                }

            });

            $('#save_comment').on('click', function() {
                window.appgrubAjax.request('/admin/app/editcomment', function(data) {
                    $('#modal_comment_edit').modal('hide');
                    swal({
                        title: data,
                        type: "success",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-info',
                        confirmButtonText: '确定'
                    });
                    $('#conment_' + _self.conditions.id).text($('#comment_content').val().trim());
                    $('#comment_content').text('');
                    _self.conditions.id = 0;
                }, {
                    id: _self.conditions.id,
                    content: $('#comment_content').val().trim()
                }, 'post');
            });

            $('.info-edit').on('click', function() {
                var appinfo_modal = new window.appgrubBootstrapModal({
                    id: 'appinfo_modal',
                    title: '编辑介绍',
                    css: {
                        width: '800px',
                        "margin-left": '-400px',
                        top: '20%'
                    },
                    content: '<textarea style="height: 300px; width: 95%" id="appinfo_content"></textarea>',
                    callback: {
                        ok: function() {
                            var app_id = $('#app_id').val();
                            var content = $('#appinfo_content').val().trim();
                            window.appgrubAjax.request('/admin/app/editappinfo', function(data) {
                                swal({
                                    title: data,
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonClass: 'btn-info',
                                    confirmButtonText: '确定'
                                });
                                $('#app_info').text($('#appinfo_content').val().trim());
                                $('#appinfo_content').text('');
                                appinfo_modal.modal('hide');
                            }, {
                                app_id: app_id,
                                content: content
                            }, 'post');
                        }
                    }
                });
                $('#appinfo_content').val($('#app_info').text().trim());
            });
        }
    };
    app_detail.init();
});