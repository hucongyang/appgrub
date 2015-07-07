$(document).ready(function() {
    var user_info_edit = {
        condition: {
            username: $('#hidden_username').val() ? $('#hidden_username').val().trim() : $('#hidden_username').val(),
            email: $('#hidden_email').val() ? $('#hidden_email').val().trim() : $('#hidden_email').val()
        },
        check_username: function(input_username){
            var len = input_username.length;
            if(input_username == '') {
                $('#username_group').removeClass('success').addClass('error');
                $("#username_info").html("昵称不能为空");
                return false;
            }else if (len>255) {
                $('#username_group').removeClass('success').addClass('error');
                $("#username_info").html("昵称长度过长");
                return false;
            }
            return true;
        },
        check_email: function(input_email){
            if(input_email == '') {
                $('#email_group').removeClass('success error');
                $("#email_info").html("&nbsp;");
                return true;
            }
            var fullPattern =  /^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i;
            if(fullPattern.test(input_email) == false) {
                $('#email_group').removeClass('success').addClass('error');
                $("#email_info").html("邮箱格式不正确");
                return false;
            }
            return true;
        },
        init: function() {
            var _self = this;
            $('#edit_user_info').on('click', function(){
                $('#username_group').removeClass('success error');
                $('#email_group').removeClass('success error');
                $('#input_username').val(_self.condition.username);
                $('#input_email').val(_self.condition.email);
                $('#username_info').removeClass('success error').html('&nbsp;');
                $('#email_info').removeClass('success error').html('&nbsp;');
                $('#user_info_modal').modal('show');
            });
            $("#input_username").blur(function () {
                var input_username = $("#input_username").val().trim();
                if(_self.check_username(input_username)) {
                    window.appgrubAjax.request(
                        '/user/checkusername',
                        function(data){
                            if(data.code < 0){
                                $('#username_group').removeClass('success').addClass('error');
                            } else {
                                $('#username_group').removeClass('error').addClass('success');
                            }
                            $("#username_info").html(data.msg);
                        },
                        {username: input_username},
                        'post'
                    );
                }
            });
            $('#input_email').on('blur', function(){
                var input_email = $("#input_email").val().trim();
                if( input_email !== '' && _self.check_email(input_email)) {
                    window.appgrubAjax.request(
                        '/user/checkemail',
                        function(data){
                            if(data.code < 0){
                                $('#email_group').removeClass('success').addClass('error');
                            } else {
                                $('#email_group').removeClass('error').addClass('success');
                            }
                            $("#email_info").html(data.msg);
                        },
                        {email: input_email},
                        'post'
                    );
                }
            });
            $('#save').on('click', function(){
                var input_username = $("#input_username").val().trim();
                var input_email = $("#input_email").val().trim();
                if(!_self.check_username(input_username) || !_self.check_email(input_email)){
                    return false;
                }
                window.appgrubAjax.request(
                    '/user/save',
                    function(data){
                        if(data.code < 0){
                            $('#'+data.type+'_group').removeClass('success').addClass('error');
                            $('#'+data.type+'_info').html(data.msg);
                        } else {
                            _self.condition.username = input_username;
                            _self.condition.email = input_email;
                            $('#username').text(input_username);
                            $('#user_info_modal').modal('hide');
                            swal({
                                title: data.msg,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonClass: 'btn-primary',
                                confirmButtonText: '确定'
                            })
                        }
                    },
                    {username: input_username, email: input_email},
                    'post'
                );
            });
        }
    };
    user_info_edit.init();
});
