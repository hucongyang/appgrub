<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
<meta name="description" content="有趣的app,有潜力的app,好玩的app,热门app，app排行,app游戏,app实用工具"  />
<meta name="keywords" content="有趣的app,有潜力的app,好玩的app,热门app，app排行,app游戏,app实用工具,app哥伦部"/>
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<link rel="shortcut icon" href="/favicon.ico" />
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css" rel="stylesheet" media="screen">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-responsive.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile-main.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="/css/sweet-alert.css">
<link rel="stylesheet" href="/css/font-awesome.min.css"/>
<script>
    var not_login_flag = <?php echo (empty(Yii::app()->user->id)) ? 1 : 0;?>;
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery-1.10.1.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/bootstrap.min.js"></script>
<script src="/js/lib/sweet-alert.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/app/mobile/mobile-main.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/common.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/mobile/mobile-appgrubAjax.js"></script>
<script>
wx.config({
    debug: false,
    appId: 'wx1ac3d9fb295a53b1',
    timestamp: <?php echo $this->timestamp;?>, // 必填，生成签名的时间戳
    nonceStr: '<?php echo $this->nonceStr;?>', // 必填，生成签名的随机串
    signature: '<?php echo $this->signature;?>',// 必填，签名，见附录1
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
});
</script>
</head>
<body>
    <div id="header">
        <h2 class="text-center clearfix">
            <?php if($this->id == 'app'): ?>
                <div class="search">
                    <form method='get' target="fake_iframe">
                        <i></i>
                    <input type="text" id="search" class="mobile-search-input" style="width: 150px; transform: translateX(-200px);"/>
                    <form>
                    <iframe id="fake_iframe" name="fake_iframe" class="hide"></iframe>
                </div>
            <?php else: ?>
                <div class="home-btn"><a href="/"><i></i></a></div>
            <?php endif; ?>
            <p style="margin:auto;font-size:20px;width:100px;height:20px;color:#fff;position:absolute;top:0;left:0;right:0;bottom:0;"><a href="/">App哥伦部</a></p>
            <?php if(empty(Yii::app()->user->id)):?>
                <div class="wei-login clearfix">
                    <a href="/user/wxlogin" class="wei login-wei pull-right" style="background:none;font-size:18px;padding:0;font-weight:normal;margin-top:4px;margin-right:10px;">登录</a>
                </div>
            <?php else: ?>
                <div class="wei-login clearfix">
                    <a href="javascript:;" class="wei login-wei login-me pull-right" id="login-me" style="border-radius:50%;width:50px;height:50px;padding:0;margin: 2px 5px 0 0;">
                        <img src="<?php echo Yii::app()->user->userurl;?>" class="img-circle" />
                    </a>
                    <div class="login-code" style="width:80px;right:0px;">
                        <p><a href="<?php echo $this->createUrl('/user/myzone');?>">我的主页</a></p>
                        <p id="msg_menu" style="position: relative;">
                            <a href="<?php echo $this->createUrl('/msg/index');?>">我的通知</a>
                        </p>
                        <p><a href="<?php echo $this->createUrl('/user/myfavorite');?>">我的收藏</a></p>
                        <p><a href="<?php echo $this->createUrl('/user/logout');?>">退出登录</a></p>
                    </div>
                </div>
            <?php endif;?>
        </h2>
    </div>
    <?php echo $content;?>
    <?php if(!YII_DEBUG):?>
    <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "//hm.baidu.com/hm.js?ad6ffaa54ba44eab576f5fdcfd845310";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <?php endif; ?>
</body>
</html>
