$(function()
{
    // 分享事件
    $(document).on('click', '.plugins-share ul li', function()
    {
        // 分享类型
        var type = $(this).data('type');

        // 来源站点
        var site = 'shopxo';

        // url
        var url = encodeURIComponent(share_url);
        // 是否自定义url地址
        var cu_url = $(this).parents('.plugins-share').parent().data('url') || null;
        if(cu_url != null)
        {
            url = encodeURIComponent(cu_url);
        }

        // 标题
        var title = encodeURIComponent(share_title);
        // 是否自定义url地址
        var cu_title = $(this).parents('.plugins-share').parent().data('title') || null;
        if(cu_title != null)
        {
            title = encodeURIComponent(cu_title);
        }

        // 描述
        var desc = encodeURIComponent(share_desc);
        // 是否自定义url地址
        var cu_desc = $(this).parents('.plugins-share').parent().data('desc') || null;
        if(cu_desc != null)
        {
            desc = encodeURIComponent(cu_desc);
        }

        // 封面图
        var pic = encodeURIComponent(share_pic);
        // 是否自定义了封面图片
        var cu_pic = $(this).parents('.plugins-share').parent().data('pic') || null;
        if(cu_pic != null)
        {
            pic = encodeURIComponent(cu_pic);
        }

        // 平台地址
        var platform_url = null;

        // 当前环境
        var env = MobileBrowserEnvironment();
        
        // 关闭弹层
        $('#plugins-share-layer').hide();

        // 根据分享类型处理
        switch(type)
        {
            // QQ
            case 'qq' :
                if(env == 'qq' || env == 'weixin' || env == 'qzone' || env == 'weibo')
                {
                    $('#plugins-share-layer').show();
                } else {
                    platform_url = 'https://connect.qq.com/widget/shareqq/index.html?url='+url+'&utm_medium=qqim&title='+title+'&desc='+desc+'&pics='+pic+'&site='+site
                }
                break;

            // QQ空间
            case 'qzone' :
                if(env == 'qq' || env == 'weixin' || env == 'weibo')
                {
                    $('#plugins-share-layer').show();
                } else {
                    platform_url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+url+'&utm_medium=qzone&title='+title+'&desc='+desc+'&pics='+pic+'&summary='+desc+'&site='+site;
                }
                break;

            // 新浪微博
            case 'sina' :
                platform_url = 'http://service.weibo.com/share/share.php?url='+url+'&utm_medium=sina&title='+title+'&desc='+desc+'&pics='+pic+'&site='+site;
                break;

            // Twitter
            case 'twitter' :
                platform_url = 'https://twitter.com/intent/tweet?url='+url+'&utm_medium=twitter&text='+title+desc+'&images='+pic+'&site='+site;
                break;

            // Facebook
            case 'facebook' :
                platform_url = 'https://www.facebook.com/sharer/sharer.php?u='+url+'&utm_medium=facebook&title='+title+'&desc='+desc+'&images='+pic+'&site='+site;
                break;

            // 微信
            case 'weixin' :
                if(env == 'qq' || env == 'weixin' || env == 'qzone' || env == 'weibo')
                {
                    $('#plugins-share-layer').show();
                } else {
                    var $modal = $('#plugins-share-weixin-modal');
                    $modal.find('.weixin-qrcode').empty().qrcode({
                        text: decodeURIComponent(url),
                        width: 200,
                        height: 200
                    });
                    $modal.modal({width: 260});
                    $modal.modal('open');
                }
                break;

            // url
            case 'url' :
                var $modal = $('#plugins-share-copy-modal');
                $modal.find('.am-input-group input').val(decodeURIComponent(url));
                $modal.modal({width: 300});
                $modal.modal('open');
                break;
        }
        
        // 跳转
        if(platform_url != null)
        {
            window.open(platform_url);  
        }
    });

    // url复制
    var clipboard = new ClipboardJS('#plugins-share-copy-modal .am-input-group .am-input-group-label',
    {
        text: function()
        {
            return $('#plugins-share-copy-modal .am-input-group input').val();
        }
    });
    clipboard.on('success', function(e)
    {
        Prompt('复制成功', 'success');
    });
    clipboard.on('error', function(e)
    {
        Prompt('复制失败，请手动复制！');
    });

    // 分享提示弹层关闭
    $('#plugins-share-layer').on('click', function()
    {
        $('#plugins-share-layer').hide();
    });


    // 分享组建初始化
    if($('.plugins-share-container').length > 0)
    {
        // 标签初始化
        if($('.plugins-share-view').length > 0)
        {
            // 循环处理每个节点
            $('.plugins-share-view').each(function(k, v)
            {
                // 获取指定分享项
                var html = '';
                var share = $(this).data('share') || null;

                // 未指定则全部
                if(share == null)
                {
                    html = $('.plugins-share-container').html();
                } else {
                    share = share.split(',');
                    if(share.length > 0)
                    {
                        html += '<div class="plugins-share"><ul>';
                        for(var i in share)
                        {
                            html += $('.plugins-share-container').find('ul li.share-'+share[i]).prop('outerHTML');
                        }
                        html += '</ul></div>';
                    }
                }
                $(this).html(html);
            });
        }
    }
});