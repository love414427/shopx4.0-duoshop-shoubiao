$(function()
{
    // 列表数据发起支付
    $('.pay-submit').on('click', function()
    {
        var id = $(this).data('id') || '';
        var order_no = $(this).data('order-no') || '';
        var price = $(this).data('price') || '';
        var $popup = $('#plugins-wallet-recharge-pay-popup');
        $popup.find('ul.payment-list li').removeClass('selected');
        $popup.find('input[name="payment_id"]').val('');
        $popup.find('input[name="recharge_id"]').val(id);
        $popup.find('.base .recharge-no').text(order_no);
        $popup.find('.base .price').text(__currency_symbol__+price);
        $popup.modal('open');
    });

    // 支付确认
    $(document).on('click', '#plugins-wallet-recharge-pay-popup button[type="submit"]', function()
    {
        var $parent = $(this).parents('form');
        var payment_id = $parent.find('input[name="payment_id"]').val() || null;
        if(payment_id == null)
        {
            Prompt('请选择支付方式');
            return false;
        }
    });
});