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
namespace app\plugins\freightfee\admin;

use app\plugins\freightfee\admin\Common;
use app\plugins\freightfee\service\BaseService;
use app\service\PluginsService;
use app\service\PaymentService;
use app\service\GoodsCategoryService;

/**
 * 运费设置 - 管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Admin extends Common
{
    /**
     * 列表
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-22
     * @desc    description
     */
    public function Index()
    {
        // 获取列表
        $data_params = [
            'where'         => $this->form_where,
        ];
        $ret = BaseService::WarehouseFeeList($data_params);

        // 基础参数赋值
        MyViewAssign('params', $this->data_request);
        MyViewAssign('data_list', $ret['data']);
        return MyView('../../../plugins/view/freightfee/admin/admin/index');
    }

    /**
     * 详情
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @date     2020-07-22
     */
    public function Detail()
    {
        if(!empty($this->data_request['wid']))
        {
            // 条件
            $where = [
                ['w.id', '=', intval($this->data_request['wid'])],
            ];

            // 获取列表
            $data_params = [
                'm'             => 0,
                'n'             => 1,
                'where'         => $where,
            ];
            $ret = BaseService::WarehouseFeeList($data_params);
            $data = (empty($ret['data']) || empty($ret['data'][0])) ? [] : $ret['data'][0];
            MyViewAssign('data', $data);
        }
        return MyView('../../../plugins/view/freightfee/admin/admin/detail');
    }

    /**
     * 编辑页面
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-22
     * @desc    description
     */
    public function SaveInfo()
    {
        // 参数
        $params = $this->data_request;

        // 数据
        $data = [];
        if(!empty($params['wid']))
        {
            // 获取列表
            $data_params = array(
                'where' => [
                    ['w.id', '=', intval($params['wid'])],
                ],
            );
            $ret = BaseService::WarehouseFeeList($data_params);
            $data = empty($ret['data'][0]) ? [] : $ret['data'][0];
        }

        // 支付方式
        MyViewAssign('payment_list', PaymentService::PaymentList(['is_enable'=>1, 'is_open_user'=>1]));

        // 商品分类
        MyViewAssign('goods_category_list', GoodsCategoryService::GoodsCategoryAll());

        // 地区
        MyViewAssign('region_list', BaseService::RegionList());

        // 静态数据
        MyViewAssign('is_whether_list', BaseService::$is_whether_list);
        MyViewAssign('is_continue_type_list', BaseService::$is_continue_type_list);

        // 数据
        MyViewAssign('data', $data);
        MyViewAssign('params', $params);
        return MyView('../../../plugins/view/freightfee/admin/admin/saveinfo');
    }

    /**
     * 数据保存
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function Save()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error(MyLang('illegal_access_tips'));
        }

        // 开始处理
        $params = $this->data_request;
        return BaseService::WarehouseFeeSave($params);
    }

    /**
     * 状态更新
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-07
     * @desc    description
     */
    public function StatusUpdate()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error(MyLang('illegal_access_tips'));
        }

        // 开始处理
        $params = $this->data_request;
        $params['admin'] = $this->admin;
        return BaseService::WarehouseFeeStatusUpdate($params);
    }

    /**
     * 商品搜索
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-07-22
     * @desc    description
     */
    public function GoodsSearch()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error(MyLang('illegal_access_tips'));
        }

        // 搜索数据
        return BaseService::GoodsSearchList($this->data_request);
    }
}
?>