<style>
    body{background-color: #fff;}
</style>
<?php 
$host = Yii::app()->request->hostInfo;
?>

<div class="error-container">
    <div class="error-wrap">
        <div class="error-title">
            <p>
                <span class="an-error"></span>
                <?php echo $title; ?>
                <span class="ios-error"></span>
            </p>
        </div>
        <div class="error-content"><?php echo $message; ?></div>
    </div>
</div>
<script>
$(function(){
    size();
});
$(window).resize(function(){
    size();
});
function size(){
    $('html,body').height('100%');
    var h = $('html').height() - $('#header').height() - $('#footer').outerHeight();
    if($('.error-wrap').height() + 100 > h){
       $('.error-container').height($('.error-wrap').height() + 100); 
    }else{
       $('.error-container').height(h); 
    }
    
    $('.error-wrap').css({
        'margin-top':-$('.error-wrap').height()/2,
        //'margin-left':-$('.error').width()/2
    });
}
</script>