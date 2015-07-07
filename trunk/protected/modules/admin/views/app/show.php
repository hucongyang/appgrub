<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/iCheck/skins/flat/blue.css" rel="stylesheet">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.icheck.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/app/admin/show.js"></script>
<div class="container panel">        
    <legend>查询</legend>

    <form class="form-horizontal">
        <div class="control-group">
             <label class="control-label" for="os">操作系统：</label>
            <div class="controls">
                <div>
                <input type="radio" class="icheck-radio" id="os_all" name="os" checked value=""><label class="icheck-label" for="os_all">&nbsp;全部&nbsp;&nbsp;</label>
                <input type="radio" class="icheck-radio" id="os_ios" name="os" value="IOS"><label class="icheck-label" for="os_ios">&nbsp;IOS&nbsp;&nbsp;</label>
                <input type="radio" class="icheck-radio" id="os_android" name="os" value="Android"><label class="icheck-label" for="os_android">&nbsp;Android&nbsp;&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="control-group">
             <label class="control-label" for="order">排序：</label>
            <div class="controls">
                <input type="radio" class="icheck-radio" id="order_download" name="order" value="DownLoadNum" checked><label class="icheck-label" for="order_download">&nbsp;下载量&nbsp;&nbsp;</label>
                <input type="radio" class="icheck-radio" id="order_comment" name="order" value="CommentNum"><label class="icheck-label" for="order_comment">&nbsp;评论数&nbsp;&nbsp;</label>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                 <a class="btn btn-primary" id="search" href="javascript:;"><i class="icon-search icon-white"></i> 查询</a>
            </div>
        </div>
    </form>
    
    <div class="panel cut-off-line"></div>

    <div class="panel">
        <div class="opt-btn pull-left"><a class="btn btn-danger" id="delete" href="javascript:;" ><i class="icon-trash icon-white"></i> 删除</a></div>
        <div class="opt-btn pull-left"><a class="btn btn-success" id="add" href="javascript:;"><i class="icon-plus icon-white"></i> 添加</a></div>
        <div class="opt-btn pull-left"><a class="btn btn-inverse" href="/admin/app/editcategory"><i class="icon-pencil icon-white"></i> 修改分类</a></div>
        <div class="clearfix"></div>
    </div>
    
    <div class="panel">
        <table id="grid" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>选项</th>
                    <th>序号</th>
                    <th>图标</th>
                    <th>名称</th>
                    <th>市场</th>
                    <th>分类</th>
                    <th>大小</th>
                    <th>操作系统</th>
                    <th>发布日期</th>
                    <th>下载量</th>
                    <th>评论数</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="grid_loading" class="grid-loading hide"></div>
    </div>
</div>
