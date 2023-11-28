# v2.1.0
# 转账记录
CREATE TABLE IF NOT EXISTS `{PREFIX}plugins_wallet_transfer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `transfer_no` char(60) NOT NULL DEFAULT '' COMMENT '转账单号',
  `send_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发起用户id',
  `send_wallet_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发起钱包id',
  `receive_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接收用户id',
  `receive_wallet_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接收钱包id',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `note` char(230) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `transfer_no` (`transfer_no`),
  KEY `send_user_id` (`send_user_id`),
  KEY `send_wallet_id` (`send_wallet_id`),
  KEY `receive_user_id` (`receive_user_id`),
  KEY `receive_wallet_id` (`receive_wallet_id`),
  KEY `money` (`money`)
) ENGINE=InnoDB DEFAULT CHARSET={CHARSET} ROW_FORMAT=DYNAMIC COMMENT='转账记录 - 钱包';

# 提现
ALTER TABLE `{PREFIX}plugins_wallet_recharge` add `commission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '手续费' after `pay_money`;

















# v1.3.6
# 付款码
CREATE TABLE IF NOT EXISTS `{PREFIX}plugins_wallet_payment_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `wallet_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '钱包id',
  `code` char(30) NOT NULL DEFAULT '' COMMENT '付款码',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `wallet_id` (`wallet_id`),
  KEY `user_id` (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET={CHARSET} ROW_FORMAT=DYNAMIC COMMENT='付款码 - 钱包';











# v1.2.6
# 充值记录
ALTER TABLE `{PREFIX}plugins_wallet_recharge` add `operate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作人id' after `payment_name`;
ALTER TABLE `{PREFIX}plugins_wallet_recharge` add `operate_name` char(30) NOT NULL DEFAULT '' COMMENT '操作人名称' after `operate_id`;
# 日志
ALTER TABLE `{PREFIX}plugins_wallet_log` add `operate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作人id' after `msg`;
ALTER TABLE `{PREFIX}plugins_wallet_log` add `operate_name` char(30) NOT NULL DEFAULT '' COMMENT '操作人名称' after `operate_id`;