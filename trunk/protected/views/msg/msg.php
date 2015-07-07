<link href="<?php echo Yii::app()->request->baseUrl;?>/css/user.css" rel="stylesheet" type="text/css">
<div class="ag-mal-title">
    <div class="ag-mal-main-title">我的通知</div>
</div>
<div class="content" style="min-height:300px;max-width:900px;width:900px;">
	<section class="notification">
		<div id="comment-list" class="comment-list">
                <?php
                    $countMsg = count($msg);
                    if ($countMsg === 0) {
                ?>
                        <header class="notiication-header cf">
                            <h3 style="margin-top:15px;text-align:center;font-family: inherit;font-size: 20;font-style: inherit;font-weight: bold;" >无通知内容</h3>
                        </header>
                <?php
                    } else {
                        foreach ($msg as $myMessage) {
                ?>
                            <div class="comment clearfix">
                                <div class="user-info">
                                    <div style="min-width:50px;float:left;">
                                        <?php
                                            if ($myMessage['type'] != 0) {
                                        ?>
                                                <a href="/user/myzone?memberid=<?php echo $myMessage['authorID'] ?>"><?php echo $myMessage['authorName']; ?></a> &nbsp;
                                        <?php
                                                if ($myMessage['type'] == 1) {
                                                    echo '评论了<a href="/produce/index/'.$myMessage['appID'].'" title="'.$myMessage['appName'].'">《'.$myMessage['appName'].'》</a>';
                                                } elseif ($myMessage['type'] == 2) {
                                                    echo '在<a href="/produce/index/'.$myMessage['appID'].'" title="《'.$myMessage['appName'].'》">《'.$myMessage['appName'].'》</a>中回复了你';
                                                } elseif ($myMessage['type'] == 3) {
                                                    echo '在<a href="/produce/index/'.$myMessage['appID'].'" title="《'.$myMessage['appName'].'》">《'.$myMessage['appName'].'》</a>中@了你';
                                                }
                                            } else {
                                                echo '系统消息：';
                                            }
                                        ?>
                                    </div>
                                    <div class="pull-right"><?php echo $myMessage['createTime']; ?></div>
                                </div>
                                <div class="user-content">
                                    <p><?php echo $myMessage['msg'];?></p>
                                </div>
                            </div>
                <?php
                        }//foreach
                    }//else
                ?>
        </div>
	</section>
</div>
