<script src="<?php echo Yii::app()->request->baseUrl;?>/js/app/mobile/mobile-share.js"></script>

<div class="mobile-share-app">
    <div class="mobile-share-header">
        <h4>分享新App</h4>
    </div>
    <div class="mobile-share-body">
        <div class="share-input">
            <label for="appUrl">App链接：（<a href="/static/share<?php echo $mobile_type;?>.html">如何填写App链接</a>）</label>
            <input type="url" id="appUrl" class="info mobile-app-info span12" name="appUrl" placeholder="请输入App链接" required="required" aria-required="true"/>
            <span id="checkUrl" class="help-inline" ></span>
        </div>
        <div class="share-input">
            <label for="url" style="width:200px;">App官网：<span style="color:#888;">（可不填）</span></label>
            <input type="url" id="url" class="info span12" name="url" placeholder="请输入App的官方网址" />
        </div>
        <div class="share-input">
            <label for="detail">一句话描述这个App：<span style="color:#888;">（可不填）</span></label>
            <textarea  id="detail" name="explain" cols="20" rows="4" class="span12"></textarea>
            <small>请精简描述，40字以内</small>
            <br>
        </div>
    </div>
    <div class="mobile-share-footer pull-left">
        <button class="btn" data-dismiss="modal" aria-hidden="true" id="submitCancel">取消</button>
        <button class="btn btn-primary" type="submit" id="shareSubmit">发布</button>
    </div>
</div>

