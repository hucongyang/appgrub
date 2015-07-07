<link href="<?php echo Yii::app()->request->baseUrl;?>/css/user.css" rel="stylesheet" type="text/css">
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/user/interactionList.js"></script>
<div class="ag-mal-body" id="ag-mal-body-id">
    <div class="ag-mal-title">
        <div class="ag-mal-sub-title"><a href="/user/myzone">我的主页</a></div>
        <div class="ag-mal-main-title">
            <ul id="interaction_change" class="interaction-change">
                <li>
                    <a href="/user/interactionlist?type=1&memberid=<?php echo $memberID;?>" class="interaction-like">点赞的App</a>
                </li>
                <li>
                    <a href="/user/interactionlist?type=2&memberid=<?php echo $memberID;?>" class="interaction-comment">评论的App</a>
                </li>
            </ul>
        </div>
    </div>
    <dl>
    <div id="likedApp">
        <?php
            if (empty($apps)) {
        ?>
                <header class="notiication-header cf">
                    <div class="alert alert-info interaction-no-content">
                        还没有<?php echo $type == 1 ? '点过赞' : '评论过';?>哦
                    </div>
                </header>
        <?php
            } else {
                foreach ($apps as $app) {
        ?>
                    <dd class="list clearfix">
                        <div class="content-list clearfix row">
                            <div class="col-md-8">
                                <?php
                                    $class = $app['isUpped'] ? 'link' : '';
                                ?>
                                <div class="top pull-left">
                                     <a href="javascript:;" class="isLiked <?php echo $class;?>" _id="<?php echo $app['Id']?>">
                                         <span class="arrow"></span>
                                         <span class="like-num"><?php echo $app['count'] ?></span>
                                     </a>
                                 </div>
                                <div title="<?php echo $app['AppName']?>" class="appListContent" target="_blank" _appID="<?php echo $app['Id']?>">
                                <div class="pull-left thumbnail" style="width:36px;height:36px;">
                                    <img src="<?php echo $app['IconUrl']?>" width="36px" height="36px"/>
                                </div>
                            <div class="detail pull-left">
                                <div class="clearfix">
                                    <a class="pull-left title ellipsis-text" href="/produce/index/<?php echo $app['Id']?>" title="<?php echo $app['AppName']?>" target="_blank"><?php echo $app['AppName']?></a>
                                    <a class="pull-left say" href="javascript:;" title="评论数"><?php echo $app['CommentCount']?><i></i></a>
                                    <a class="pull-left" href="javascript:;" _id="<?php echo $app['Id']?>" id="favorite-<?php echo $app['Id']?>" title="<?php echo $app['hasFavorited'] ? '已收藏': '收藏';?>"><span class="collection <?php echo $app['hasFavorited'] ? 'collectioned' : 'uncollection';?>"></span></a>
                                </div>
                                <div class="clearfix">
                                    <div class="pull-left remarks ellipsis-text">
                                        <?php echo ($app['Remarks'] !== '') ? htmlspecialchars($app['Remarks']) : htmlspecialchars($app['AppInfo']);?>
                                    </div>
                                </div>
                            </div>
                            <div class="pull-right col-md-4">
                                <a href="javascript:;" class="phone">
                                    <img src="/img/<?php echo strtolower($app['OS']).'Grey';?>.png" />
                                </a>
                                <a href="/user/myzone?memberid=<?php echo $app['commitUser'];?>" class="img user-thumbnail" target="_blank" _username="<?php echo CommonFunc::encodeURIComponent($app['username']);?>">
                                    <img src="<?php echo $app['userurl'] ; ?>" class="img-circle" width="30px;" />
                                </a>
                            </div>
                            <div class="shareDateString">分享于 <?php echo $app['CommitTime']?></div>
                        </div>
                        </div>
                    </dd>
                <?php
                }
            }
        ?>
    </div>
    </dl>
</div>
<input id="interaction_type" type="hidden" value = <?php echo isset($_GET['type']) ? $_GET['type'] : 1;?> />
