<link href="<?php echo Yii::app()->request->baseUrl;?>/css/user.css" rel="stylesheet" type="text/css">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/app/user/myZone.js"></script>
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
<div class="ag-mz">
    <div class="ag-mz-info">
        <div class="ag-mz-info-img">
            <img src="<?php echo $icon; ?>"/>
        </div>
        <div class="ag-mz-info-username">
            <?php
            if ($data['amI']) {
            ?>
                <div class="info-wrap">
                    <div class="info-main-left">
                        <div class="info-main-float-left">你好，</div>
                        <div id="username" class="name ellipsis-text"><?php echo $memberName;?></div>
                        <div class="info-main-float-right"><span class="user-edit" id="edit_user_info" title="编辑个人信息"><img src="/img/myzone_pencil.png"></span></div>
                    </div>
                    <div class="info-main-time">
                        <div class="info-main-float-left">上次登陆时间：</div>
                        <div class="info-main-float-left"><?php echo Yii::app()->user->lastLoginTime;?></div>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <span>
                    <?php echo $memberName; ?>
                </span>
            <?php
            }
            ?>
        </div>
    </div><!-- ag-mz-info-->
    <div class="ag-mz-myApp">
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
    <div class="ag-mz-myApp">
        <span>
            &nbsp&nbsp App被赞了：
            <?php
            echo $data['appUp'];
            ?>次
        </span>
    </div>
    <div class="ag-mz-myApp">
        <span>
            互动过的App：
            <?php
            if ($data['interactedApp'] == 0) {
                echo '0次';
            } else {
                echo "<a href='/user/interactionlist?memberid=".$data['member']['memberID']."'>".$data['interactedApp']."次</a>";
            }
            ?>
        </span>
    </div>
    <div class="modal hide fade user-edit-modal" id="user_info_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title" id="myModalLabel">
                用户设置
            </h3>
        </div>
        <div class="modal-body">
            <div class="form-horizontal">
                <div class="control-group" id="username_group">
                    <label class="control-label" for="input_username"><span>昵称:</span></label>
                    <div class="controls">
                        <input class="user-info" type="text" id="input_username" placeholder="请输入昵称">
                        <span class="help-inline"  id="username_info"></span>
                    </div>
                </div>
                <div class="control-group" id="email_group">
                    <label class="control-label" for="input_email"><span>邮箱:</span></label>
                    <div class="controls">
                        <input class="user-info" type="text" id="input_email" placeholder="请输入邮箱">
                        <span class="help-inline"  id="email_info"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="button" id="save" class="btn btn-primary">保存</button>
        </div>
    </div>
    <input type="hidden" id="hidden_username" value="<?php echo $memberName;?>"/>
    <input type="hidden" id="hidden_email" value="<?php echo $email;?>"/>
</div>
