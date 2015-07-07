<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile-user.css" rel="stylesheet" type="text/css">
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/mobile/mobile-interactionlist.js"></script>
<div class="ag-mal-body">
    <dl>
    <div class="interaction-change-button">
        <ul id="interaction_change" class="nav nav-tabs">
            <li>
                <a href="/#appLike" data-toggle="tab" _type="1">点赞的App</a>
            </li>
            <li>
                <a href="/#appComment" data-toggle="tab" _type="2">评论的App</a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane app-list-pane" id="appLike" _type="1"></div>
        <div class="tab-pane app-list-pane" id="appComment" _type="2"></div>
    </div>
    </dl>
</div>
<input id="memberID" type="hidden" value =<?php echo Yii::app()->request->getParam('memberid');?> />