<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\plugins\usercentertopnotice\admin;

use app\service\PluginsService;

/**
 * 用户中心顶部公告插件 - 管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Admin
{
    /**
     * 首页
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('usercentertopnotice');
        if($ret['code'] == 0)
        {
            MyViewAssign('data', $ret['data']);
            return MyView('../../../plugins/view/usercentertopnotice/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

    /**
     * 编辑页面
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function saveinfo($params = [])
    {
        $ret = PluginsService::PluginsData('usercentertopnotice');
        if($ret['code'] == 0)
        {
            MyViewAssign('data', $ret['data']);
            return MyView('../../../plugins/view/usercentertopnotice/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }

    /**
     * 数据保存
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function save($params = [])
    {
        return PluginsService::PluginsDataSave(['plugins'=>'usercentertopnotice', 'data'=>$params]);
    }
}
?>