<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile-user.css" rel="stylesheet" type="text/css">
<div class="ag-mal-body">
    <div class="app-list">
    <?php
        foreach ($data as $app) {
    ?>
            <dd class="list clearfix">
                <div class="content-list clearfix row">
                    <div class="col-md-8 clearfix">
                        <?php
                            $statusFlag = $app['Status'] == 0 ? true : false;
                            if ($statusFlag) {
                        ?>
                                <div class="top pull-left">
                                    <?php $upClass = $app['isUpped'] ? 'link' : '';?>
                                    <a href="javascript:;" class="isLiked <?php echo $upClass?>" _id="<?php echo $app['Id'];?>">
                                        <span class="arrow"></span>
                                        <span class="like-num"><?php echo $app['count'];?></span>
                                    </a>
                                </div>
                        <?php
                            }
                        ?>
                        <div class="detail pull-left">
                            <div class="d-img">
                                <?php
                                    $appCheckClass = '';
                                    if ($app['Status'] == 1) {
                                        $appCheckClass = 'app-wait-4-grub';
                                    } else if ($app['Status'] == 2) {
                                        $appCheckClass = 'app-grub-failure';
                                    }
                                ?>
                                <a href="javascript:;" class="dimg <?php echo $appCheckClass;?>">
                                    <?php
                                        if ($app['Status'] == 1) {
                                            echo '待审核';
                                        } else if ($app['Status'] == 2) {
                                            echo '审核<br/>失败';
                                        } else {
                                            echo "<img src='{$app['IconUrl']}' class='img-circle img-radius'/>";
                                        }
                                    ?>
                                </a>
                            </div>
                            <div class="limit clearfix">
                                <?php
                                if ($statusFlag) {
                                    ?>
                                    <a href="/produce/index/<?php echo $app['Id'];?>" target="_blank" class="title">
                                        <?php echo $app['AppName'];?>
                                    </a>
                                <?php
                                } else {
                                ?>
                                    <a href="javascript:;" class="title" title="<?php echo $app['AppUrl']?>">
                                        <?php echo $app['AppUrl'];?>
                                    </a>
                                <?php
                                }
                                if ($statusFlag) {
                                ?>
                                    <div class="say pull-right">
                                        <i class="<?php echo 'd-type-' . strtolower($app['OS']);?>"></i>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <?php
                            if ($statusFlag) {
                               $summary = $app['Remarks'] ? $app['Remarks'] : $app['AppInfo'];
                            ?>
                                <div class="limit-auto clearfix">
                                    <p><?php echo $summary;?></p>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="shareDateMobile clearfix">
                                <div class="pull-right"><?php echo $app['CommitTime'];?></div>
                                <?php
                                if ($statusFlag) {
                                ?>
                                    <div class="say pull-right" style="margin-right:10px;">
                                        <?php echo $app['CommentCount'];?>
                                        <i></i>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    if ($statusFlag) {
                ?>
                        <a href="/produce/index/<?php echo $app['Id'];?>" class="content-link" target="_blank"></a>
                <?php
                    }
                ?>
            </dd>
    <?php
        }
    ?>
    </div>
</div>
