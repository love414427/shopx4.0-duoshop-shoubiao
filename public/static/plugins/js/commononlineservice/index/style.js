$(function()
{
  // 在线客服展开、收起
  $('.commononlineservice .btn-open').click(function()
    {
        $('.commononlineservice .content').toggle(300);
        $('.commononlineservice .btn-open').css('display', 'none');
        $('.commononlineservice .btn-ctn').css('display', 'block');        
    });
    $('.commononlineservice .btn-ctn').click(function()
    {
        $('.commononlineservice .content').toggle(300);
        $('.commononlineservice .btn-open').css('display', 'block');
        $('.commononlineservice .btn-ctn').css('display', 'none');
    });

    // 打开客服 - 在线客服
    $('.commononlineservice-plugins-chat-entry-event').on('click', function()
    {
        // 客服地址
        var chat_url = $(this).data('chat-url');

        // 是否新窗口打开
        var is_open_win = parseInt($(this).data('is-open-new-win') || 0);
        if(is_open_win == 1)
        {
            OpenWindow(chat_url, '客服咨询', 1000, 750);
        } else {
            $('.commononlineservice-plugins-chat-popup').remove();
            var html = '<div class="commononlineservice-plugins-chat-popup">';
                html+= '<a href="javascript:;" class="close-submit">&times;</a>';
                html+= '<iframe src="'+chat_url+'"></iframe>';
                html+= '</div>';
            $('body').append(html);
            // 是否需要隐藏按钮
            if($(this).data('is-hide') == 1)
            {
                $(this).addClass('am-hide');
            }
        }
    });
    // 关闭客服 - 在线客服
    $(document).on('click', '.commononlineservice-plugins-chat-popup .close-submit', function()
    {
        $(this).parent().remove();
        if($('.commononlineservice-plugins-chat-entry-event').hasClass('am-hide'))
        {
            $('.commononlineservice-plugins-chat-entry-event').removeClass('am-hide');
        }
    });
});