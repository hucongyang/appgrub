<link href="<?php echo Yii::app()->request->baseUrl;?>/css/user.css" rel="stylesheet" type="text/css">
<div class="ag-mal-body" id="ag-mal-body-id">
    <div class="ag-mal-title">
        <div class="ag-mal-sub-title"><a href="/user/myzone">我的主页</a></div>
        <div class="ag-mal-main-title">我分享的 App</div>
    </div>
    <?php foreach($data as $app): ?>
        <dd class="list clearfix">
            <div class="content-list clearfix row">
                <div class="col-md-8">
                    <?php
                        $class = $app['isUpped'] ? 'link' : '';
                        $statusFlag = $app['Status'] == 0 ? true : false;
                        $checkClass = $statusFlag ? '' : 'wait-for-check';
                        if ($statusFlag) {
                    ?>
                            <div class="top pull-left">
                                <a href="javascript:;" class="isLiked <?php echo $class;?>" _id="<?php echo $app['Id']?>">
                                    <span class="arrow"></span>
                                    <span class="like-num"><?php echo $app['count'] ?></span>
                                </a>
                            </div>
                    <?php
                        }
                    ?>
                    <div title="<?php echo $app['AppName']?>" class="appListContent" target="_blank" _appID="<?php echo $app['Id']?>">
                        <div class="pull-left thumbnail <?php echo $checkClass;?>" style="width:36px;height:36px; <?php echo $app['Status'] == 2 ? 'line-height:18px;' : ''; ?>">
                            <?php
                                if ($statusFlag) {
                            ?>
                                    <img src="<?php echo $app['IconUrl']?>" width="36px" height="36px"/>
                            <?php
                                } else {
                                    if ($app['Status'] == 1) {
                                        echo '审核中';
                                    } else {
                                        echo '审核<br/>失败';
                                    }
                                }
                            ?>
                        </div>
                        <div class="detail pull-left">
                            <div class="clearfix">
                                <?php
                                if ($statusFlag) {
                                ?>
                                <a class="pull-left title ellipsis-text" href="/produce/index/<?php echo $app['Id']?>" title="<?php echo $app['AppName']?>" target="_blank"><?php echo $app['AppName']?></a>
                                <a class="pull-left say" href="javascript:;" title="评论数"><?php echo $app['CommentCount']?><i></i></a>
                                <a class="pull-left" href="javascript:;" _id="<?php echo $app['Id']?>" id="favorite-<?php echo $app['Id']?>" title="<?php echo $app['hasFavorited'] ? '已收藏': '收藏';?>"><span class="collection <?php echo $app['hasFavorited'] ? 'collectioned' : '';?>"></span></a>
                                <?php
                                } else {
                                ?>
                                <a class="pull-left title ellipsis-text" href="javascript:;" title="<?php echo $app['AppUrl']?>" target="_blank"><?php echo $app['AppUrl']?></a>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="clearfix">
                                <div class="pull-left remarks ellipsis-text">
                                    <?php echo ($app['Remarks'] !== '') ? $app['Remarks'] : $app['AppInfo'];?>
                                </div>
                            </div>
                        </div>
                        <div class="pull-right col-md-4">
                            <?php
                                if ($statusFlag) {
                            ?>
                                    <a href="javascript:;" class="phone">
                                        <img src="/img/<?php echo strtolower($app['OS']).'Grey';?>.png" />
                                    </a>
                            <?php
                                }
                            ?>
                            <a href="/user/myzone?memberid=<?php echo $app['commitUser'];?>" class="img user-thumbnail" target="_blank" _username="<?php echo CommonFunc::encodeURIComponent($app['username']);?>">
                                <img src="<?php echo $app['userurl'] ; ?>" class="img-circle" width="30px;" />
                            </a>
                        </div>
                        <div class="shareDateString">分享于 <?php echo $app['CommitTime']?></div>
                    </div>
                </div>
        </dd>
    <?php endforeach;?>
</div>
