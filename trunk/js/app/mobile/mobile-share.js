$(document).ready(function() {
    var mobile_share_params = {
        app_url_array : []
    };
    var mobile_share = {
        emptyForm : function () {
            $('#appUrl').val('');
            $('#url').val('');
            $('#detail').val('');
            $('#checkUrl').text('');
        },
        init: function() {
            $('#submitCancel').on('click', function () {
                window.location.href = '/';
            });
            $('#appUrl').on('blur', function () {
                var share_url = $('#appUrl').val().trim();
                if (share_url == '') {
                    $('#checkUrl').text('请输入App链接');
                    return;
                }
                var parser = document.createElement('a');
                parser.href = share_url;
                if (mobile_share_params.app_url_array.length == 0) {
                    window.appgrubAjax.request("/share/getsource", function (request) {
                        mobile_share_params.app_url_array = request;
                        if ($.inArray(parser.host, mobile_share_params.app_url_array) < 0) {
                            $('#checkUrl').text('App链接有误,请参考填写规则');
                        } else {
                            $('#checkUrl').text('');
                        }
                    }, {}, 'post');
                } else {
                    if ($.inArray(parser.host, mobile_share_params.app_url_array) < 0) {
                        $('#checkUrl').text('App链接有误,请参考填写规则');
                    } else {
                        $('#checkUrl').text('');
                    }
                }
            });
            $('#shareSubmit').on('click', function () {
                var url = $('#url').val().trim();
                var appUrl = $('#appUrl').val().trim();
                var explain = $('#detail').val().trim();
                if (appUrl == '') {
                    $('#checkUrl').text('请输入App链接');
                    return;
                } else if (mobile_share_params.app_url_array.length > 0) {
                    var parser = document.createElement('a');
                    parser.href = appUrl;
                    if ($.inArray(parser.host, mobile_share_params.app_url_array) < 0) {
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
        }
    };
    mobile_share.init();
});
