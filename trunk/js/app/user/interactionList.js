$(function(){
    var user_interaction_list_params = {};
    var user_interaction_list = {
        init: function(){
            var type = $('#interaction_type').val() - 1;
            $('#interaction_change li a').removeClass('active');
            $('#interaction_change li:eq('+type+') a').addClass('active');
        }
    };
    user_interaction_list.init();
});
