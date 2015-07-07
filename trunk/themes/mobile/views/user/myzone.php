<link href="<?php echo Yii::app()->request->baseUrl;?>/css/mobile-user.css" rel="stylesheet" type="text/css">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/app/mobile/mobile-myzone.js"></script>
<!--个人主页-->
<?php
if (isset($data['member'])) {
    $icon = $data['member']['icon'];
    $memberName = $data['member']['memberName'];
    $email = $data['member']['email'];
} else {
    $icon =  Yii::app()->user->userurl;
    $memberName =  Yii::app()->user->username;
}
?>
<div class="ag-mz span12 clearfix">
    <div class="ag-mz-info span12">
        <div class="ag-mz-info-img">
            <img src="<?php echo $icon; ?>" />
        </div>
        <div class="ag-mz-info-username">
            <div class="ag-mz-info-name ellipsis-text">
                <?php echo $data['amI'] ? '您好，' : '';?><span id="username"><?php echo $memberName;?></span>
            </div>
            <?php
                if ($data['amI']) {
            ?>
                    <div class="ag-mz-info-login-time">
                        上次登陆时间：<?php echo Yii::app()->user->lastLoginTime;?>
                    </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="ag-mz-myApp span12">
        <span>
            &nbsp&nbsp 分享的App：
            <?php
            if ($data['appCount'] == 0) {
                echo '0个';
            } elseif ($data['appCount'] > 0) {
                echo "<a href='/user/myapplist?memberid=".$data['member']['memberID']."'>".$data['appCount']."个</a>";
            }
            ?>
        </span>
    </div>
    <div class="ag-mz-myApp span12">
        <span>
            &nbsp&nbsp App被赞了：
            <?php
            echo $data['appUp'];
            ?>次
        </span>
    </div>
    <div class="ag-mz-myApp span12">
        <span>
            互动过的App：
            <?php
            if ($data['interactedApp'] == 0) {
                echo '0个';
            } else {
                echo "<a href='/user/interactionlist?memberid=".$data['member']['memberID']."'>".$data['interactedApp']."个</a>";
            }
            ?>
        </span>
    </div
    <input type="hidden" id="hidden_username" value="<?php echo $memberName;?>"/>
    <input type="hidden" id="hidden_email" value="<?php echo $email;?>"/>
</div>
