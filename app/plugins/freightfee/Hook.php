<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2099 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/licenses/mit-license.php )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\plugins\freightfee;

use think\facade\Db;
use app\service\PluginsService;
use app\service\ResourcesService;
use app\service\GoodsCategoryService;
use app\plugins\freightfee\service\BaseService;

/**
 * 运费设置 - 钩子入口
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Hook
{
    /**
     * 应用响应入口
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-09T14:25:44+0800
     * @param    [array]          $params [输入参数]
     */
    public function handle($params = [])
    {
        if(!empty($params['hook_name']))
        {
            switch($params['hook_name'])
            {
                // css
                case 'plugins_css' :
                    $ret = 'static/plugins/css/freightfee/index/style.css';
                    break;

                // 运费计算
                case 'plugins_service_buy_group_goods_handle' :
                    $ret = $this->FreightFeeCalculate($params);
                    break;

                // 商品免运费icon
                case 'plugins_view_goods_detail_title' :
                    $ret = $this->FreeShippingGoodsIcon($params);
                    break;

                default :
                    $ret = '';
            }
            return $ret;
        }
    }

    /**
     * 商品免运费icon
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-04-26
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    private function FreeShippingGoodsIcon($params = [])
    {
        $goods_ids = Db::name('PluginsFreightfeeTemplate')->where(['is_enable'=>1])->column('goods_ids');
        $ret = PluginsService::PluginsData('freightfee');
        if(!empty($goods_ids) && !empty($params['goods_id']))
        {
            foreach($goods_ids as $ids)
            {
                if(!empty($ids))
                {
                    if(in_array($params['goods_id'], explode(',', $ids)))
                    {
                        return '<span class="am-badge am-badge-success-plain am-radius am-vertical-align-middle">免运费</span>';
                    }
                }
            }
        }
        return '';
    }

    /**
     * 运费计算
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-03-21
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public function FreightFeeCalculate($params = [])
    {
        if(!empty($params['data']))
        {
            // 获取运费模板
            $fee_template = BaseService::DataHandle(Db::name('PluginsFreightfeeTemplate')->where([
                'warehouse_id'  => array_column($params['data'], 'id'),
                'is_enable'     => 1,
            ])->select()->toArray());
            if(!empty($fee_template))
            {
                // 运费模板分组
                $group = [];
                foreach($fee_template as $fv)
                {
                    $group[$fv['warehouse_id']] = $fv;
                }

                // 货币符号
                $currency_symbol = ResourcesService::CurrencyDataSymbol();

                // 当前选中的支付方式
                $payment_id = (isset($params['params']) && isset($params['params']['params']) && isset($params['params']['params']['payment_id'])) ? $params['params']['params']['payment_id'] : 0;

                // 运费计算
                foreach($params['data'] as &$v)
                {
                    // 订单模式（0快递, 1展示型, 2自提点, 3虚拟销售）
                    // 仓库是否存在运费模板
                    if($v['order_base']['site_model'] != 0 || empty($group[$v['id']]))
                    {
                        continue;
                    }
                    $template = $group[$v['id']];

                    // 计算运费
                    // 支付方式免运费
                    $is_payment = false;
                    if(!empty($template['payment']) && !empty($payment_id))
                    {
                        if(!is_array($template['payment']))
                        {
                            $template['payment'] = explode(',', $template['payment']);
                        }
                        $payment = array_map(function($v){return explode('-', $v);}, $template['payment']);
                        if(!empty($payment) && is_array($payment))
                        {
                            foreach($payment as $pv)
                            {
                                if(isset($pv[0]) && $pv[0] == $payment_id)
                                {
                                    $is_payment = true;
                                    break;
                                }
                            }
                        }
                    }
                    if($is_payment === false)
                    {
                        // 免运费商品
                        $free_goods = $this->FreeShippingGoods(empty($template['goods_ids']) ? '' : $template['goods_ids'], $v['goods_items']);
                        $buy_count = $v['order_base']['buy_count']-$free_goods['buy_count'];
                        $spec_weight_total = $v['order_base']['spec_weight_total']-$free_goods['spec_weight'];
                        $spec_volume_total = $v['order_base']['spec_volume_total']-$free_goods['spec_volume'];

                        // 是否设置运费数据
                        if(!empty($template['data']) && ($buy_count > 0 || $spec_weight_total > 0 || $spec_volume_total > 0))
                        {
                            // 规则
                            $rules = $this->RulesHandle($template['data'], $v['order_base']['address']);

                            // 计费方式
                            $price = 0;
                            if(!empty($rules))
                            {
                                // 订单金额满免运费
                                if(empty($rules['free_shipping_price']) || $v['order_base']['total_price'] < $rules['free_shipping_price'])
                                {
                                    // 根据规则计算运费
                                    switch($template['valuation'])
                                    {
                                        // 按件
                                        case 0 :
                                            if($buy_count > 0)
                                            {
                                                $price = $this->FreightTypeCalculate($rules, $buy_count, $template);
                                            }
                                            break;

                                        // 按重量
                                        case 1 :
                                            if($spec_weight_total > 0)
                                            {
                                                $price = $this->FreightTypeCalculate($rules, $spec_weight_total, $template);
                                            }
                                            break;

                                        // 按体积
                                        case 2 :
                                            if($spec_volume_total > 0)
                                            {
                                                $price = $this->FreightTypeCalculate($rules, $spec_volume_total, $template);
                                            }
                                            break;
                                    }
                                }
                            }
                            // 扩展展示数据
                            if($price > 0)
                            {
                                $v['order_base']['extension_data'][] = [
                                    'name'      => empty($template['show_name']) ? '运费' : $template['show_name'],
                                    'price'     => $price,
                                    'type'      => 1,
                                    'business'  => 'plugins-freightfee',
                                    'tips'      => '+'.$currency_symbol.$price.'元',
                                ];
                            }
                        }

                        // 分类特定运费
                        if(!empty($template['goods_category_append']) && is_array($template['goods_category_append']))
                        {
                            $goods_category_ids = Db::name('GoodsCategoryJoin')->where(['goods_id'=>array_column($v['goods_items'], 'goods_id')])->column('category_id');
                            if(!empty($goods_category_ids))
                            {
                                $parent_ids = GoodsCategoryService::GoodsCategoryParentIds($goods_category_ids);
                                if(!empty($parent_ids))
                                {
                                    foreach($template['goods_category_append'] as $gca)
                                    {
                                        if(!empty($gca['price']) && !empty($gca['name']) && !empty($gca['id']) && in_array($gca['id'], $parent_ids))
                                        {
                                            $v['order_base']['extension_data'][] = [
                                                'name'      => empty($gca['icon']) ? '【'.$gca['name'].'】额外运费' : $gca['icon'],
                                                'price'     => $gca['price'],
                                                'type'      => 1,
                                                'business'  => 'plugins-freightfee',
                                                'tips'      => '+'.$currency_symbol.$gca['price'].'元',
                                                'ext'       => 'special',
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 免运费商品
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-04-26
     * @desc    description
     * @param   [string]         $goods_ids [商品ids]
     * @param   [array]          $goods     [商品]
     */
    private function FreeShippingGoods($goods_ids, $goods)
    {
        $result = [
            'buy_count'     => 0,
            'spec_weight'   => 0,
            'spec_volume'   => 0,
        ];
        if(!empty($goods_ids))
        {
            if(!is_array($goods_ids))
            {
                $goods_ids = explode(',', $goods_ids);
            }
            foreach($goods as $v)
            {
                if(in_array($v['goods_id'], $goods_ids))
                {
                    $result['buy_count'] += $v['stock'];
                    $result['spec_weight'] += $v['stock']*$v['spec_weight'];
                    $result['spec_volume'] += $v['stock']*$v['spec_volume'];
                }
            }
        }

        return $result;
    }

    /**
     * 运费计费
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-03-21
     * @desc    description
     * @param   [array]          $rules   [规则]
     * @param   [array]          $value   [数据值( 数量 buy_count , 重量 spec_weight_total , 体积 spec_volume_total )]
     * @param   [array]          $config  [插件配置]
     */
    public function FreightTypeCalculate($rules, $value, $config)
    {
        // 运费金额
        $price = 0;

        // 首重
        if($rules['first_price'] > 0)
        {
            // 首件不满足也加首费 或者 满足加首费
            if((isset($config['is_insufficient_first_price']) && $config['is_insufficient_first_price'] == 1) || $value >= $rules['first'])
            {
                $price = $rules['first_price'];
            }
        }

        // 续重
        if($rules['continue_price'] > 0 && $value > $rules['first'])
        {
            $is_continue_type = isset($config['is_continue_type']) ? intval($config['is_continue_type']) : 0;
            switch($is_continue_type)
            {
                // 向上取整（有小数就加1）
                case 1 :
                    $number = ceil(($value-$rules['first'])/$rules['continue']);
                    break;

                // 向下取整（有小数就忽略）
                case 2 :
                    $number = floor(($value-$rules['first'])/$rules['continue']);
                    break;

                // 四舍五入取整 默认
                default :
                    $number = round(($value-$rules['first'])/$rules['continue']);
            }
            if($number > 0)
            {
                $price += PriceNumberFormat($rules['continue_price']*$number);
            }
        }
        return $price;
    }

    /**
     * 运费规则匹配
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-03-21
     * @desc    description
     * @param   [array]          $rules   [运费规则列表]
     * @param   [array]          $address [用户地址]
     */
    public function RulesHandle($rules, $address)
    {
        // 规则数据类型
        if(!empty($rules) && !is_array($rules))
        {
            $rules = json_decode($rules, true);
        }

        // 大于一个规则
        if(count($rules) > 1 && !empty($address))
        {
            $data = [
                'province'  => ['rules' => [], 'number' => 0],
                'city'      => ['rules' => [], 'number' => 0],
            ];
            foreach($rules as $k=>$v)
            {
                if($k != 0)
                {
                    $region = explode('-', $v['region']);
                    if(!empty($region))
                    {
                        if(in_array($address['province'], $region))
                        {
                            $data['province']['rules'] = $v;
                            $data['province']['number']++;
                        }
                        if(in_array($address['city'], $region))
                        {
                            $data['city']['rules'] = $v;
                            $data['city']['number']++;
                        }
                    }
                }
            }
            if($data['city']['number'] > 0)
            {
                if($data['province']['number'] > $data['city']['number'])
                {
                    return $data['province']['rules'];
                }
                return $data['city']['rules'];
            } else {
                if($data['province']['number'] > 0)
                {
                    return $data['province']['rules'];
                }
            }
        }
        return  isset($rules[0]) ? $rules[0] : [];
    }
}
?>