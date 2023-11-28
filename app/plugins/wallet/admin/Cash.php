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
namespace app\plugins\wallet\admin;

use app\plugins\wallet\admin\Common;
use app\plugins\wallet\service\CashService;
use app\plugins\wallet\service\BaseService;
use app\plugins\wallet\service\WalletService;

/**
 * 钱包插件 - 提现管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Cash extends Common
{
    /**
     * 列表
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function Index($params = [])
    {
        return MyView('../../../plugins/view/wallet/admin/cash/index');
    }

    /**
     * 详情
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-03-15T23:51:50+0800
     * @param   [array]          $params [输入参数]
     */
    public function Detail($params = [])
    {
        return MyView('../../../plugins/view/wallet/admin/cash/detail');
    }

    /**
     * 审核页面
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-05-05
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public function AuditInfo($params = [])
    {
        $data = [];
        if(!empty($params['id']))
        {
            $data_params = array(
                'm'         => 0,
                'n'         => 1,
                'where'     => ['id'=>intval($params['id'])],
            );
            $ret = BaseService::CashList($data_params);
            if(!empty($ret['data'][0]))
            {
                // 用户钱包
                $user_wallet = WalletService::UserWallet($ret['data'][0]['user_id']);
                if($user_wallet['code'] == 0)
                {
                    $data = $ret['data'][0];
                    MyViewAssign('user_wallet', $user_wallet['data']);
                } else {
                    MyViewAssign('msg', $user_wallet['msg']);
                }
            } else {
                MyViewAssign('msg', '数据不存在或已删除');
            }
        } else {
            MyViewAssign('msg', '参数id有误');
        }
        MyViewAssign('data', $data);
        return MyView('../../../plugins/view/wallet/admin/cash/auditinfo');
    }

    /**
     * 审核
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-05-06
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public function Audit($params = [])
    {
        $params['plugins_config'] = $this->plugins_config;
        return CashService::CashAudit($params);
    }
}
?>