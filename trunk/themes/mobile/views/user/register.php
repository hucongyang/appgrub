<div class="row-fluid">
	<div class="span11 pull-right">
		
        <form action="" method="post">
    		<fieldset>
    		<legend>注册用户</legend>
            
            <label for="username">用户名</label>
    		<input id="reg_username" type="text" name="username" placeholder="输入用户名" class="sweet-10"/>&nbsp;&nbsp;<span id="chk_msg"></span><br />
    		
    		<label for="email">邮箱（可不填）</label>
    		<input type="email" name="email" placeholder="输入邮箱"/><br />
            <!--<span class="help-block">我们不会主动给你发送邮件。</span>-->
    		
    		<label for="password">密码</label>
    		<input type="password" name="password" placeholder="输入密码"/><br />
    		
            <label for="passconf">确认密码</label>
    		<input type="password" name="passconf" placeholder="再次输入密码"/><br />
    		<div style="color:red;"></div>
    		
            <div class="control-group">
			    <?php $this->widget('CCaptcha',array(
			        'showRefreshButton'=>true,
			        'clickableImage'=>true,
			        'buttonLabel'=>'刷新验证码',
			        'imageOptions'=>array(
			            'alt'=>'点击换图',
			            'title'=>'点击换图',
			            'style'=>'cursor:pointer',
			            'padding'=>'10')
			        )); ?>
			</div>
            <label for="captcha">验证码</label>
            <input type="text" name="captcha" placeholder="输入上图中的4个字符"/>
            <div style="color:red;"></div>
            
    		<button class="btn btn-primary" id="save" type="submit" name="submit" >提交</button>

            <!--错误提示<span id="msg"></span>-->

    		</fieldset>
    		
        </form>
	</div>
</div>

<script type="text/javascript">
    $('#save').click(function(){
        var username = $('#username').val();
        if(username.length <6 || username.length >16){
            alter('密码长度不能少于6或大于16');
            return false;
        }
        var username = $('#username').val();
        if(username.length <6 || username.length >16){
            alter('密码长度不能少于6或大于16');
            return false;
        }
    })

</script>

<script type="text/javascript">
   $(document).ready(function(){
        $("#reg_username").keyup(function(){
            var username = $(this).val();
            //alert(username);
            $.ajax({
                type:"POST",
                url:"<?php echo $this->createUrl('user/chk_username');?>",
                data:{'username':username},
                error:function(){
                    //alert("error");
                    swal({
                      title: "error",
                      type: "warning",
                      showCancelButton: true,
                      confirmButtonClass: 'btn-warning',
                      confirmButtonText: '确定'
                    });
                },
                success:function(msg){
                    $("#chk_msg").html(msg);
                }
            });
        });
   });
</script>
