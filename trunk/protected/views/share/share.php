<style>
    @media(max-width:650px){
        .content{max-width:300px;margin:100px auto;padding-left:1em;}
        .pro-detail textarea{min-width:200px;}
        input[name=ios]{display:block;margin-bottom:10px;}
        select[name=android_list]{margin-left:0;}
    }
    .pro-name {
        line-height: 86px;
        background-color: white;
        overflow: hidden;
    }
</style>
<div id="container" style="margin-top: -30px;">
    <div class="content row">

    </div>
</div>
<script type="text/javascript">
    $('#url').blur(function(){
        if(! $('#url').val() == ''){
            $('#checkUrl').html('').css('color','green');
            checkUrl = true;
        }
    });
    $('#ios_url').blur(function(){
        if($('#ios_url').val() == '' && $('#android').val() == ''){
            checkIos = false;
        }else{
            $('#checkIos').html('').css('color','green');
            checkIos = true;
        }
    });
    $('#android').blur(function(){
        if($('#ios_url').val() == '' && $('#android').val() == ''){
            checkAndroid = false;
        }else{
            $('#checkAndroid').html('').css('color','green');
            checkAndroid = true;
        }
    });
    $('#detail').blur(function(){
        if(($('#detail').val().length > 40)){
            $('#checkExplain').html('描述不能超过40个字').css('color','red');
            checkExplain = false;
        }else{
            $('#checkExplain').html('').css('color','green');
            checkExplain = true;
        }
    });
    $('#appType').change(function () {
        var appType = $('#appType').val();
        if (appType == 1) {
            $('#iosArea').show();
            $('#androidArea').hide();
            $('#android').val("");
        } else if (appType == 2) {
            $('#iosArea').hide();
            $('#ios_url').val("");
            $('#androidArea').show();
        } else if (appType == 3) {
            $('#iosArea').show();
            $('#androidArea').show();
        }
    });
    $('#submit').click(function(){
        if(checkIos == true || checkAndroid == true){
            var url = $('#url').val();
            var ios = $('#ios_url').val();
            var android_list = $('#android_list').val();
            var android = $('#android').val();
            var explain = $('#detail').val();
            $.post(
                "/share/add",
                {'url': url, 'ios':ios, 'android_list':android_list, 'android':android, 'explain':explain},
                function (data) {
                    if (data['ret_code'] == 0) {
                        swal({
                          title: "添加成功",
                          type: "success",
                          showCancelButton: false,
                          confirmButtonClass: 'btn-success',
                          confirmButtonText: '确定'
                        });
                        $("input[type=reset]").trigger("click");
                        window.location.reload();
                    } else if(data['ret_code'] == 1) {
                        swal({
                          title: data['ret_msg'],
                          type: "info",
                          showCancelButton: false,
                          confirmButtonClass: 'btn-info',
                          confirmButtonText: '确定'
                        });
                    } else if(data['ret_code'] == 13) {
                         $('#checkIos').html('AppStore地址错误').css('color','red');
                    } else if(data['ret_code'] == 14) {
                        $('#checkAndroid').html('安卓市场地址错误').css('color','red');
                    } else if(data['ret_code'] == 8) {
                        $('#checkUrl').html('产品官网地址错误').css('color','red');
                    }
                },
                'json'
            );
        }
    });
</script>