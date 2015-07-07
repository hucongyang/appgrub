$(document).ready(function() {
    var app_list = {
        conditions : {
            os: '',
            app_name : '',
            main_category : '',
            subcategory : '',
            limit : '',
            offset : ''
        },
        url : {
            app_list_url: '/admin/app/appinfolist'
        },
        dom : {
            grid_tbody: $('#grid').find('tbody'),
            grid_loading: $('#grid_loading')
        },
        flag : {
            pagination_times : 0
        },
        category : [],
        get_search_condition : function() {
            return this.conditions;
        },
        fetch_result : function(list) {
            var html = '';
            $.each(list, function(i, o) {
                html += '<tr>';
                html += '<td align="center"><a href="/produce/index?id=' + o['Id'] + '" target="_blank"><img src="' + o['IconUrl'] + '" align="absmiddle" width="36" height="36"></a></td>';
                html += '<td>' + o['AppName'] + '</td>';
                html += '<td _category="' + o['Id'] + '">' + o['mainCategory'] + '</td>';
                html += '<td _subcategory="' + o['Id'] + '">' + o['subcategory'] + '</td>';
                html += '<td align="center">' + o['UpdateTime'] + '</td>';
                html += '<td>' + o['OS'] + '</td>';
                html += '<td>' +
                            '<select class="category-main"><option value="0">请选择</option></select>' +
                            '<select class="category-sub"><option value="0">请选择</option></select>' +
                        '</td>';
                html += '<td><a class="btn btn-success category_alert" href="javascript:;" _id="' + o['Id'] + '"><i class="icon-file icon-white"></i>修改</a></td>';
                html += '</tr>';
            });
            return html;
        },
        request : function(limit, offset) {
            var _self = this;
            _self.dom.grid_tbody.html('');
            _self.dom.grid_loading.html('<img src="/img/loading.gif"/>').show();
            _self.conditions.limit = limit;
            _self.conditions.offset = offset;
            var search_condition = _self.get_search_condition();
            window.appgrubAjax.request(
                _self.url.app_list_url,
                function(data) {
                    _self.dom.grid_loading.hide();
                    if (data.length) {
                        var tr_html = _self.fetch_result(data);
                        _self.dom.grid_tbody.html(tr_html);
                    } else {
                        _self.dom.grid_loading.html('<div class="alert">无查询结果</div>').show();
                    }
                },
                search_condition,
                'post',
                function() {
                    _self.dom.grid_loading.hide().html('');
                }
            );
        },
        request_category : function () {
            var _self = this;
            window.appgrubAjax.request(
                '/admin/app/getmaincategory',
                function(data) {
                    var html = '';
                    $.each(data, function(i, o) {
                        html += '<option value="'+i+'">';
                        html += o;
                        html += '</option>';
                    });
                    if (_self.flag.pagination_times == 0) {
                        $('.edit_category').find('.category-main').append(html);
                    } else {
                        $('.table').find('.category-main').append(html);
                    }
                },
                {},
                'post'
            );
        },
        request_get_pages : function () {
            var _self = this;
            var search_condition = _self.get_search_condition();
            window.appgrubAjax.request(
                '/admin/app/gettotalpage',
                function (request) {
                    var total = Math.ceil(request / 10);
                    $('.pagination').bootpag({
                        total: total,
                        page: 1,
                        maxVisible: 10
                    }).on('page', function(event, num){
                        var start = (num - 1) * 10;
                        _self.flag.pagination_times++;
                        _self.request(10 , start);
                        _self.request_category();
                    });
                },
                search_condition,
                'post'
            );
        },
        init: function() {
            var _self = this;
            _self.request(10, 0);
            _self.request_category();
            $('.icheck-radio').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            }).on('ifChecked', function(event) {
                _self.conditions[$(this).attr('name')] = $(this).val();
            });
            $('.edit_category').on('change', '.category-main', function() {
                var $this = $(this);
                var main_category = $this.find('option:selected').val();
                if (main_category != 0) {
                    window.appgrubAjax.request(
                        '/admin/app/getsubcategory',
                        function (request) {
                            var subCategoryHtml = '<option value="0">请选择</option>';
                            $.each(request, function(i, o) {
                                subCategoryHtml += '<option value="'+ o.ID+'">';
                                subCategoryHtml += o.Name;
                                subCategoryHtml += '</option>';
                            });
                            $this.next().empty().append(subCategoryHtml);
                        },
                        {main_category : main_category},
                        'post'
                    );
                } else {
                    $this.next().empty().append('<option value="0">请选择</option>');
                }
            });
            $('.table').on('click', '.category_alert', function () {
                var get_prev = $(this).parent().prev();
                var get_main_category_option = get_prev.find('.category-main').find('option:selected');
                var get_sub_category_option = get_prev.find('.category-sub').find('option:selected');
                var main_category  = get_main_category_option.val();
                var main_category_name  = get_main_category_option.text();
                var subcategory = get_sub_category_option.val();
                var subcategory_name = get_sub_category_option.text();
                var appID = $(this).attr('_id');
                if (main_category && subcategory) {
                    window.appgrubAjax.request(
                        '/admin/app/alertappinfolistcategory',
                        function (request) {
                            if (request == 1) {
                                $('.table').find("td[_category="+appID+"]").html(main_category_name);
                                $('.table').find("td[_subcategory="+appID+"]").html(subcategory_name);
                                swal({
                                    title: '修改成功',
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonClass: 'btn-info',
                                    confirmButtonText: '确定'
                                });
                            }
                        },
                        {main_category : main_category, subcategory: subcategory, appID : appID},
                        'post'
                    );
                }
            });
            _self.request_get_pages();
            $('#search').on('click', function () {
                _self.flag.pagination_times++;
                _self.conditions.app_name = $('#app_name').val().trim();
                _self.conditions.main_category = $('#search_main_category').find('option:selected').val();
                _self.conditions.subcategory =  $('#search_subcategory').find('option:selected').val();
                _self.request(10, 0);
                _self.request_get_pages();
                _self.request_category();
            });
        }
    }
    app_list.init();
});