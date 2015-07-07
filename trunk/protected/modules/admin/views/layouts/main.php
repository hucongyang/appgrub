<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
<meta name="description" content="有趣的app,有潜力的app,好玩的app,热门app，app排行,app游戏,app实用工具"  />
<meta name="keywords" content="有趣的app,有潜力的app,好玩的app,热门app，app排行,app游戏,app实用工具,app哥伦部,优秀app"/>
<title>App哥伦部 - App的新大陆</title>
<link rel="shortcut icon" href="/favicon.ico" />
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css" rel="stylesheet" media="screen">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-responsive.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/sweet-alert.css" rel="stylesheet" type="text/css">
<script>
    var not_login_flag = <?php echo (empty(Yii::app()->user->id)) ? 1 : 0; ?>;
</script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery-1.10.1.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.cookie.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/highcharts.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/exporting.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/sweet-alert.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/appgrubAjax.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/lib/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/main.js"></script>
</head>
<body>
    <div class="header navbar">
        <div class="navbar-inner">
            <a class="brand" href="/">
                <span class="logo"></span>
                <span class="sitename">App哥伦部</span>
                <span class="slogan"></span>
            </a>
            <ul class="nav pull-right">
                <li><a href="/">首页</a></li>
                <?php if(empty(Yii::app()->user->id)):?>
                    <?php if(!isset($this->hidden_weixin) || !$this->hidden_weixin):?>
                    <li class="relative login-weixin">
                        <a class="dropdown-hover socket-login" href="<?php echo $this->createUrl('user/login');?>"><i class="icon-weixin"></i>&nbsp;登录</a>
                        <div class="dropdown-box">
                            <img src="<?php echo Yii::app()->request->baseUrl; ?>/user/qrcode" alt="二维码" width="110px">
                            <p>微信扫一扫：登录</p>
                        </div>
                    </li>
                    <?php endif; ?>
                <?php else:?>
                <li id="message" class="relative"><a href="<?php echo $this->createUrl('msg/index');?>">通知</a></li>
                <li class="relative">
                    <a class="dropdown-hover" href="javascript:;">
                        <img src="<?php echo Yii::app()->user->userurl;?>" width="30px" class="img-circle" />
                    </a>
                    <ul class="dropdown-box">
                        <li><a href="/user/myzone">我的主页</a></li>
                        <li><a href="/user/myfavorite">我的收藏</a></li>
                        <li><a href="<?php echo $this->createUrl('user/logout');?>">退出登录</a></li>
                    </ul>
                </li>
                <?php endif;?>
            </ul>
        </div>
    </div>
<?php echo $content; ?>
<footer id="footer">
    <p class="text-center">
        版权所有 智云众 京ICP备14026918号-4<br>
        Copyright ©2013-<?php echo date('Y');?> OptAim Technology All rights reserved.
    </p>
</footer>
<?php
if(!YII_DEBUG){
?>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?ad6ffaa54ba44eab576f5fdcfd845310";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
<?php
}
?>
</body>
</html>
