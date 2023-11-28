$(function()
{
    // 计费方式切换
    $('form.am-form input[name=valuation]').on('click', function()
    {
        var $this = $(this);
        var valuation = parseInt($this.val());
        if($(this).parents('.am-form-group').attr('data-value') != valuation)
        {
            AMUI.dialog.confirm({
                title: '温馨提示',
                content: '切换计价方式后，所设置当前模板的运输信息将被清空，确定继续么？',
                onConfirm: function(e)
                {
                    // 内容
                    var valuation_unit = ['件', 'kg', 'm³'];
                    var unit = valuation_unit[valuation] || null;
                    if(unit == null)
                    {
                        Prompt('配置有误');
                        return false;
                    }

                    $this.parents('.am-form-group').attr('data-value', valuation);
                    var thead = '<tr>';
                        thead += '<th>运送到<span class="am-form-group-label-tips-must">*</span></th>';
                        thead += '<th>首件数('+unit+')<span class="am-form-group-label-tips-must">*</span></th>';
                        thead += '<th>首费(元)<span class="am-form-group-label-tips-must">*</span></th>';
                        thead += '<th>续件数('+unit+')<span class="am-form-group-label-tips-must">*</span></th>';
                        thead += '<th>续费(元)<span class="am-form-group-label-tips-must">*</span></th>';
                        thead += '<th>订单金额满(免运费)</th>';
                        thead += '<th>操作</th>';
                        thead += '</tr>';

                    var html = '';
                    switch(valuation)
                    {
                        // 按件
                        case 0 :
                            html += '<tr>';
                            html += '<td class="first"><div class="region-td none"></div>默认运费';
                            html += '<input type="text" class="am-radius region-name" name="data[0][region]" data-validation-message="请选择地区" value="default" required data-is-clearout="0" />';
                            html += '<input type="hidden" class="am-radius region-name-show" name="data[0][region_show]" value="" data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius first-name" name="data[0][first]" min="1" max="9999999" data-validation-message="输入1~9999999的整数" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius first-price-name" name="data[0][first_price]" min="0.00" max="9999999.99" step="0.01" data-validation-message="应输入0.00~9999999.99的数字,小数保留两位" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius continue-name" name="data[0][continue]" min="1" max="9999999" data-validation-message="输入1~9999999的整数" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius continue-price-name" name="data[0][continue_price]" min="0.00" max="9999999.99" step="0.01" data-validation-message="应输入0.00~9999999.99的数字,且不能大于首费,小数保留两位" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius continue-free-shipping-price-name" name="data[0][free_shipping_price]" min="0.00" step="0.01" data-validation-message="满免运费,小数保留两位" data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td><!--operation--></td>';
                            html += '</tr>';
                            break;

                        // 按重量
                        // 按体积
                        case 1 :
                        case 2 :
                            html += '<tr>';
                            html += '<td class="first"><div class="region-td none"></div>默认运费';
                            html += '<input type="text" class="am-radius region-name" name="data[0][region]" data-validation-message="请选择地区" value="default" required data-is-clearout="0" />';
                            html += '<input type="hidden" class="am-radius region-name-show" name="data[0][region_show]" value="" data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius first-name" name="data[0][first]" min="0.01" max="9999999.0" step="0.1" data-validation-message="输入0.1~9999999.9的数字,小数保留1位" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius first-price-name" name="data[0][first_price]" min="0.00" max="9999999.99" step="0.01" data-validation-message="应输入0.00~9999999.99的数字,小数保留两位" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius continue-name" name="data[0][continue]" min="0.01" max="9999999.0" step="0.1"  data-validation-message="输入0.1~9999999.9的数字,小数保留1位" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius continue-price-name" name="data[0][continue_price]" min="0.00" max="9999999.99" step="0.01" step="0.01" data-validation-message="应输入0.00~9999999.99的数字,且不能大于首费,小数保留两位" required data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" class="am-radius continue-free-shipping-price-name" name="data[0][free_shipping_price]" min="0.00" step="0.01" data-validation-message="满免运费,小数保留两位" data-is-clearout="0" />';
                            html += '</td>';
                            html += '<td><!--operation--></td>';
                            html += '</tr>';
                            break;

                        default :
                            Prompt('配置有误');
                    }
                    $('.freightfee-rules table.am-table thead').html(thead);
                    $('.freightfee-rules table.am-table tbody').html(html);
                },
                onCancel: function()
                {
                    $('form.am-form input[name=valuation]').eq(valuation).uCheck('uncheck');
                    $('form.am-form input[name=valuation]').eq(valuation == 1 ? 0 : 1).uCheck('check');
                    $this.parents('.am-form-group').attr('data-value', valuation == 1 ? 0 : 1);
                }
            });
        }
    });

    // 元素添加
    $('.rules-submit-add').on('click', function()
    {
        // 唯一索引
        var index = parseInt(Math.random()*1000001);

        // 元素html
        var html = $('.freightfee-rules table.am-table').find('tbody tr:first').prop('outerHTML');
        if(html.indexOf('默认运费') >= 0)
        {
            html = html.replace(/默认运费/ig, '<a href="javascript:;" class="am-text-primary line-edit" data-index="'+index+'">添加地区</a>');
        }
        if(html.indexOf('<!--operation-->') >= 0)
        {
            html = html.replace(/<!--operation-->/ig, '<a href="javascript:;" class="am-text-danger line-remove">删除</a>');
        }
        $('.freightfee-rules table.am-table').append(html);

        // 值赋空
        $('.freightfee-rules table.am-table').find('tbody tr:last').find('input').each(function(k, v)
        {
            $(this).attr('value', '');
        });
        $('.freightfee-rules table.am-table').find('tbody tr:last .region-td').text('').addClass('none');

        // 移除原来的class新增新的class
        $('.freightfee-rules table.am-table').find('tbody tr:last').removeClass().addClass('data-list-'+index);

        // name名称设置
        $('.freightfee-rules table.am-table').find('tbody tr:last .region-name').attr('name', 'data['+index+'][region]');
        $('.freightfee-rules table.am-table').find('tbody tr:last .region-name-show').attr('name', 'data['+index+'][region_show]');
        $('.freightfee-rules table.am-table').find('tbody tr:last .first-name').attr('name', 'data['+index+'][first]');
        $('.freightfee-rules table.am-table').find('tbody tr:last .first-price-name').attr('name', 'data['+index+'][first_price]');
        $('.freightfee-rules table.am-table').find('tbody tr:last .continue-name').attr('name', 'data['+index+'][continue]');
        $('.freightfee-rules table.am-table').find('tbody tr:last .continue-price-name').attr('name', 'data['+index+'][continue_price]');
        $('.freightfee-rules table.am-table').find('tbody tr:last .continue-free-shipping-price-name').attr('name', 'data['+index+'][free_shipping_price]');
    });

    // 行移除
    $(document).on('click', '.freightfee-rules table.am-table .line-remove', function()
    {
        $(this).parents('tr').remove();
    });

    // 地区编辑
    $(document).on('click', '.freightfee-rules table.am-table .line-edit', function()
    {
        var index = $(this).data('index');
        $('#freightfee-region-popup').modal();
        $('#freightfee-region-popup').attr('data-index', index);

        // 清除选中
        $('#freightfee-region-popup').find('.province-name').removeClass('selected').removeClass('selected-may');
        $('#freightfee-region-popup').find('.city-name').parent('li').removeClass('selected');

        // 地区选中
        var ids = $('.data-list-'+index).find('td.first input').val() || null;
        
        if(ids != null)
        {
            var ids_all = ids.split('-');
            for(var i in ids_all)
            {
                $('.region-node-'+ids_all[i]).parent('li').addClass('selected');
            }

            // 父级选择处理
            $('#freightfee-region-popup .city-list').each(function(k, v)
            {
                var items_count = $(this).find('.city-name').length;
                var selected_count = $(this).find('.selected').length;
                if(selected_count >= items_count)
                {
                    $(this).prev('.province-name').removeClass('selected-may').addClass('selected');
                } else if(selected_count > 0 && selected_count < items_count)
                {
                    $(this).prev('.province-name').removeClass('selected').addClass('selected-may');
                } else {
                    $(this).prev('.province-name').removeClass('selected-may').removeClass('selected');
                }
            });
        }
    });

    // 地区选择事件 - 省
    $('#freightfee-region-popup .province-name').on('click', function()
    {
        if($(this).hasClass('selected-may') || $(this).hasClass('selected'))
        {
            $(this).next('.city-list').find('li').removeClass('selected');
            $(this).removeClass('selected-may').removeClass('selected');
        } else {
            $(this).next('.city-list').find('li').addClass('selected');
            $(this).addClass('selected');
        }
    });

    // 地区选择事件 - 城市
    $('#freightfee-region-popup .city-name').on('click', function()
    {
        if($(this).parent('li').hasClass('selected'))
        {
            $(this).parent('li').removeClass('selected');
        } else {
            $(this).parent('li').addClass('selected');
        }

        // 父级处理
        var items_count = $(this).parents('.city-list').find('.city-name').length;
        var selected_count = $(this).parents('.city-list').find('.selected').length;
        if(selected_count >= items_count)
        {
            $(this).parents('.city-list').prev('.province-name').removeClass('selected-may').addClass('selected');
        } else if(selected_count > 0 && selected_count < items_count)
        {
            $(this).parents('.city-list').prev('.province-name').removeClass('selected').addClass('selected-may');
        } else {
            $(this).parents('.city-list').prev('.province-name').removeClass('selected-may').removeClass('selected');
        }
    });

    // 地区选择确认
    $('#freightfee-region-popup button[type="submit"]').on('click', function()
    {
        var name_all = [];
        var ids_all = [];
        var show_ids_all = [];
        var city_index = 0;
        var province_index = 0;
        var province_id = 0;
        $('#freightfee-region-popup .city-list li').each(function(k, v)
        {
            if($(this).parent('.city-list').prev('.province-name').hasClass('selected'))
            {
                var temp_province_id = $(this).parent('.city-list').prev('.province-name').data('id');
                console.log(temp_province_id)
                if(province_id != temp_province_id)
                {
                    province_id = temp_province_id;
                    name_all[province_index] = $(this).parent('.city-list').prev('.province-name').text();
                    show_ids_all[province_index] = temp_province_id;
                    province_index++;
                }
            } else {
                if($(this).hasClass('selected'))
                {
                    name_all[province_index] = $(this).find('.city-name').text();
                    show_ids_all[province_index] = $(this).find('.city-name').data('city-id');
                    province_index++;
                }
            }
            if($(this).hasClass('selected'))
            {
                ids_all[city_index] = $(this).find('.city-name').data('city-id');
                city_index++;
            }
        });
        var $content = $('.data-list-'+$('#freightfee-region-popup').attr('data-index')+' .region-td');
        $content.text(name_all.join('、'));
        if(name_all.length > 0)
        {
            $content.removeClass('none');
        } else {
            $content.addClass('none');
        }
        
        $('.data-list-'+$('#freightfee-region-popup').attr('data-index')+' td.first input.region-name').val(ids_all.join('-'));
        $('.data-list-'+$('#freightfee-region-popup').attr('data-index')+' td.first input.region-name-show').val(show_ids_all.join('-'));

        $('#freightfee-region-popup').modal('close');
    });


    // 添加元素到右侧
    function RightElementAdd(value, name)
    {
        if($('.forth-selection-container ul.ul-right').find('.items-li-'+value).length == 0)
        {
            var html = '<li class="am-animation-slide-bottom items-li-'+value+'"><span class="name" data-value="'+value+'">'+name+'</span><i class="am-icon-trash-o am-fr"></i></li>';
            $('.forth-selection-container ul.ul-right').append(html);
        }

        // 右侧数据同步
        RightElementGoods();

        // 左侧是否还有内容
        if($('.forth-selection-container ul.ul-left li').length == 0)
        {
            $('.forth-selection-container ul.ul-left .table-no').removeClass('none');
        } else {
            $('.forth-selection-container ul.ul-left .table-no').addClass('none');
        }
    }

    // 批量-商品id同步
    function RightElementGoods()
    {
        var value_all = [];
        $('.forth-selection-container ul.ul-right li').each(function(k, v)
        {
            value_all[k] = $(this).find('span.name').data('value');
        });
        $('.forth-selection-container input[name="goods_ids"]').val(value_all.join(',')).blur();

        // 右侧是否还有数据
        if($('.forth-selection-container ul.ul-right li').length == 0)
        {
            $('.forth-selection-container ul.ul-right .table-no').removeClass('none');
        } else {
            $('.forth-selection-container ul.ul-right .table-no').addClass('none');
        }
    }
    // 左侧点击到右侧
    $('.forth-selection-container ul.ul-left').on('click', 'i.am-icon-angle-right', function()
    {
        var value = $(this).prev().data('value');
        var name = $(this).prev().text();
        $(this).parent().remove();
        RightElementAdd(value, name);
    });

    // 左侧全部移动到右侧
    $('.forth-selection-container .selected-all').on('click', function()
    {
        $('.forth-selection-container ul.ul-left li').each(function(k, v)
        {
            var value = $(this).find('span.name').data('value');
            var name = $(this).find('span.name').text();
            $(this).remove();
            RightElementAdd(value, name);
        });
    });

    // 右侧删除
    $('.forth-selection-container ul.ul-right').on('click', 'i.am-icon-trash-o', function()
    {
        $(this).parent().remove();
        RightElementGoods();
    });

    // 商品搜索
    $('.forth-selection-form .search-submit').on('click', function()
    {
        var category_id = $('.forth-selection-form .forth-selection-form-category').val();
        var keywords = $('.forth-selection-form .forth-selection-form-keywords').val();
        console.log(category_id, keywords)

        // ajax请求
        $.ajax({
            url:$('.forth-selection-form').data('search-url'),
            type:'POST',
            dataType:"json",
            timeout:10000,
            data:{"category_id": category_id, "keywords": keywords},
            success:function(result)
            {
                if(result.code == 0)
                {
                    var html = '';
                    for(var i in result.data)
                    {
                        html += '<li class="am-animation-slide-bottom"><span class="name" data-value="'+result['data'][i]['id']+'">'+result['data'][i]['title']+'</span><i class="am-icon-angle-right am-fr"></i></li>';
                    }
                    $('ul.ul-left .table-no').addClass('none');
                    $('ul.ul-left li').remove();
                    $('ul.ul-left').append(html);
                } else {
                    Prompt(result.msg);
                }
            },
            error:function()
            {
                Prompt('网络异常错误');
            }
        });
    });

    // 特定商品分类运费添加
    $('.goods-category-append-submit').on('change', function()
    {
        var value = $(this).val() || null;
        if(value != null && $('.goods-category-append-list li.data-item-'+value).length == 0)
        {
            var name = $(this).find('option:selected').data('name');
            var html = `<li class="am-nbfc am-padding-xs data-item-`+value+`">
                <input type="hidden" name="goods_category_append[`+value+`][id]" value="`+value+`" />
                <input type="hidden" name="goods_category_append[`+value+`][name]" value="`+name+`" />
                <p class="name am-fl am-text-truncate am-padding-vertical-xs">`+name+`</p>
                <input type="number" name="goods_category_append[`+value+`][price]" step="0.01" min="0" placeholder="额外增加运费" value="" class="am-radius am-fl" />
                <input type="text" name="goods_category_append[`+value+`][icon]" placeholder="显示名称" value="`+name+`" class="am-radius am-fl am-margin-left-xs" />
                <button type="button" class="am-close am-fr">&times;</button>
            </li>`;
            $('.goods-category-append-list').append(html);
        }
    });
    // 特定商品分类运费移除
    $(document).on('click', '.goods-category-append-list li button.am-close', function()
    {
        $(this).parent().remove();
    });
});