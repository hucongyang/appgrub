window.appgrubBootstrapModal = function(params) {
    var default_config = {
        id: 'bootstrap_modal_' + (+ new Date()),
        fade: true,
        css: {},
        title: '',
        content_wrap_class: '',
        content: '',
        show_cancel: true,
        cancel_btn_text: '关闭',
        ok_btn_text: '保存',
        options: {
            backdrop: true,          //为模态对话框添加一个背景元素。另外，为背景指定static时，点击模态对话框的外部区域不会将其关闭。
            keyboard: true,          //按下esc键时关闭模态对话框
            show: true,              //初始化时即显示模态对话框
            remote: false           //如果提供了远程url地址
        },
        callback: {
            show: null,
            shown: null,
            hide: null,
            hidden: function(){
                modal_obj.remove();
            },
            cancel: null,
            ok: null
        }
    };

    var config = $.extend(true, {}, default_config, params);

    var modal_obj = $('<div class="modal hide" id="'+config.id+'"/>').css(config.css);
    if (config.fade) {
        modal_obj.addClass('fade');
    }
    var modal_html = '';
    modal_html += '<div class="modal-header">';
    modal_html += '<button type="button" class="close cancel" data-dismiss="modal" aria-hidden="true">&times;</button>';
    modal_html += '<h3>' + config.title + '</h3>';
    modal_html += '</div>';

    modal_html += '<div class="modal-body">';
    modal_html += '<div ' + config.content_wrap_class + '>' + config.content + '</div>';
    modal_html += '</div>';

    modal_html += '<div class="modal-footer">';
    if(config.show_cancel){
        modal_html += '<a href="javascript:;" class="btn cancel" data-dismiss="modal" aria-hidden="true">' + config.cancel_btn_text + '</a>';
    }
    modal_html += '<a href="javascript:;" class="btn btn-primary ok">' + config.ok_btn_text + '</a>';
    modal_html += '</div>';

    modal_obj.html(modal_html).appendTo('body').modal(config.options);

    /**
    show    当show方法被调用时，此事件将被立即触发。
    shown   当模态对话框呈现到用户面前时（会等待过渡效果执行结束）此事件被触发。
    hide    当hide方法被调用时，此事件被立即触发。
    hidden  当模态对话框被隐藏（而且过渡效果执行完毕）之后，此事件将被触发。
    */

    modal_obj.on('show', function(){
        config.callback.show && config.callback.show();
    }).on('shown', function(){
        config.callback.shown && config.callback.shown();
    }).on('hide', function(){
        config.callback.hide && config.callback.hide();
    }).on('hidden', function(){
        config.callback.hidden && config.callback.hidden();
    }).on('click', '.cancel', function(){
        config.callback.cancel && config.callback.cancel();
    }).on('click', '.ok', function(){
        config.callback.ok && config.callback.ok();
    });

    return modal_obj;
};