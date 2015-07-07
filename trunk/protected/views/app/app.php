<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/index.js"></script>
<div class="menu-nav">
    <div class="menu-nav-list">
        <ul>
        <?php foreach ($systemCategory as $key => $value): ?>
            <li class="<?php if ($category == $key) echo 'active';?>"><a href="/app/index?category=<?php echo $key;?>&type=<?php echo $type;?>&order=<?php echo $order;?>"><?php echo $value; ?></a></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
<div id="section">
    <div class="section-header clearfix">
            <a href="#myModal" id="shareButton" role="button" class="share pull-right"  data-toggle="modal">分享新App</a>
            <form id="search" class="pull-right">
                 <input type="text" name="search" id="searchValue" class="pull-left" placeholder='请输入关键词后按"回车"进行搜索' value="<?php echo $search;?>"/>
                 <button class="search-btn">
                    <span class="search"></span>
                 </button>
            </form>
            <?php
                if (!empty(Yii::app()->user->id)) {
            ?>
                    <div id="myModal" class="modal hide fade modal-share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeButton">×</button>
                            <h4>分享新App</h4>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label for="appUrl">App链接：（<a href="/static/howtoshare.html" target="_blank">如何填写App链接</a>）</label>
                                <input type="url" id="appUrl" class="info" name="appUrl" placeholder="请输入App链接" required="required" aria-required="true"/>
                                <span id="checkUrl" class="help-inline"></span>
                            </div>
                            <div class="pro-url">
                                <label for="url" style="width:200px;">App官网：<span style="color:#888;">（可不填）</span></label>
                                <input type="url" id="url" class="info" name="url" placeholder="请输入App的官方网址" />
                            </div>
                            <div class="pro-detail">
                                <label for="detail">一句话描述这个App：<span style="color:#888;">（可不填）</span></label>
                                <textarea  id="detail" name="explain" cols="20" rows="4" ></textarea>
                                <br>
                                <small>请精简描述，40字以内</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal" aria-hidden="true" id="submitCancel">取消</button>
                            <button class="btn btn-primary" type="submit" id="shareSubmit">发布</button>
                        </div>
                    </div>
            <?php
                }
            ?>
    </div>
</div>
<div id="container">
     <dl>
         <dt>
             <div class="btn-nav clearfix">
                 <ul class="nav_list pull-left" id="selectUl">
                     <li class="<?php if ($order == 1 || $order == '') echo 'orderActive'?> pull-left" id="allApps" ><a href="/app/index?category=<?php echo $category;?>&type=<?php echo $type;?>&order=1">最新</a></li>
                     <li class="<?php if ($order == 2) echo 'orderActive'?> pull-left" id="fastUp" ><a href="/app/index?category=<?php echo $category;?>&type=<?php echo $type;?>&order=2">上升最快</a></li>
                     <li class="<?php if ($order == 3) echo 'orderActive'?> pull-left" id="mostComment" ><a href="/app/index?category=<?php echo $category;?>&type=<?php echo $type;?>&order=3">热议</a></li>
                     <li class="<?php if ($order == 4) echo 'orderActive'?> pull-left" id="up" ><a href="/app/index?category=<?php echo $category;?>&type=<?php echo $type;?>&order=4">得分</a></li>
                 </ul>
                 <ul class="nav_list pull-right" id="typeUl">
                     <li class="pull-right <?php if ($type == 0 || $type == '') echo 'orderActive'?>" _type="0">
                        <a id="typeAllSelect" href="/app/index?category=<?php echo $category;?>&type=0&order=<?php echo $order;?>" title="查看全部">全部</a>
                     </li>
                     <li class="pull-right <?php if ($type == 2) echo 'orderActive'?>" _type="2">
                        <a id="androidSelect" href="/app/index?category=<?php echo $category;?>&type=2&order=<?php echo $order;?>" title="只看Android" class="androidIcon arrowup"><i class="an"></i>Android</a>
                     </li>
                     <li class="pull-right <?php if ($type == 1) echo 'orderActive'?>" _type="1">
                         <a id="iosSelect" href="/app/index?category=<?php echo $category;?>&type=1&order=<?php echo $order;?>" title="只看IOS" class="iosIcon arrowup"><i class="apple"></i>IOS</a>
                     </li>
                 </ul>
             </div>
             <div class="title-left">
                <ul id="categoryUl">
                
                </ul>
            </div>
         </dt>
    <?php foreach($data as $app): ?>
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
                        <a class="pull-left" href="javascript:;" _id="<?php echo $app['Id']?>" id="favorite-<?php echo $app['Id']?>" title="<?php echo $app['hasFavorited'] ? '已收藏': '收藏';?>"><span class="collection <?php echo $app['hasFavorited'] ? 'collectioned' : '';?>"></span></a>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left remarks ellipsis-text">
                            <?php echo ($app['Remarks'] !== '') ? $app['Remarks'] : $app['AppInfo'];?>
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
        <?php endforeach;?>
      </dl>
      <div style="max-width:900px;margin:0 auto;border-top:1px solid #f4f4f4;height:10px;"></div>
        <h4 class="text-center" id="loading_msg" >
            <?php if($pagecount <= 1):?>
            没有更多了
            <?php endif;?>
        </h4>
        <div class="text-center" id="more-a">
            <a href="javascript:;" class="more-a">点击显示更多</a>
        </div>
</div>
<div id="return">
    <img src="/img/top.png" width="100%"/>
</div>
<input type="hidden" id="type" name="type" value="<?php echo $type;?>" />
<input type="hidden" id="order" name="order" value="<?php echo $order;?>" />
<input type="hidden" id="category" name="category" value="<?php echo $category;?>" />
<input type="hidden" id="pagecount" value="<?php echo $pagecount; ?>"/>
<input type="hidden" id="maxid" value="<?php echo $maxid; ?>"/>
