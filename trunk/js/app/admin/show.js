$(document).ready(function() {

    var filter_app = {
        conditions: {
            os: '',
            order: 'DownLoadNum'
        },
        dom: {
            grid_tbody: $('#grid').find('tbody'),
            grid_loading: $('#grid_loading')
        },
        url: {
            show_url: '/admin/app/list',
            add_url: '/admin/app/add',
            delete_url: '/admin/app/delete'
        },
        get_search_conditon: function() {
            return this.conditions;
        },
        fetch_result: function(list) {
            var html = '';
            $.each(list, function(i, o) {
                html += '<tr _PushId="' + o.PushId + '">';
                html += '<td align="center"><input class="tbody-icheck-checkbox" name="PushId" type="checkbox" value="' + o.PushId + '"></td>';
                html += '<td align="center">' + o.PushId + '</td>';
                html += '<td align="center"><a href="/admin/app/detail?id=' + o.PushId + '" target="_blank"><img src="' + o.IconUrl + '" align="absmiddle" width="36" height="36"></a></td>';
                html += '<td>' + o.AppName + '</td>';
                html += '<td>' + o.ChnName + '</td>';
                html += '<td>' + o.MainCategory + '</td>';
                html += '<td>' + o.FileSize + '</td>';
                html += '<td>' + o.OS + '</td>';
                html += '<td align="center">' + o.ProcessDate + '</td>';
                html += '<td align="right">' + o.DownLoadNum + '</td>';
                html += '<td align="right">' + o.CommentNum + '</td>';
                html += '</tr>';
            });
            return html;
        },
        request: function() {
            var _self = this;
            var search_condition = _self.get_search_conditon();
            _self.dom.grid_tbody.html('');
            _self.dom.grid_loading.html('<img src="/img/loading.gif"/>').show();
            window.appgrubAjax.request(_self.url.show_url, function(data) {
                _self.dom.grid_loading.hide();
                if (data.length) {
                    var tr_html = _self.fetch_result(data);
                    _self.dom.grid_tbody.html(tr_html);
                    $('.tbody-icheck-checkbox').iCheck({
                        checkboxClass: 'icheckbox_flat-blue',
                        radioClass: 'iradio_flat-blue'
                    }).on('ifChecked', function(event) {
                        $(this).attr('checked', true);
                    });
                } else {
                    _self.dom.grid_loading.html('<div class="alert">无查询结果</div>').show();
                }
            }, search_condition, 'post', function() {
                _self.dom.grid_loading.hide().html('');
            });
        },
        init: function() {
            var _self = this;
            $('.icheck-radio').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            }).on('ifChecked', function(event) {
                _self.conditions[$(this).attr('name')] = $(this).val();
            });

            $('#search').click(function() {
                _self.request();
            })

            $("#delete").click(function() {
                var id = [];
                $('.tbody-icheck-checkbox:checked').each(function() {
                    id.push($(this).val());
                });
                if (id.length) {
                    window.appgrubAjax.request(_self.url.delete_url, function(data) {
                        swal({
                            title: data,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonClass: 'btn-info',
                            confirmButtonText: '确定'
                        });
                        $.each(id, function(i, o) {
                            _self.dom.grid_tbody.find('tr[_PushId="' + o + '"]').remove();
                        });
                    }, {
                        id: id
                    }, 'post');
                } else {
                    swal({
                        type: 'error',
                        title: "勾选项不能为空",
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: '确定'
                    });
                }
            });

            $("#add").click(function() {
                var id = [];
                $('.tbody-icheck-checkbox:checked').each(function() {
                    id.push($(this).val());
                });
                if (id.length) {
                    window.appgrubAjax.request(_self.url.add_url, function(data) {
                        swal({
                            title: data,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonClass: 'btn-info',
                            confirmButtonText: '确定'
                        });
                        $.each(id, function(i, o) {
                            _self.dom.grid_tbody.find('tr[_PushId="' + o + '"]').remove();
                        });
                    }, {
                        id: id
                    }, 'post');
                } else {
                    swal({
                        type: 'error',
                        title: "勾选项不能为空",
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: '确定'
                    });
                }
            });


            _self.request();

        }
    }
    filter_app.init();

});