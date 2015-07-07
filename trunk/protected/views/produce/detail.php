<link href="<?php echo Yii::app()->request->baseUrl;?>/css/jquery.atwho.min.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->baseUrl;?>/css/jquery.fancybox.css?v=2.1.5" rel="stylesheet" type="text/css" media="screen" />
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/lib/jquery.fancybox.pack.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/lib/jquery.caret.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/lib/jquery.atwho.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/common_func.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/product/detail.js"></script>
<?php $userId = !empty(Yii::app()->user->id) ? Yii::app()->user->id : 0; ?>
<div id="container" style="margin-top:0;">
    <dl>
        <dd class="clearfix" style="max-width:900px;padding:20px 10px;">
            <div class="content-list row">
                <div class="pull-left">
                    <div class="top pull-left">
                        <?php $class = $data['isUpped'] ? 'link' : '';?>
                        <a href="javascript:;" class="isLiked <?php echo $class;?>" _id="<?php echo $data['Id'];?>">
                            <span class="arrow"></span>
                            <span class="like-num"><?php echo $data['count'];?></span>
                        </a>
                    </div>
                    <div class="pull-left">
                        <a class="thumbnail">
                            <img src="<?php  echo $data['IconUrl'];?>">
                        </a>
                    </div>    
                    <div class="detailInProduce pull-left">
                        <div class="clearfix">
                            <div  class="detail-title ellipsis-text" id="app_name" title="<?php echo $data['AppName'];?>"><?php echo $data['AppName'];?></div>
                            <?php $appBtnClass = ($data['SourceId'] == 1) ? 'appStore' : 'android';?>
                            <a href="<?php echo $data['AppUrl'];?>" title="前往<?php echo $data['markName'];?>下载" class="<?php echo $appBtnClass;?> pull-left" target="_blank"><i></i><?php echo $data['markName'];?></a>
                            <div class="fileSize pull-left" title="文件大小"><?php echo strtoupper($data['pushListObj']); ?></div>
                            <a href="javascript:;" _id="<?php echo $data['Id']?>" id="favorite-<?php echo $data['Id']?>" title="<?php echo $data['hasFavorited'] ? '已收藏': '收藏';?>"><span class="collection <?php echo $data['hasFavorited'] ? 'collectioned' : '';?>"></span></a>
                        </div>
                        <div class="clearfix">
                            <div class="remarks ellipsis-text" title="<?php echo $data['Remarks']; ?>"><?php echo $data['Remarks']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="pull-right col-md-4">
                    <a href="/user/myzone?memberid=<?php echo $data['CommitUserId'];?>" class="img user-thumbnail" target="_blank" _username="<?php echo CommonFunc::encodeURIComponent($data['username']);?>">
                        <img src="<?php echo $data['userurl']; ?>" class="img-circle" width="40px;"/>
                    </a>
                </div>
                <div class="shareDate pull-right">分享于<?php echo $data['CommitTime']?></div>
            </div>
        </dd>
    </dl>
    <?php if (!empty($data['imgurl']) || $data['AppInfo'] !== ''):?>
    <div class="good">
        <?php if (!empty($data['imgurl'])):?>
        <div class="good-img">
            <a href="javascript:;" class="pull-left carousel-arrow"></a>
            <div class="soft-img">
                <ul class="img-list clearfix">
                    <?php foreach ($data['imgurl'] as $imgUrl):?>
                        <li class="pull-left" >
                            <a class="fancybox" data-fancybox-group="gallery" href="<?php echo $imgUrl;?>">
                                <img src="<?php echo $imgUrl;?>" alt="" class="carousel-img" />
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="javascript:;" class="pull-right carousel-arrow"></a>
        </div>
        <?php endif;?>
        <?php if($data['AppInfo'] !== ''): ?>
        <h3>应用介绍：</h3>
        <?php echo $data['AppInfo']; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="good clearfix" id="uppedPeople">
        <br>
        <p>
            <strong><?php echo $data['count']; ?>人觉得很赞：</strong>
            <a href="javascript:;" class="btn-lr pull-right">
                <span class="old"></span>
                <span></span>
            </a>
        </p>
        <div class="tooltip bottom" id="like_user_tooltip" role="tooltip">
            <div class="tooltip-arrow"></div>
            <div class="tooltip-inner"/>
                <img class="img-circle" />
                <p></p>
            </div>
        </div>
        <?php if (!empty($data['p_user'])): ?>
            <div class="good-wrap">
                <ul id ="iconArea" class="clearfix">
                   <li class="pull-left">
                        <div class="good-people-area">
                        <?php foreach ($data['p_user'] as $upUser): ?>
                            <div class="good-people">
                                <a href="/user/myzone?memberid=<?php echo $upUser['userId']; ?>" class="img like-user-thumbnail" target="_blank" _username="<?php echo CommonFunc::encodeURIComponent($upUser['username']);?>">
                                    <img src="<?php echo $upUser['userurl']; ?>" class="img-circle"/>
                                </a>
                            </div>
                        <?php endforeach;?>
                        </div>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <div class="produce-share clearfix">
        <p class="shareTo">
            <strong>分享到</strong>
        </p>
        <a id="share_weibo" href="javascript:;" class="weibo"><span>微博</span></a>
        <div class="relative">
            <a href="javascript:;" class="weixin dropdown-hover" id="shareQRcode">
                <span>微信</span>
            </a>
            <div class="dropdown-box code">
                <img id="qrcode" src="<?php echo '/produce/getqrcode/'. $data['Id'];?>" alt="二维码">
                <div>微信扫一扫：分享</div>
            </div>
        </div>
    </div>
    <div class="comment-list" id="comment-list">
        <br>
        <h4><strong>评论(</strong><strong id="commentCount"><?php echo $data['CommentCount'] ?></strong><strong>)</strong></h4>
        <?php if (empty(Yii::app()->user->id)):?>
            <a href="/user/login">登录后发表评论</a>
        <?php else: ?>
        <textarea name="" id="content" cols="30" rows="10"></textarea>
        <div class="ico-say clearfix">
            <a href="javascript:;" class="face-ico face-btn face-other"><em></em><span>表情</span></a>
            <a href="javascript:;" class="btn-say pull-right"  id="submit" _index="1">发表评论</a>
        </div>
        <div class="W_layer" on="true">
            <span class="arrow_1"></span>
            <span class="arrow_2"></span>
            <div class="ico-name">
                <div class="W_layer_close"><a href="javascript:;"></a>
                </div>
                <div class="layer_faces" id="layer_faces"></div>
                <div class="W_layer_arrow"><span class="W_arrow_bor W_arrow_bor_t" node-type="arrow" style="left: 16px;"><i class="S_line3"></i><em class="S_bg2_br"></em></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div id="comment_wrap">
        <?php foreach($data['replies'] as $reply): ?>
            <div class="comment clearfix" id="comment_<?php echo $reply['Id'];?>">
                <div class="user-info comment-line" id="userInfo-<?php echo $reply['Id'];?>">
                    <div class="good-people">
                        <a href="/user/myzone?memberid=<?php echo $reply['AuthorId'];?>" class="img user-thumbnail" target="_blank" _username="<?php echo CommonFunc::encodeURIComponent($reply['AuthorName']);?>">
                            <img src="<?php echo $reply['AuthorIcon']?>" class="img-circle"/>
                        </a>
                    </div>
                    <a href="/user/myzone?memberid=<?php echo $reply['AuthorId'];?>" class="aTagWrapName" target="_blank" id="aTag" title="<?php echo $reply['AuthorName']; ?>"><?php echo $reply['AuthorName']; ?></a>&nbsp;<small class="reply-time"> <?php echo $reply['UpdateTime'] ?></small>
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
                                <a href="/user/myzone?memberid=<?php echo $appc['AuthorId'];?>" class="img user-thumbnail" target="_blank" _username="<?php echo CommonFunc::encodeURIComponent($appc['AuthorName']);?>">
                                    <img src="<?php echo $appc['AuthorIcon'];?>'" class="img-circle"/>
                                </a>
                            </div>
                            <a href="/user/myzone?memberid=<?php echo $appc['AuthorId'];?>" class="aTagWrapName" target="_blank" id="aTag" title="<?php echo $appc['AuthorName'] ?>" ><?php echo $appc['AuthorName'] ?></a> 回复了 <a href="/user/myzone?memberid=<?php echo $appc['ToAuthorID'];?>" class="aTagWrapName" target="_blank" id="aTag" title="<?php echo $appc['ToAuthorName']; ?>" ><?php echo $reply['ToAuthorName']; ?></a>:&nbsp;<small class="reply-time"> <?php echo $appc['UpdateTime']?></small>
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

<div id="download_app_qrcode" class="download-qrcode">
    <img title="扫描二维码安装" src="/produce/getdownloadqrcode/<?php echo $data['Id']?>" alt="二维码">
    <div>扫描二维码下载App</div>
</div>

<input type="hidden"  id="appID" value="<?php echo $data['Id']?>"/>
<input type="hidden"  id="uid" value="<?php echo $data['username'];?>"/>
<input id="userLength" type="hidden" value="<?php echo $data['count'];?>">
