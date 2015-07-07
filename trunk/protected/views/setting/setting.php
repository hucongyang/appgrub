<style>
@media(max-width:580px){
    .info-left{width:224px;margin:0 auto;}
    .info-right{width:224px;margin:0 auto;}
    .info-img{text-align:center;}
    #header h2:nth-child(2){display:block;}
    #header h2:nth-child(1) .wei:nth-child(1),#header h2:nth-child(1) .wei:nth-child(3){display:none;}
}
@media(min-width:580px){
    .info-left{float:left}
    .info-right{border-left:1px solid #ccc;padding-left:30px;float:right;}
}
</style>

    <div id="container">
        <!--<div class="login-content">-->
            <div class="content row">
                <div class="info-left">
                    <h4>修改个人信息</h4><br>
              <form action="" method="post">
                    <div>
                        <label for="user-name">昵称</label>
                        <input type="text" class="info" id="username" name="username">
                    </div>
                    <div>
                        <label for="user-upload" class="file-upload">头像<br>
                        <input type="file" class="info" id="user-upload" name="userurl"/>
                       </label>
                    </div><br>
                    <div>
                        <label for="user-dialog">一句话简介</label>
                        <textarea id="user-dialog" cols="30" rows="10" name="userdialog"></textarea>
                    </div><br>
                    <div>
                        <label for="">组织</label>
                        <input type="text" id="" class="info"/>
                    </div>
                    <div>
                        <label for="">职位</label>
                        <input type="text" id="" class="info"/>
                    </div>
                    <button class="btn-blue" onclick="update_Information">更新设置</button>
                 </form>  
                    <br><br>
                    <h4>订阅</h4><br>
                    <p>
                        <span>频率：</span>&nbsp;&nbsp;
                        
                        <label for="no-send" class="send">
                            <input type="radio" id="no-send" name="send"/>不发送
                        </label>&nbsp;&nbsp;
                        
                        <label for="day-send" class="send">
                            <input type="radio" id="day-send" name="send"/>日报
                        </label>&nbsp;&nbsp;
                        
                        <label for="week-send" class="send">
                            <input type="radio" id="week-send" name="send"/>周报
                        </label>
                    </p>
                    <button class="btn-blue">保存设置</button>
                    <br><br>
                    <h4>修改密码</h4>
                    <br>
                    <div>
                        <label for="user-email">邮箱</label>
                        <input type="email" class="info" id="user-email">
                    </div>
                    <div>
                        <label for="user-pass">密码</label>
                        <input type="password" class="info" id="user-pass"><br>
                        <span>不更改密码请留空</span>
                    </div>
                    <br><br>
                    <div>
                        <label for="user-passOk">确认密码</label>
                        <input type="password" class="info" id="user-passOk">
                    </div>
                    <div>
                        <label for="user-passOld">当前密码</label>
                        <input type="password" class="info" id="user-passOld"><br>
                        <span>更新以上信息，请输入密码</span>
                    </div><br>
                    <button class="btn-blue">修改密码</button>
                </div>
                <div class="info-right">
                    <h5 class="text-center"><strong>微信扫一扫：绑定微信，体验更多个<br>性化功能～</strong></h5>
                    <div class="info-img">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/code.jpg" alt="二维码"/>
                    </div>
                    <div class="info-content">
                        <small>1.微信授权仅用于获取昵称和头像信息</small><br>
                        <small>2.定制关注和订阅，个性化内容和动态提醒</small><br>
                        <small>3.活动定向推送的第一手信息，还有更多…</small><br>
                    </div>
               
                </div>
            </div>
        <!--</div>-->
    </div>
    
<script>
	function update_Information(id){
		$.ajax({
			url:"<?php echo $this->createUrl('setting/add');?>",
			datatype:"json",
			success:function(response){
				//var res = eval("(" + response +")");

			}
		)};
	}
			
</script>
</html> 