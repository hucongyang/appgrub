<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile-user.css" rel="stylesheet" type="text/css">
<div class="ag-mal-body">
    <dl>
        <div class="mobile-mf-title">
            <div class="mobile-mf-title-font">我收藏的App</div>
        </div>
        <?php
        if (empty($data)) {
        ?>
        <div class="alert alert-info no-content">
            您还没有收藏过App哦
        </div>
        <?php
        } else {
        ?>
        <div class="app-list">
        <?php
            foreach ($data as $app) {
        ?>
                <dd class="list clearfix">
                    <div class="content-list clearfix row">
                        <div class="col-md-8 clearfix">
                            <div class="top pull-left">
                                <?php $upClass = $app['isUpped'] ? 'link' : '';?>
                                <a href="javascript:;" class="isLiked <?php echo $upClass?>" _id="<?php echo $app['Id'];?>">
                                    <span class="arrow"></span>
                                    <span class="like-num"><?php echo $app['count'];?></span>
                                </a>
                            </div>
                            <div class="detail pull-left">
                                <div class="d-img">
                                    <a href="javascript:;" class="dimg">
                                        <img src="<?php echo $app['IconUrl'];?>" class="img-circle img-radius"/>
                                    </a>
                                </div>
                                <div class="limit clearfix">
                                    <a href="/produce/index/<?php echo $app['Id'];?>" target="_blank" class="title"><?php echo $app['AppName'];?></a>
                                    <div class="say pull-right">
                                        <i class="<?php echo 'd-type-' . strtolower($app['OS']);?>"></i>
                                    </div>
                                </div>
                                <?php $summary = $app['Remarks'] ? $app['Remarks'] : $app['AppInfo'];?>
                                <div class="limit-auto clearfix">
                                    <p><?php echo $summary;?></p>
                                </div>
                                <div class="shareDateMobile clearfix">
                                    <div class="pull-right"><?php echo $app['CommitTime'];?></div>
                                    <div class="say pull-right" style="margin-right:10px;">
                                        <?php echo $app['CommentCount'];?>
                                        <i></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="/produce/index/<?php echo $app['Id'];?>" class="content-link" target="_blank"></a>
                </dd>
        <?php
            }
        ?>
            </div>
        <?php
        }
        ?>
    <dl>
</div>