<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/mobile/mobile-index.js"></script>
<div id="section">
    <div class="section-header clearfix">
        <a id="share_app" class="share pull-right"><i><img src="/img/add.png" /></i>分享新App</a>
    </div>
    <div class="title-left pull-left">
        <ul>
            <li><a href="javascript:;" id="type_category_name">全部/全部</a><i></i></li>
        </ul>
    </div>
    <ol class="section-list">
        <li class="clearfix" _key="type">
            <span>系统：</span>
            <a href="javascript:;" class="active" _value="0">全部</a>
            <a href="javascript:;" _value="1">IOS</a>
            <a href="javascript:;" _value="2">Android</a>
        </li>
        <li class="clearfix" _key="category">
            <span>分类：</span>
            <?php foreach ($systemCategory as $key => $value): ?>
            <a href="javascript:;" <?php if ($key == 0) echo 'class="active"';?> _value="<?php echo $key;?>"><?php echo $value;?></a>
            <?php endforeach;?>
        </li>
    </ol>
</div>
<div id="container">
     <dl>
         <dt>
             <ul class="nav_list nav_task clearfix" style="position:fixed;bottom:0;left:0;z-index:10;">
                 <li class="pull-left active" id="allApps" _value="1">
                     <a href="javascript:;"><i class="change"></i>最新</a>
                 </li>
                 <li class="pull-left" id="fastUp" _value="2"><a href="javascript:;"><i></i>上升最快</a></li>
                 <li class="pull-left" id="mostComment" _value="3"><a href="javascript:;"><i></i>热议</a></li>
                 <li class="pull-left" id="up" _value="4"><a href="javascript:;"><i></i>得分</a></li>
             </ul>
         </dt>
         <div id="appList" class="app-list">
         </div>
     </dl>
    <hr/>
    <h4 class="text-center"></h4>
</div>
<div id="return">
    <img src="/img/top.png" width="100%"/>
</div>
<input type="hidden" id="userId" value="<?php echo $userId;?>"/>
<input type="hidden" id="isFollow" value="<?php echo $isFollow;?>"/>