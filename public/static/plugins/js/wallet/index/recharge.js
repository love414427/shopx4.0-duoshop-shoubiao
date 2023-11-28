$(function()
{
    // 充值列表支付发起事件
    $(document).on('click', '.pay-submit', function()
    {
        var recharge_id = $(this).data('id') || null;
        var recharge_no = $(this).data('order-no') || null;
        var price = $(this).data('price') || null;
        var $popup = $('#plugins-wallet-recharge-pay-popup');
        if(recharge_id != null && recharge_no != null && price != null)
        {
            $popup.find('.business-item ul li').removeClass('selected');
            $popup.find('input[name="payment_id"]').val('');
            $popup.find('input[name="recharge_id"]').val(recharge_id);
            $popup.find('.base .recharge-no').text(recharge_no);
            $popup.find('.base .price').text(__currency_symbol__+price);
            $popup.modal('open');
        } else {
            Prompt('充值参数有误');
        }
    });

    // 自动支付处理
    if($('.pay-submit-auto-event').length > 0 && ($('.pay-submit-auto-event').data('payment-id') || null) != null)
    {
        var payment_id = $('.pay-submit-auto-event').data('payment-id') || 0;
        $('.pay-submit-auto-event').trigger('click');
        var $popup = $('#plugins-wallet-recharge-pay-popup');
        $('.payment-document-'+payment_id).addClass('selected');
        $popup.find('input[name="payment_id"]').val(payment_id);
        $popup.find('form.am-form button[type="submit"]').trigger('click');
    }
});