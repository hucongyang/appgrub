<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/hammer.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.hammer.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/lib/sweet-alert.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/lib/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/common_func.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/mobile/mobile-detail.js"></script>
<?php $userId = !empty(Yii::app()->user->id) ? Yii::app()->user->id : 0;?>
<?php $userAgent = Yii::app()->request->userAgent;?>
<div id="container" style="margin-top:0;">
<dl>
    <dd class="clearfix" style="padding: 10px 0;">
        <div class="content-list row">
            <div class="pull-left">
                <a class="thumbnail">
                    <img id="app_logo" src="<?php  echo $data['IconUrl'];?>" width="36px;">
                </a>
            </div>
            <div class="pull-left detail-title" id="app_name"><?php echo $data['AppName']; ?></div>
        </div>
        <div class="content-list row">
            <div class="detailInProduce clearfix">
                <?php $appBtnClass = ($data['SourceId'] == 1) ? 'appStore' : 'android';?>
                <a id="download_app_btn" href="<?php echo $data['AppUrl'];?>" class="pull-left <?php echo $appBtnClass;?>" target="_blank"><i></i>前往<?php echo $data['markName'];?>下载</a>
                <div class="fileSize pull-left"><?php echo strtoupper($data['pushListObj']); ?></div>
            </div>
        </div>
        <div class="content-list row">
            <div class="pull-left remarks"><?php echo $data['Remarks']; ?></div>
            <div class="pull-right col-md-4">
                <a href="/user/myzone?memberid=<?php echo $data['CommitUserId'];?>" class="img user-thumbnail" target="_blank">
                    <img src="<?php echo $data['userurl']; ?>" class="img-circle"/>
                </a>
            </div>
            <div class="shareDate pull-right">分享于<?php echo $data['CommitTime']?></div>
        </div>
    </dd>
</dl>
<?php if(!empty($data['imgurl']) || $data['AppInfo'] !== ''): ?>
<div class="good" style="border-top: none;">
    <?php if(!empty($data['imgurl'])): ?>
    <div class="carousel slide app-img-carousel" id="app_carousel">
        <div class="carousel-inner">
            <?php foreach ($data['imgurl'] as $key => $imgUrl): ?>
                <div class="<?php if ($key == 0) {echo 'active';}?> item">
                    <div>
                        <img src="<?php echo $imgUrl; ?>"/>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a class="carousel-control left" href="#app_carousel" data-slide="prev">&lsaquo;</a>
        <a class="carousel-control right" href="#app_carousel" data-slide="next">&rsaquo;</a>
    </div>
    <?php endif; ?>
    <?php if($data['AppInfo'] !== ''): ?>
    <h3>应用介绍：</h3>
    <?php echo $data['AppInfo'];?>
    <?php endif;?>
</div>
<?php endif;?>
<div class="good clearfix" id="uppedPeople">
    <p style="margin-top: 10px;">
        <strong><?php echo $data['count']; ?>人觉得很赞：</strong>
    </p>
    <?php if(!empty($data['p_user'])): ?>
    <div class="good-wrap">
        <ul id ="iconArea" class="clearfix">
            <?php
            $count = count($data['p_user']);
            $pUserArrary = array_slice($data['p_user'], 0, 12);
            $str = '<li class="pull-left"><div class="good-people-area">';
            foreach ($pUserArrary as $key => $upUser) {
                if ($key > 12) {
                    break;
                }
                $str .= '<div class="good-people">';
                if($key == 11 && $count > 12) {
                    $str .= '<div class="user-more">...</div>';
                } else {
                    $username = htmlspecialchars($upUser['username']);
                    $str .= "<a href='/user/myzone?memberid={$upUser['userId']}' class='img' target='_blank'>";
                    $str .= "<img src='{$upUser['userurl']}' class='img-circle'/></a>";
                }
                $str .= '</div>';
            }
            $str .= '</div></li>';
            echo $str;
            ?>
        </ul>
    </div>
    <?php endif; ?>

</div>
<div class="good clearfix" style="border-top:none;">
    <div class="produce-share pull-right clearfix">
        <div class="share-to">
            <strong>分享到</strong>
            <a id="share_weibo" href="javascript:;" class="btn-share weibo"><i class="weibo-icon"></i><span>微博</span></a>
        </div>
    </div>
</div>

<div class="comment-list" id="comment-list">
    <h4><strong>评论(</strong><strong id="commentCount"><?php echo $data['CommentCount'] ?></strong><strong>)</strong></h4>
    <?php if(empty(Yii::app()->user->id)){ ?>
        <a href="/user/wxlogin">登录后发表评论</a>
    <?php }else{ ?>
        <textarea name="" id="content" cols="30" rows="10"></textarea>
        <div class="ico-say clearfix">
            <a href="javascript:;" class="btn-say pull-right sweet-3"  id="submit" _index="1">发表评论</a>
        </div>
    <?php }?>
    <div id="comment_wrap">
    <?php foreach($data['replies'] as $reply): ?>
        <div class="comment clearfix" id="comment_<?php echo $reply['Id'];?>">
            <div class="user-info comment-line" id="userInfo-<?php echo $reply['Id'];?>">
                <div class="good-people">
                    <a href="/user/myzone?memberid=<?php echo $reply['AuthorId'];?>" class="img" target="_blank">
                        <img src="<?php echo $reply['AuthorIcon']?>" class="img-circle"/>
                    </a>
                </div>
                <a href="/user/myzone?memberid=<?php echo $reply['AuthorId'];?>" class="aTagWrapName" target="_blank"><?php echo $reply['AuthorName']; ?></a>&nbsp; <?php echo $reply['UpdateTime'] ?>
                <?php if ($userId): ?>
                    <?php if ($userId == $reply['AuthorId']): ?>
                        <a href="javascript:;" class="delete" _id="<?php echo $reply['Id'];?>">删除</a>
                    <?php else: ?>
                        <a href="javascript:;" class="reply" _id="<?php echo $reply['Id'];?>">回复</a>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="user-content clearfix" id="replyNode-<?php echo $reply['Id'];?>">
                    <p><?php echo $reply['Content'];?></p>
                </div>
            </div>
            <ul>
                <?php foreach(array_reverse($reply['children']) as $appc): ?>
                    <li id="comment_<?php echo $appc['Id'];?>">
                        <div class="comment-line comment-comment">
                            <div class="good-people">
                                <a href="/user/myzone?memberid=<?php echo $appc['AuthorId'];?>" class="img" target="_blank">
                                    <img src="<?php echo $appc['AuthorIcon'];?>'" class="img-circle"/>
                                </a>
                            </div>
                            <a href="/user/myzone?memberid=<?php echo $appc['AuthorId'];?>" class="aTagWrapName" target="_blank"><?php echo $appc['AuthorName'] ?></a> 回复了 <a href="/user/myzone?memberid=<?php echo $appc['ToAuthorID'];?>" class="aTagWrapName" target="_blank"><?php echo $reply['ToAuthorName']; ?></a>:&nbsp;<?php echo $appc['UpdateTime']?>
                            <?php if ($userId): ?>
                                <?php if ($userId == $appc['AuthorId']): ?>
                                    <a href="javascript:;" class="delete" _id="<?php echo $appc['Id'];?>">删除</a>
                                <?php else: ?>
                                    <a href="javascript:;" class="reply" _id="<?php echo $appc['Id'];?>">回复</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="user-content clearfix">
                                <p class="replyTagP"><?php echo $appc['Content']; ?></p>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
    </div>
</div>
</div>

<div class="nav-bottom">
    <ul class="nav-bar clearfix">
        <li class="">
            <a href="#commentCount">
                <i class="icon-comment"></i>
                <span id="nav_bar_comment_num" class="nav-msg"><?php echo $data['CommentCount']; ?></span>
            </a>
        </li>
        <li class="detail-up <?php echo $data['isUpped'] ? 'link' : '';?>" _id="<?php echo $data['Id'];?>">
            <span class="arrow"></span>
            <span class="nav-msg like-num"><?php echo $data['count']; ?></span>
        </li>
        <li class="detail-collect <?php echo $data['hasFavorited'] ? 'collected' : '';?>" _id="<?php echo $data['Id']?>">
            <span class="icon-collect"></span>
            <span class="nav-msg collect-msg"><?php echo $data['hasFavorited'] ? '已收藏' : '收藏';?></span>
        </li>
    </ul>    
</div>


<input type="hidden"  id="appID" value="<?php echo $data['Id']?>"/>
<input type="hidden"  id="uid" value="<?php echo $data['username'];?>"/>
<input id="loginUserID" type="hidden" value="<?php echo Yii::app()->user->id;?>">