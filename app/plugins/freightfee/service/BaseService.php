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
namespace app\plugins\freightfee\service;

use think\facade\Db;
use app\service\GoodsService;
use app\service\GoodsCategoryService;
use app\service\RegionService;
/**
 * 运费设置服务层
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class BaseService
{
    // 计件方式
    public static $is_whether_list = [
        0 => array('id' => 0, 'name' => '按件数', 'checked' => true),
        1 => array('id' => 1, 'name' => '按重量(kg)'),
        2 => array('id' => 2, 'name' => '按体积(m³)'),
    ];

    // 续费计算方式
    public static $is_continue_type_list = [
        0 => array('id' => 0, 'name' => '四舍五入取整', 'checked' => true),
        1 => array('id' => 1, 'name' => '向上取整（有小数就加1）'),
        2 => array('id' => 2, 'name' => '向下取整（有小数就忽略、直接取整）'),
    ];

    /**
     * 数据列表
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-12-18
     * @desc    description
     * @param   [array]          $params  [输入参数]
     */
    public static function WarehouseFeeList($params = [])
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? 'ft.*,w.id as warehouse_id,w.name as warehouse_name,w.alias as warehouse_alias' : $params['field'];
        $order_by = empty($params['order_by']) ? 'w.level desc, w.id desc' : trim($params['order_by']);
        $data = Db::name('PluginsFreightfeeTemplate')->alias('ft')->rightJoin('warehouse w', 'ft.warehouse_id=w.id')->field($field)->where($where)->order($order_by)->select()->toArray();
        return DataReturn(MyLang('handle_success'), 0, self::DataHandle($data));
    }

    /**
     * 数据处理
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-18
     * @desc    description
     * @param   [array]          $data [仓库数据]
     */
    public static function DataHandle($data)
    {
        if(!empty($data))
        {
            foreach($data as &$v)
            {
                // 支付方式
                if(empty($v['payment']))
                {
                    $v['payment'] = [];
                    $v['payment_names'] = [];
                } else {
                    $v['payment'] = explode(',', $v['payment']);
                    $v['payment_names'] = array_map(function($v){return mb_substr($v, strrpos($v, '-')+1, null, 'utf-8');}, $v['payment']);
                }

                // 地区
                if(!empty($v['data']))
                {
                    if(!is_array($v['data']))
                    {
                        $v['data'] = json_decode($v['data'], true);
                    }
                    foreach($v['data'] as &$vs)
                    {
                        $vs['region_names'] = (empty($vs['region_show']) || $vs['region_show'] == 'default') ? '' : implode('、', Db::name('Region')->where('id', 'in', explode('-', $vs['region_show']))->column('name'));
                    }
                }

                // 商品列表
                $v['goods_list'] = empty($v['goods_ids']) ? [] : self::GoodsList($v['goods_ids']);

                // 商品分类追加运费
                $v['goods_category_append'] = empty($v['goods_category_append']) ? '' : json_decode($v['goods_category_append'], true);
            }
        }
        return $data;
    }

    /**
     * 保存
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-07
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function WarehouseFeeSave($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'warehouse_id',
                'error_msg'         => '仓库id有误',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'show_name',
                'error_msg'         => '运费展示名称不能为空',
            ],
            [
                'checked_type'      => 'length',
                'key_name'          => 'show_name',
                'checked_data'      => '0,16',
                'error_msg'         => '运费展示名称格式最多 16 个字符',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'valuation',
                'checked_data'      => array_column(self::$is_whether_list, 'id'),
                'error_msg'         => '计价方式范围值有误',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'is_insufficient_first_price',
                'checked_data'      => [0,1],
                'is_checked'        => 2,
                'error_msg'         => '不满足按首费计算范围值有误',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'is_continue_type',
                'checked_data'      => array_column(self::$is_continue_type_list, 'id'),
                'error_msg'         => '计价方式范围值有误',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'data',
                'error_msg'         => '运费规则不能为空',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 操作数据
        $is_default = isset($params['is_default']) ? intval($params['is_default']) : 0;
        $data = [
            'warehouse_id'                  => intval($params['warehouse_id']),
            'show_name'                     => trim($params['show_name']),
            'valuation'                     => intval($params['valuation']),
            'is_insufficient_first_price'   => isset($params['is_insufficient_first_price']) ?  intval($params['is_insufficient_first_price']) : 0,
            'is_continue_type'              => intval($params['is_continue_type']),
            'data'                          => empty($params['data']) ? '' : json_encode($params['data']),
            'payment'                       => empty($params['payment']) ? '' : $params['payment'],
            'goods_ids'                     => empty($params['goods_ids']) ? '' : $params['goods_ids'],
            'goods_category_append'         => empty($params['goods_category_append']) ? '' : json_encode($params['goods_category_append'], JSON_UNESCAPED_UNICODE),
            'is_enable'                     => isset($params['is_enable']) ? intval($params['is_enable']) : 0,
        ];

        // 查询数据
        $info = Db::name('PluginsFreightfeeTemplate')->where(['warehouse_id'=>$data['warehouse_id']])->find();

        // 添加/更新数据
        if(empty($info))
        {
            $data['add_time'] = time();
            if(Db::name('PluginsFreightfeeTemplate')->insertGetId($data) <= 0)
            {
                return DataReturn(MyLang('insert_fail'), -100);
            }
        } else {
            $data['upd_time'] = time();
            if(!Db::name('PluginsFreightfeeTemplate')->where(['id'=>$info['id']])->update($data))
            {
                return DataReturn(MyLang('update_fail'), -100);
            }
        }
        return DataReturn(MyLang('operate_success'), 0);
    }

    /**
     * 状态更新
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-07
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    public static function WarehouseFeeStatusUpdate($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => MyLang('data_id_error_tips'),
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'field',
                'error_msg'         => MyLang('operate_field_error_tips'),
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'state',
                'checked_data'      => [0,1],
                'error_msg'         => MyLang('form_status_range_message'),
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 数据更新
        if(Db::name('PluginsFreightfeeTemplate')->where(['id'=>intval($params['id'])])->update([$params['field']=>intval($params['state']), 'upd_time'=>time()]))
        {
            return DataReturn(MyLang('edit_success'), 0);
        }
        return DataReturn(MyLang('edit_fail'), -100);
    }

    /**
     * 商品搜索
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     * @param    [array]          $params [输入参数]
     */
    public static function GoodsSearchList($params = [])
    {
        // 条件
        $where = empty($params['where']) ? [] : $params['where'];
        $where = array_merge($where, [
            ['g.is_delete_time', '=', 0],
            ['g.is_shelves', '=', 1]
        ]);

        // 关键字
        if(!empty($params['keywords']))
        {
            $where[] = ['g.title', 'like', '%'.$params['keywords'].'%'];
        }

        // 分类id
        if(!empty($params['category_id']))
        {
            $category_ids = GoodsCategoryService::GoodsCategoryItemsIds([$params['category_id']], 1);
            $category_ids[] = $params['category_id'];
            $where[] = ['gci.category_id', 'in', $category_ids];
        }

        // 指定字段
        $field = 'g.id,g.title';

        // 获取数据
        return GoodsService::CategoryGoodsList(['where'=>$where, 'm'=>0, 'n'=>100, 'field'=>$field, 'is_admin_access'=>1]);
    }

    /**
     * 商品列表
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     * @param    [string]          $goods_ids [商品id]
     */
    public static function GoodsList($goods_ids = [])
    {
        // 商品id
        $goods_ids = empty($goods_ids) ? [] : explode(',', $goods_ids);

        // 条件
        $where = [
            ['g.is_delete_time', '=', 0],
            ['g.is_shelves', '=', 1],
            ['g.id', 'in', $goods_ids],
        ];

        // 指定字段
        $field = 'g.id,g.title,g.images,g.price';

        // 获取数据
        $ret = GoodsService::CategoryGoodsList(['where'=>$where, 'm'=>0, 'n'=>0, 'field'=>$field]);
        return $ret['data'];
    }

    /**
     * 地区列表
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-22
     * @desc    description
     */
    public static function RegionList()
    {
        // 地区
        $region = RegionService::RegionItems(['pid'=>0, 'field'=>'id,name']);
        if(!empty($region))
        {
            $region = array_map(function($v)
            {
                $v['items'] = RegionService::RegionItems(['pid'=>$v['id'], 'field'=>'id,name']);
                return $v;
            }, $region);
        }
        return $region;
    }
}
?>