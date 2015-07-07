<style>
@media(max-width:580px){
    .info-left{width:224px;margin:0 auto;}
    .info-right{width:224px;margin:0 auto;}
    .info-img{text-align:center;}
}
@media(min-width:580px){
    .info-left{float:left}
    .info-right{border-left:1px solid #ccc;padding-left:30px;float:right;}
}
</style>
</head>
<body>
    <div id="container">
        <!--<div class="login-content">-->
            <div class="content row">
            <?php if(YII_DEBUG){?>
            
            <div class="info-left">
                    <input type="text" id = "username" name="username" placeholder="邮箱" class="info" required="required" aria-required="true"><br>
                    <input type="password" id = "password" name="password" placeholder="密码" class="info" required="required" aria-required="true"><br>
                    <button id="submit" class="btn-blue sweet-9">登录</button>
                    <br><br>
                    <hr style="border:1px solid #ccc">

                    <p><a href="#">注册</a></p>
                    <p><a href="#">忘记密码?</a></p>
                    <p><a href="#">没有收到验证邮件?</a></p>
                </div>
                <div class="info-right">
                    <h5 class="text-center"><strong>微信扫一扫：立即登录、快速注册～</strong></h5>
                    <div class="info-img">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/code.jpg" alt="二维码"/>
                    </div>
                    <div class="info-content">
                        <small>1.微信授权仅用于获取昵称和头像信息</small><br>
                    <small>2.定制关注和订阅，个性化内容和动态提醒</small><br>
                    <small>3.定向推送最新产品信息，还有更多…</small><br>
                    </div>
                </div>
    <?php } else {?>
    
                <div class="text-center">
                    <h5 class="text-center"><strong>微信扫一扫：立即登录、快速注册～</strong></h5>
                    <div class="info-img">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/user/qrcode" alt="二维码" width=200 height=200 />
                    </div>
                    <div class="info-content" style="text-align:center;">
                    <div style="width:250px;text-align:left;margin:auto;padding-left:50px">
                    <small>1.微信授权仅用于获取昵称和头像信息</small><br>
                    <small>2.定制关注和订阅，个性化内容和动态提醒</small><br>
                    <small>3.定向推送最新产品信息，还有更多…</small><br>
                    </div>
                    </div>
                </div>
    
    <?php } ?>
                
                </div>
        <!--</div>-->
    </div>
    
</html>
<script>
    $("#submit").click(function() {
        var username = $("#username").val();
        var password = $("#password").val();
        if (username != '') {
            
            if (password != '') {
                $.ajax({
                    type:"POST",
                    url:"<?php echo $this->createUrl('User/CheckLogin');?>",
                    data:{ 'username' : username,'password' : password },
                    dataType:'json',
                    success:function(response) {
                        //alert(response.ret_code );
//                        alert(response);
                        //var res = eval("(" + response +")");
                        if (response.ret_code == 0) {
                            console.log(response.ret_msg);
                            window.location.href="/";
                        } else {
                            //console.log(response.ret_msg);
                            swal({
                              title: response.ret_msg,
                              type: "info",
                              showCancelButton: false,
                              confirmButtonClass: 'btn-info',
                              confirmButtonText: '确定'
                            });
                        }
                    }
                });
            } else {
                //alert('密码不能为空');
                swal({
                  title: '密码不能为空',
                  type: "warning",
                  showCancelButton: false,
                  confirmButtonClass: 'btn-warning',
                  confirmButtonText: '确定'
                });
            }
        } else {
            //alert('用户名不能为空');
            swal({
              title: '用户名不能为空',
              type: "warning",
              showCancelButton: false,
              confirmButtonClass: 'btn-warning',
              confirmButtonText: '确定'
            });
        }
    });
</script>