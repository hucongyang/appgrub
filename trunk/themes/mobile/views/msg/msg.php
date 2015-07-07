<div class="content" style="min-height:300px">
    <section class="notification">
        <div class="mobile-notice-title">
            <div class="mobile-notice-title-font">我的通知</div>
        </div>
        <div id="comment-list" class="comment-list">
                <?php
                    $countMsg = count($msg);
                    if ($countMsg === 0) {
                ?>
                        <div class="alert alert-info no-content">
                            无通知内容
                        </div>
                <?php
                    } else {
                        foreach ($msg as $myMessage) {
                ?>
                            <div class="comment clearfix">
                                <div>
                                    <small><?php echo $myMessage['createTime']; ?></small>
                                </div>
                                <div class="notice-list clearfix">
                                    <?php
                                    if ($myMessage['type'] != 0) {
                                        ?>
                                        <span class="ellipsis-text username">
                                            <a href="/user/myzone?memberid=<?php echo $myMessage['authorID'] ?>"><?php echo $myMessage['authorName']; ?></a> &nbsp;
                                        </span>
                                        <?php
                                        if ($myMessage['type'] == 1) {
                                            echo '评论了<a href="/produce/index/'.$myMessage['appID'].'" title="'.$myMessage['appName'].'"><span class="ellipsis-text appname">《'.$myMessage['appName'].'》</span></a>';
                                        } elseif ($myMessage['type'] == 2) {
                                            echo '在<a href="/produce/index/'.$myMessage['appID'].'" title="《'.$myMessage['appName'].'》"><span class="ellipsis-text appname">《'.$myMessage['appName'].'》</span></a>中回复了你';
                                        } elseif ($myMessage['type'] == 3) {
                                            echo '在<a href="/produce/index/'.$myMessage['appID'].'" title="《'.$myMessage['appName'].'》"><span class="ellipsis-text appname">《'.$myMessage['appName'].'》</span></a>中@了你';
                                        }
                                    } else {
                                        echo '<span>系统消息：</span>';
                                    }
                                    ?>
                                </div>
                                <p><?php echo $myMessage['msg']; ?></p>
                            </div>
                <?php
                        }//foreach
                    }//else
                ?>
        </div>
    </section>
</div>
