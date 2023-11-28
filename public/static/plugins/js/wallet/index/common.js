// 付款码获取展示
var scheduled_timer = null;
var $payment_code_modal = $('#modal-payment-code');
$payment_code_modal.on('close.modal.amui', function()
{
    clearInterval(scheduled_timer);
});

/**
 * 付款码
 * @author  Devil
 * @blog    http://gong.gg/
 * @version 1.0.0
 * @date    2023-10-23
 * @desc    description
 * @param   {Boolean}       is_modal [是否打开弹窗]
 */
function PluginsWalletPaymentCode(is_modal = true)
{
    clearInterval(scheduled_timer);
    $.ajax({
        url: $payment_code_modal.data('url'),
        type: 'post',
        dataType: "json",
        timeout: 10000,
        data: {},
        success: function(result)
        {
            if(result.code == 0)
            {
                var time = parseInt(result.data.time || 30);
                scheduled_timer = setInterval(function()
                {
                    if(time <= 1) {
                        PluginsWalletPaymentCode(false);
                    } else {
                        time--;
                        $payment_code_modal.find('.scheduled-value').text(time);
                    }
                }, 1000);

                // 条码和二维码
                JsBarcode('#payment-barcode', result.data.code);
                $('#payment-qrcode').empty().qrcode({text: result.data.code, width: 180, height: 180});
                // 打开弹窗
                if(is_modal)
                {
                    $payment_code_modal.modal({
                        width: 260,
                        height: 430,
                    });
                }
                $payment_code_modal.find('.scheduled-value').text(time);
                $payment_code_modal.find('.error-tips').addClass('am-hide');
                $payment_code_modal.find('.payment-code-content').removeClass('am-hide');
            } else {
                $payment_code_modal.find('.error-tips').removeClass('am-hide').text(result.msg);
                $payment_code_modal.find('.payment-code-content').addClass('am-hide');
            }
        },
        error: function(xhr, type)
        {
            var msg = HtmlToString(xhr.responseText) || '异常错误';
            $payment_code_modal.find('.error-tips').removeClass('am-hide').text(msg);
            $payment_code_modal.find('.payment-code-content').addClass('am-hide');
        }
    });
}

/**
 * 收款用户搜索
 * @author  Devil
 * @blog    http://gong.gg/
 * @version 1.0.0
 * @date    2023-10-23
 * @desc    description
 */
function PluginsWalletTransferUserQuery()
{
    var $transfer = $('.transfer-input-container');
    var value = $transfer.find('input').val() || null;
    if(value == null)
    {
        Prompt('请输入收款用户');
        return false;
    }

    $.ajax({
        url: $transfer.data('ajax-url'),
        type: 'post',
        dataType: "json",
        timeout: 10000,
        data: {keywords: value},
        success: function(result)
        {
            var $user_query = $('.user-query-tips');
            var $user_error = $user_query.find('.error-msg');
            var $receive_user_info = $user_query.find('.receive-user-info');
            var $input = $user_query.find('input[name="receive_user_id"]');
            if(result.code == 0)
            {
                $user_error.addClass('am-hide');
                $receive_user_info.removeClass('am-hide');
                $receive_user_info.find('img').attr('src', result.data.avatar);
                $receive_user_info.find('span').text(result.data.user_name_view);
                $input.val(result.data.id);
            } else {
                $user_error.removeClass('am-hide').text(result.msg);
                $receive_user_info.addClass('am-hide');
                $input.val('');
                Prompt(result.msg);
            }
        },
        error: function(xhr, type)
        {
            Prompt(HtmlToString(xhr.responseText) || '异常错误');
        }
    });
}


$(function()
{
    // 表单初始化
    // 转账
    if($('form.form-validation-plugins-transfer-modal').length > 0)
    {
        FromInit('form.form-validation-plugins-transfer-modal');
    }
    // 充值
    if($('form.form-validation-plugins-recharge-modal').length > 0)
    {
        FromInit('form.form-validation-plugins-recharge-modal');
    }
    // 支付
    if($('form.form-validation-plugins-recharge-pay-popup').length > 0)
    {
        FromInit('form.form-validation-plugins-recharge-pay-popup');
    }

    // 查看付款码
    $(document).on('click', '.payment-code-submit', function()
    {
        PluginsWalletPaymentCode();
    });
    $(document).on('input', '.recharge-price-input input', function() {
        var value = $(this).val();
        var self = $('.recharge-price-list');
        self.find('.recharge-price-value').each((index,item) => {
            self.eq(index).removeClass('active');
            if($(item).text() === value) {
                self.eq(index).addClass('active').siblings().removeClass('active');
            }
        });
    });

    // 打开充值窗口
    $(document).on('click', '.recharge-submit', function()
    {
        $('.recharge-price-input input').val('');
        $('.recharge-price-list').removeClass('active');
    });

    // 充值金额选择事件
    $(document).on('click', '.recharge-price-list', function()
    {
        $(this).addClass('active').siblings().removeClass('active');
        $('.recharge-price-input input').val($(this).data('original-price'));
    });

    // 充值用户查询事件
    $(document).on('blur', '.transfer-input-container input', function()
    {
        PluginsWalletTransferUserQuery();
    });
    $(document).on('keydown', '.transfer-input-container input', function()
    {
        if(event.keyCode == 13)
        {
            PluginsWalletTransferUserQuery();
            return false;
        }
    });
    $(document).on('click', '.transfer-input-container button', function()
    {
        PluginsWalletTransferUserQuery();
    });
    // 用户输入事件值改变，空则隐藏查询的用户信息
    $(document).on('change', '.transfer-input-container input', function()
    {
        if($(this).val() == '')
        {
            var $user_query = $('.user-query-tips');
            $user_query.find('.error-msg').addClass('am-hide');
            $user_query.find('.receive-user-info').addClass('am-hide');
            $user_query.find('input[name="receive_user_id"]').val('');
        }
    });
});