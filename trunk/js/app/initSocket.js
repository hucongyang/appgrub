window.nodejs_socket_io = {
    socket: null,
    init: function() {
        var _self = this;
        _self.socket = io('http://appgrub.com:443');
        var join = function() {
            _self.socket.emit('join', {
                qrcode: login_njsid
            });
        };
        join();
        _self.socket.on('message', function(data) {
            if (data.ret_code == 0) {
                $.ajax({
                    type: "POST",
                    url: "/user/checklogin",
                    dataType: 'json',
                    success: function(response) {
                        if (response.ret_code == 0) {
                            _self.socket.emit('disconnect');
                            _self.socket = null;
                            window.location.href = window.location.href;
                        } else {
                            swal({
                                title: response.ret_msg,
                                type: "error",
                                showCancelButton: false,
                                confirmButtonClass: 'btn-danger',
                                confirmButtonText: '确定'
                            });
                        }
                    }
                });
            }
        });
    },
    disconnect: function() {
        var _self = this;
        _self.socket && _self.socket.emit('disconnect');
    }
};