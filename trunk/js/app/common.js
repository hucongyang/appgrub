/**
 * Created by admin on 2015/3/24.
 */
$(function() {
    var appgrub_common_params = {

    };
    var appgrub_common = window.appgrub_common = {
        //点赞
        up: function($obj) {
            if(!$obj) {
                return;
            }
            var app_id = $obj.attr('_id');
            var url, num;
            if($obj.hasClass('link')) {
                url = '/produce/dislike';
                num = (+$obj.find('.like-num').text()) - 1;
            } else {
                url = '/produce/like';
                num = (+$obj.find('.like-num').text()) + 1;
            }
            window.appgrubAjax.request(url, function(data) {
                $obj.toggleClass('link');
                $('.isLiked[_id="'+app_id+'"]').each(function(){
                    $(this).find('.like-num').text(num);
                })
            }, {
                'id': app_id
            }, 'post');
            return;
        },
        favorite: function(appID) {
            window.appgrubAjax.request('/produce/favorite', function(data) {
                $('#favorite-' + appID).attr("title", "已收藏").find('.collection').addClass('collectioned');
            }, {
                'id': appID
            }, 'post');
        },
        unfavorite: function(appID) {
            window.appgrubAjax.request('/produce/unfavorite', function(data) {
                $('#favorite-' + appID).attr("title", "收藏").find('.collection').removeClass('collectioned');
            }, {
                'id': appID
            }, 'post');
        },
        //收藏
        collection: function(appID) {
            if ($('#favorite-' + appID).length && $('#favorite-' + appID).find('.collection').length) {
                if ($('#favorite-' + appID).find('.collection').hasClass('collectioned')) {
                    this.unfavorite(appID);
                } else {
                    this.favorite(appID);
                }
            }
            return;
        }
    };
});