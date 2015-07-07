window.appgrubAjax = {
    handle: undefined,
    request: function(url, callback, data, type, onerror, async) {
        var sync = typeof(async) == 'undefined' ? true : async;
        var suc_func = function(data, status) {
            if (data.ret_code < 0) {
                if (onerror) {
                    onerror(data)
                }
            }
            if (data.ret_code < 0) {
                swal({
                    title: data.ret_msg,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: '确定'
                });
                return;
            }
            callback(data.ret_msg, data.ret_code);
        };

        var error_func = function(xhr, status, msg) {
            if (msg.indexOf && msg.indexOf("Invalid JSON") == 0) {
                swal({
                    title: '网络连接超时，请稍后再试。',
                    type: "error",
                    showCancelButton: false,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: '确定'
                });
            } else {
                if (onerror) {
                    onerror(msg)
                }
            }
        }
        handle = $.ajax({
            url: url,
            type: type || 'get',
            dataType: 'json',
            data: data,
            success: suc_func,
            error: error_func,
            async: sync
        });
        return handle;
    },
    abort: function() {
        if (typeof(handle) != 'undefined') {
            handle.abort();
        }
    }
};