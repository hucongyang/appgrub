<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/iCheck/skins/flat/blue.css" rel="stylesheet">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.icheck.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/app/admin/editcategory.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.bootpag.min.js"></script>
<div class="container panel edit_category">
    <legend>查询</legend>
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="appName">应用名称：</label>
            <div class="controls">
                <div>
                    <input type="text" id="app_name" name="app_name" />
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="appName">分类：</label>
            <div class="controls">
                <div>
                    <select class="category-main" id="search_main_category"><option value="0">请选择</option></select>
                    <select class="category-sub" id="search_subcategory"><option value="0">请选择</option></select>
                </div>
            </div>
        </div>
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
            <div class="controls">
                <a class="btn btn-inverse" href="/admin/app/show"><i class="icon-arrow-left icon-white"></i> 返回</a>
                <a class="btn btn-primary" id="search" href="javascript:;"><i class="icon-search icon-white"></i> 查询</a>
            </div>
        </div>
    </form>
    <div class="panel cut-off-line"></div>
    <div class="panel">
        <table id="grid" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>LOGO</th>
                <th>名称</th>
                <th>大类</th>
                <th>小类</th>
                <th>发送时间</th>
                <th>操作系统</th>
                <th>调整后分类</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="grid_loading" class="grid-loading hide"></div>
        <div class="pagination">
        </div>
    </div>
</div>
