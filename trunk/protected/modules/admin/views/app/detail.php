<link href="<?php echo Yii::app()->request->baseUrl;?>/css/admin.css" rel="stylesheet">
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/common_func.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/admin/detail.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/appgrubBootstrap.js"></script>
<style xmlns="http://www.w3.org/1999/html">
    @media(max-width:865px){
        .soft-img{width:621px;}
    }
    @media(max-width:652px){
        .soft-img{width:414px;}
    }
    @media(max-width:436px){
        .soft-img{width:207px;}
    }
    @media(max-width:500px){
        #header h2:nth-child(2) {
            display: block;
        }
        #header h2:nth-child(1) .wei:nth-child(1),
        #header h2:nth-child(1) .wei:nth-child(3) {
            display: none;
        }
    }
</style>
<script>
    $(function() {
        //轮播
        var l = $('.img-list li').length;
        var w = $('.img-list li').outerWidth(true);
        var n = 0;
        $('.img-list').width(l * w);
        $('.good-img a:first').click(function() {
            n--;
            if (n < 0) {
                n = l - 1;
            }
            $('.img-list').css('left', -n * w);
        });
        $('.good-img a:last').click(function() {
            n++;
            if (n >= l) {
                n = 0;
            }
            $('.img-list').css('left', -n * w);
        });
    });

    $(function () {
        $('#container2').highcharts({
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: '数量趋势'
            },
            subtitle: {
                text: '来源：www.appgrub.com'
            },
            xAxis: [{
                categories: <?php echo $Date_json; ?>,
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value} 次',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: '下载量',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: '评论量',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value} 次',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                x: 120,
                verticalAlign: 'top',
                y: 100,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
            series: [{
                name: '评论量',
                type: 'spline',
                yAxis: 1,
                data: <?php echo $CommentNum_json; ?>,
                tooltip: {
                    valueSuffix: ' 次'
                }

            }, {
                name: '下载量',
                type: 'spline',
                data: <?php echo $DownLoadNum_json; ?>,
                tooltip: {
                    valueSuffix: '次'
                }
            }]
        });
    });
</script>


<div id="container" class="container">
    <dl>
        <dd class="clearfix">
            <div class="content-list clearfix row">
                <div class="pull-left">
                    <a href="javascript:;" target="_blank" class="thumbnail">
                        <img src="<?php  echo $data['IconUrl'];?>" width="36px;">
                    </a>
                </div>
                <div class="detailInProduce pull-left">
                    <a href="javascript:;" target="_blank" class="detail-title ellipsis-text" title="<?php echo $data['AppName']; ?>"><?php echo $data['AppName']; ?></a>
                    <?php $appBtnClass = ($data['SourceId'] == 1) ? 'appStore' : 'android';?>
                    <a href="<?php echo $data['AppUrl'];?>" class="<?php echo $appBtnClass;?> pull-left" title="前往<?php echo $data['ChnName'];?>下载" target="_blank"><i></i><?php echo $data['ChnName'];?></a>
                </div>
            </div>

            <div class="content-list clearfix row">
                <div class="col-md-8">
                    <span>大小：<?php echo $data['FileSize']; ?></span><br />
                    <span>发布日期：<?php echo $data['ProcessDate']; ?></span><br />
                    <span>分类：<?php echo $data['CategoryName']; ?></span><br>
                    <span>下载量：<?php echo $data['DownLoadNum']; ?></span><br>
                    <span>评论量：<?php echo $data['CommentNum']; ?></span><br>
                    <div class="star">
                        <p style="width:80%"></p>
                        <div id="container2"></div>
                    </div>
                </div>
            </div>

        </dd>
    </dl>

    <?php if (!empty($data['imgurl'])): ?>
        <div class="good">
            <div class="good-img">
                <a href="javascript:;" class="pull-left"></a>
                <div class="soft-img">
                    <ul class="img-list clearfix">
                        <?php foreach ($data['imgurl'] as $app): ?>
                            <li class="pull-left">
                                <img src="<?php echo $app['imgurl'] ?>"/>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="javascript:;" class="pull-right"></a>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!empty($data['AppInfo'])): ?>
        <div class="content-list">
            <input type="hidden" id="app_id" value="<?php echo $data['Id']; ?>">
            <div class="pull-left"><h3>应用介绍：</h3></div>
            <div class="info-edit pull-right">编辑</div>
            <div class="clear"></div>
            <div id="app_info">
                <?php echo $data['AppInfo']; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!empty($aReply)): ?>
        <div class="comment-list" id="comment_list">
            <h3>评论(<span id="comment_count"><?php echo count($aReply)?></span>)：<a class="btn btn-danger" id="batch_delete" href="javascript:;" ><i class="icon-trash icon-white"></i> 批量删除</a></h3>
            <?php foreach ($aReply as $reply): ?>
                <div class="comment" id="commentInfo_<?php echo $reply['Id']; ?>" _id="<?php echo $reply['Id']; ?>">
                    <div class="comment-info">
                        <div class="pull-left">标题：<?php echo $reply['Title']; ?></div>
                        <div class="comment-delete pull-right" _id="<?php echo $reply['Id']; ?>">删除</div>
                        <div class="comment-edit pull-right" _id="<?php echo $reply['Id']; ?>">编辑</div>
                        <div class="clear"></div>
                        <div class="pull-left">发表于：<?php echo $reply['UpdateTime']; ?></div>
                        <div class="clear"></div>
                    </div>
                    <div class="comment-content">
                        <p id="comment_<?php echo $reply['Id']; ?>"><?php echo $reply['Content']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="modal hide fade user-edit-modal" id="modal_comment_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close pull-right"
                            data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title" id="myModalLabel">
                        评论内容
                    </h3>
                </div>
                <div class="modal-body">
                    <textarea id="comment_content"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" id="save_comment" class="btn btn-primary">保存</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>