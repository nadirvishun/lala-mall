<?php
pdo_query("
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_bargain`;
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_bargain_goods`;
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_coupon`;
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_coupon_grant_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_coupon_record`;
DROP TABLE IF EXISTS `ims_tiny_wmall_address`;
DROP TABLE IF EXISTS `ims_tiny_wmall_article`;
DROP TABLE IF EXISTS `ims_tiny_wmall_article_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_assign_board`;
DROP TABLE IF EXISTS `ims_tiny_wmall_assign_queue`;
DROP TABLE IF EXISTS `ims_tiny_wmall_black`;
DROP TABLE IF EXISTS `ims_tiny_wmall_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_clerk`;
DROP TABLE IF EXISTS `ims_tiny_wmall_config`;
DROP TABLE IF EXISTS `ims_tiny_wmall_cube`;
DROP TABLE IF EXISTS `ims_tiny_wmall_delivery_cards`;
DROP TABLE IF EXISTS `ims_tiny_wmall_delivery_cards_order`;
DROP TABLE IF EXISTS `ims_tiny_wmall_delivery_config`;
DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer`;
DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_current_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_getcash_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_location_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_transfer_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_errander_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_errander_order`;
DROP TABLE IF EXISTS `ims_tiny_wmall_errander_order_status_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_fans`;
DROP TABLE IF EXISTS `ims_tiny_wmall_goods`;
DROP TABLE IF EXISTS `ims_tiny_wmall_goods_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_goods_options`;
DROP TABLE IF EXISTS `ims_tiny_wmall_help`;
DROP TABLE IF EXISTS `ims_tiny_wmall_member_recharge`;
DROP TABLE IF EXISTS `ims_tiny_wmall_members`;
DROP TABLE IF EXISTS `ims_tiny_wmall_news`;
DROP TABLE IF EXISTS `ims_tiny_wmall_news_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_notice`;
DROP TABLE IF EXISTS `ims_tiny_wmall_notice_read_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_cart`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_comment`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_current_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_discount`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_print_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_refund`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_refund_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_stat`;
DROP TABLE IF EXISTS `ims_tiny_wmall_order_status_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_paylog`;
DROP TABLE IF EXISTS `ims_tiny_wmall_perm_account`;
DROP TABLE IF EXISTS `ims_tiny_wmall_plugin`;
DROP TABLE IF EXISTS `ims_tiny_wmall_printer`;
DROP TABLE IF EXISTS `ims_tiny_wmall_printer_label`;
DROP TABLE IF EXISTS `ims_tiny_wmall_reply`;
DROP TABLE IF EXISTS `ims_tiny_wmall_report`;
DROP TABLE IF EXISTS `ims_tiny_wmall_reserve`;
DROP TABLE IF EXISTS `ims_tiny_wmall_slide`;
DROP TABLE IF EXISTS `ims_tiny_wmall_sms_send_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_account`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_activity`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_clerk`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_current_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_delivery_times`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_deliveryer`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_favorite`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_getcash_log`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_members`;
DROP TABLE IF EXISTS `ims_tiny_wmall_store_settle_config`;
DROP TABLE IF EXISTS `ims_tiny_wmall_tables`;
DROP TABLE IF EXISTS `ims_tiny_wmall_tables_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_tables_scan`;
DROP TABLE IF EXISTS `ims_tiny_wmall_text`;
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_bargain`;
DROP TABLE IF EXISTS `ims_tiny_wmall_activity_bargain`;


DROP TABLE IF EXISTS `ims_tiny_wmall_account`;
CREATE TABLE `ims_tiny_wmall_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '2',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `joindate` int(10) unsigned NOT NULL DEFAULT '0',
  `joinip` varchar(15) NOT NULL DEFAULT '',
  `lastvisit` int(10) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(15) NOT NULL DEFAULT '',
  `remark` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_activity_bargain`;
CREATE TABLE `ims_tiny_wmall_activity_bargain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `content` varchar(255) NOT NULL,
  `order_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `goods_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starthour` smallint(5) unsigned NOT NULL DEFAULT '0',
  `endhour` smallint(5) unsigned NOT NULL DEFAULT '0',
  `use_limit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'ongoing',
  `total_updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_activity_bargain_goods`;
CREATE TABLE `ims_tiny_wmall_activity_bargain_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `bargain_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `discount_price` varchar(10) NOT NULL DEFAULT '0',
  `max_buy_limit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `poi_user_type` varchar(10) NOT NULL DEFAULT 'all',
  `discount_total` int(10) NOT NULL DEFAULT '-1',
  `discount_available_total` int(10) NOT NULL DEFAULT '-1',
  `dosage` int(10) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `mall_displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `bargain_id` (`bargain_id`),
  KEY `goods_id` (`goods_id`),
  KEY `status` (`status`),
  KEY `mall_displayorder` (`mall_displayorder`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_activity_coupon`;
CREATE TABLE `ims_tiny_wmall_activity_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公号序号',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公号序号',
  `type` varchar(20) NOT NULL DEFAULT 'collect',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `discount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '折扣券(折扣率), 代金券(面额)',
  `condition` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单满多少可用',
  `type_limit` int(10) NOT NULL DEFAULT '1' COMMENT '1:所有用户都可领取,2:新用户可领取',
  `dosage` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已领取数量',
  `amount` int(10) unsigned NOT NULL COMMENT '总发行数量',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:可领取,2:暂停领取',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发行时间',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `coupons` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `type` (`type`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`),
  KEY `status` (`status`),
  KEY `activity_id` (`activity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_activity_coupon_grant_log`;
CREATE TABLE `ims_tiny_wmall_activity_coupon_grant_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `couponid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `grant_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态: 1:一次性领取,2:每天领取 ',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `couponid` (`couponid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_activity_coupon_record`;
CREATE TABLE `ims_tiny_wmall_activity_coupon_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `couponid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态: 1:未使用,2:已使用 ',
  `remark` varchar(300) NOT NULL DEFAULT '',
  `granttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发放时间',
  `usetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  `type` varchar(20) NOT NULL DEFAULT 'couponCollect',
  `discount` int(10) unsigned NOT NULL DEFAULT '0',
  `condition` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `couponid` (`couponid`),
  KEY `uid` (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_activity_redpacket_record`;
CREATE TABLE `ims_tiny_wmall_activity_redpacket_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(20) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT '',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `remark` varchar(300) NOT NULL DEFAULT '',
  `granttime` int(10) unsigned NOT NULL DEFAULT '0',
  `usetime` int(10) unsigned NOT NULL DEFAULT '0',
  `category_limit` varchar(500) NOT NULL,
  `times_limit` varchar(500) NOT NULL,
  `is_show` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `super_share_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `redpacketid` (`activity_id`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `is_show` (`is_show`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_address`;
CREATE TABLE `ims_tiny_wmall_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `realname` varchar(15) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(50) NOT NULL,
  `number` varchar(20) NOT NULL,
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:收货地址, 2:服务地址',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_assign_board`;
CREATE TABLE `ims_tiny_wmall_assign_board` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(15) NOT NULL,
  `openid` varchar(64) NOT NULL,
  `guest_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `number` varchar(20) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_notify` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_assign_queue`;
CREATE TABLE `ims_tiny_wmall_assign_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `guest_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` varchar(10) NOT NULL,
  `endtime` varchar(10) NOT NULL,
  `prefix` varchar(10) NOT NULL COMMENT '前缀',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `position` int(10) unsigned NOT NULL DEFAULT '1',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '根据这个时间,判断是否将position重新至0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_category`;
CREATE TABLE `ims_tiny_wmall_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'member',
  `alias` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `color` varchar(15) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_system` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_clerk`;
CREATE TABLE `ims_tiny_wmall_clerk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(15) NOT NULL,
  `nickname` varchar(15) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `avatar` varchar(255) NOT NULL,
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `token` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_config`;
CREATE TABLE `ims_tiny_wmall_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sysset` text NOT NULL,
  `pluginset` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_creditshop_order`;
CREATE TABLE `ims_tiny_wmall_creditshop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `credits` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `itemcode` int(10) unsigned NOT NULL DEFAULT '0',
  `actualprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `faceprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `description` varchar(255) NOT NULL,
  `ordernum` varchar(255) NOT NULL,
  `ordersn` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_cube`;
CREATE TABLE `ims_tiny_wmall_cube` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `tips` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer`;
CREATE TABLE `ims_tiny_wmall_deliveryer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(15) NOT NULL,
  `nickname` varchar(15) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `token` varchar(32) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `age` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `credit1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `work_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `order_takeout_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order_errander_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `auth_info` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `work_status` (`work_status`),
  KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_current_log`;
CREATE TABLE `ims_tiny_wmall_deliveryer_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(20) NOT NULL DEFAULT 'order',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:订单入账, 2: 申请提现',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `trade_type` (`trade_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_getcash_log`;
CREATE TABLE `ims_tiny_wmall_deliveryer_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1:申请成功,2:申请中',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `account` varchar(1000) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_location_log`;
CREATE TABLE `ims_tiny_wmall_deliveryer_location_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime_cn` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_deliveryer_transfer_log`;
CREATE TABLE `ims_tiny_wmall_deliveryer_transfer_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(20) NOT NULL DEFAULT 'takeout',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reason` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_year` smallint(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_delivery_cards`;
CREATE TABLE `ims_tiny_wmall_delivery_cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `price` int(10) unsigned NOT NULL DEFAULT '0',
  `day_free_limit` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_delivery_cards_order`;
CREATE TABLE `ims_tiny_wmall_delivery_cards_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `ordersn` varchar(20) NOT NULL,
  `card_id` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` varchar(20) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_delivery_config`;
CREATE TABLE `ims_tiny_wmall_delivery_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile_verify_status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `agreement` text NOT NULL,
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `plateform_delivery_fee` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_fee_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `delivery_fee` varchar(10) NOT NULL,
  `get_cash_fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_rate` varchar(10) NOT NULL,
  `get_cash_fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  `card_apply_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_agreement` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_diypage`;
CREATE TABLE `ims_tiny_wmall_diypage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `diymenu` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_diypage_menu`;
CREATE TABLE `ims_tiny_wmall_diypage_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `addtime` (`addtime`),
  KEY `updatetime` (`updatetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_diypage_template`;
CREATE TABLE `ims_tiny_wmall_diypage_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `preview` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `ims_tiny_wmall_diypage_template` (`id`, `uniacid`, `type`, `name`, `data`, `preview`, `code`) VALUES
(1,	0,	1,	'系统模板01',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTQ5MjQwNjQzNyI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjODNkYzYxIn0sImRhdGEiOnsiQzE1MDE0OTI0MDY0MzciOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQyXC9iYW5uZXItMS1sb2dvLmpwZyIsImxpbmt1cmwiOiIifX0sImlkIjoiYmFubmVyIn0sIk0xNTAxNDkzNTQ3MjU5Ijp7InBhcmFtcyI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC90ZW1wbGF0ZVwvZGVmYXVsdDJcL2ltZy1jYXJkLTEuanBnIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxNSIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjODNkYzYxIn0sImlkIjoiaW1nX2NhcmQifSwiTTE1MDE0OTI1NTczMTciOnsicGFyYW1zIjp7Imdvb2RzdHlwZSI6IjAiLCJzaG93dGl0bGUiOiIxIiwic2hvd3ByaWNlIjoiMSIsInNob3dvbGRwcmljZSI6IjEiLCJzaG93dGFnIjoiMCIsImdvb2RzZGF0YSI6IjAiLCJnb29kc3NvcnQiOiIwIiwiZ29vZHNudW0iOiI2Iiwic2hvd2ljb24iOiIxIiwiaWNvbnBvc2l0aW9uIjoibGVmdCB0b3AiLCJidXlidG50ZXh0IjoiXHU3YWNiXHU1MzczXHU2MmEyXHU4ZDJkIiwiZ29vZHNpY29uc3JjIjoiIn0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjODNkYzYxIiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwibGlzdHN0eWxlIjoiMSIsImdvb2RzaWNvbiI6InJlY29tbWFuZCIsInRpdGxlY29sb3IiOiIjMzMzIiwicHJpY2Vjb2xvciI6IiNmYjRlNDQiLCJvbGRwcmljZWNvbG9yIjoiIzk5OSIsImJ1eWJ0bmNvbG9yIjoiI2ZiNGU0NCIsImljb25wYWRkaW5ndG9wIjoiMCIsImljb25wYWRkaW5nbGVmdCI6IjAiLCJpY29uem9vbSI6IjEwMCIsInRhZ2JhY2tncm91bmQiOiIjZmU1NDU1Iiwic2FsZXNjb2xvciI6IiM3Nzc3NzcifSwiZGF0YSI6eyJDMTUwMTQ5MjU1NzMxNyI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTI1NTczMTgiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5MzUxODA1MSI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQyXC9pbWctY2FyZC0yLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzgzZGM2MSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDkzMjM2Nzg0Ijp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiIzgzZGM2MSIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjEiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE0OTMyMzY3ODQiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDkzMjM2Nzg1Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5MzIzNjc4NiI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTMyMzY3ODciOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5MzQxMzc3OCI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjE1IiwiZG90YWxpZ24iOiJjZW50ZXIiLCJsZWZ0cmlnaHQiOiI1IiwiYm90dG9tIjoiNSIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIiwiYmFja2dyb3VuZCI6IiM4M2RjNjEifSwiZGF0YSI6eyJDMTUwMTQ5MzQxMzc3OCI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3BpY3R1cmUtMS5qcGciLCJsaW5rdXJsIjoiIn0sIkMxNTAxNDkzNDEzNzc5Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvcGljdHVyZS0yLmpwZyIsImxpbmt1cmwiOiIifX0sImlkIjoicGljdHVyZSJ9LCJNMTUwMTQ5MzQ5MTI4NyI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQyXC9pbWctY2FyZC0zLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzgzZGM2MSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDkzNjA2Nzg1Ijp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiIzgzZGM2MSIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjIiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE0OTM2MDY3ODUiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDkzNjA2Nzg2Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5MzYwNjc4NyI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTM2MDY3ODgiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5Mzc2NjQzOCI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQyXC9pbWctY2FyZC00LmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzgzZGM2MSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDk3MDcwMTk2Ijp7InN0eWxlIjp7ImhlaWdodCI6IjEwIiwiYmFja2dyb3VuZCI6IiM4M2RiNjEifSwiaWQiOiJibGFuayJ9LCJNMTUwMTQ5Mzc5OTk2MyI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjODNkYzYxIiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTQ5Mzc5OTk2MyI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNDkzNzk5OTY0Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTQ5Mzc5OTk2NSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE0OTQwMTE0NjAiOnsic3R5bGUiOnsiaGVpZ2h0IjoiMTUiLCJiYWNrZ3JvdW5kIjoiIzgzZGM2MSJ9LCJpZCI6ImJsYW5rIn0sIk0xNTAxNDk2OTgyNjUxIjp7InN0eWxlIjp7InBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImJhY2tncm91bmQiOiIjODNkYjYxIn0sImRhdGEiOnsiQzE1MDE0OTY5ODI2NTEiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQyXC9iYW5uZXItMi1sb2dvLmpwZyIsImxpbmt1cmwiOiIifX0sImlkIjoiYmFubmVyIn0sIk0xNTAxNDk2MzYzODE5Ijp7InBhcmFtcyI6eyJjb250ZW50IjoiUEdScGRpQmpiR0Z6Y3owaVlXTjBhWFpwZEhrdGNuVnNaUzEwYVhSc1pTQmliM0prWlhJdE1YQjRMV0lpSUhOMGVXeGxQU0ppYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCaWIzSmtaWEl0WW05MGRHOXRPaUF4Y0hnZ2MyOXNhV1FnY21kaUtESXlOQ3dnTWpJMExDQXlNalFwT3lCb1pXbG5hSFE2SURFdU9ISmxiVHNnYkdsdVpTMW9aV2xuYUhRNklERXVPSEpsYlRzZ1ptOXVkQzF6YVhwbE9pQXdMamh5WlcwN0lHTnZiRzl5T2lCeVoySW9OVEVzSURVeExDQTFNU2s3SUhSbGVIUXRZV3hwWjI0NklHTmxiblJsY2pzZ1ptOXVkQzEzWldsbmFIUTZJR0p2YkdRN0lHWnZiblF0Wm1GdGFXeDVPaUFtY1hWdmREdE5hV055YjNOdlpuUWdXV0ZvWldrbWNYVnZkRHNzSU9XK3J1aTlyK21iaGVtN2tTd2c1YTZMNUwyVExDQlVZV2h2YldFc0lFRnlhV0ZzTENCSVpXeDJaWFJwWTJFc0lGTlVTR1ZwZEdrN0lIZG9hWFJsTFhOd1lXTmxPaUJ1YjNKdFlXdzdJajdtdEx2bGlxam9wNFRsaUprOEwyUnBkajQ4WkdsMklHTnNZWE56UFNKaFkzUnBkbWwwZVMxeWRXeGxMV052Ym5SbGJuUWlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUJ3WVdSa2FXNW5PaUF3TGpoeVpXMDdJR3hwYm1VdGFHVnBaMmgwT2lBeGNtVnRPeUJtYjI1MExYTnBlbVU2SURBdU4zSmxiVHNnWTI5c2IzSTZJSEpuWWlnMU1Td2dOVEVzSURVeEtUc2dabTl1ZEMxbVlXMXBiSGs2SUNaeGRXOTBPMDFwWTNKdmMyOW1kQ0JaWVdobGFTWnhkVzkwT3l3ZzViNnU2TDJ2Nlp1RjZidVJMQ0Rscm92a3ZaTXNJRlJoYUc5dFlTd2dRWEpwWVd3c0lFaGxiSFpsZEdsallTd2dVMVJJWldsMGFUc2dkMmhwZEdVdGMzQmhZMlU2SUc1dmNtMWhiRHNpUGpFdTVyUzc1WXFvNXBlMjZaZTA3N3lhTithY2lERXk1cGVsNzd5Tk4rYWNpREUyNXBlbEptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQakl1NXJTNzVZcW82SXlENVp1MDc3eWE1b21BNkthRzU1dVc1WitPNWJpQ0ptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQak11NXJTNzVZcW81WWFGNWE2NTc3eWE2S1dcLzU1T2NNQzQ1T2VXRmcraTF0Kys4ak9hWG9PYTB1K1dLcU9XVGdlUyttK2U3bWVXY3NPV011dVM3cGVXdW51bVpoZVM3dCthZ3ZPUzR1dVdIaGladVluTndPenhpY2lCemRIbHNaVDBpWW05NExYTnBlbWx1WnpvZ1ltOXlaR1Z5TFdKdmVEc2dMWGRsWW10cGRDMTBZWEF0YUdsbmFHeHBaMmgwTFdOdmJHOXlPaUIwY21GdWMzQmhjbVZ1ZERzaUx6NDBMdVM4bU9hRG9PUzdoZW1aa09lK2p1V2JvdVdrbHVXTmx1V1BpdWUranVXYm9rRndjT1M0aStXTmxlUzRsT21BaWVhTHFlV2NxT2U2dithVXIrUzdtT2VhaE9pdW91V05sZVM2cStXUGx6d3ZaR2wyUGc9PSJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiIzgzZGI2MSIsInBhZGRpbmciOiIxNSIsInBhZGRpbmd0b3AiOiIxNSIsInBhZGRpbmdsZWZ0IjoiMTUifSwiaWQiOiJyaWNodGV4dCJ9fX0=',	'../addons/we7_wmall/plugin/diypage/static/template/default2/preview.jpg',	'10001'),
(2,	0,	1,	'系统模板02',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTQ5NDA5Nzc5NSI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjZmY2MDJmIn0sImRhdGEiOnsiQzE1MDE0OTQwOTc3OTYiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQxXC9iYW5uZXItMS1sb2dvLmpwZyIsImxpbmt1cmwiOiIifX0sImlkIjoiYmFubmVyIn0sIk0xNTAxNDk0NTgxOTQ3Ijp7InBhcmFtcyI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC90ZW1wbGF0ZVwvZGVmYXVsdDFcL2ltZy1jYXJkLTMuanBnIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxNSIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjZmY2MDJmIn0sImlkIjoiaW1nX2NhcmQifSwiTTE1MDE0OTQ2NDAwMDQiOnsicGFyYW1zIjp7InNob3dkaXNjb3VudCI6IjEiLCJzaG93aG90Z29vZHMiOiIxIiwic3RvcmVkYXRhIjoiMCIsInN0b3JlbnVtIjoiNiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZmNjAyZiIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxMCIsInRpdGxlY29sb3IiOiIjMzMzIiwic2NvcmVjb2xvciI6IiNmZjJkNGIiLCJkZWxpdmVyeXRpdGxlYmdjb2xvciI6IiNmZjJkNGIiLCJkZWxpdmVyeXRpdGxlY29sb3IiOiIjZmZmIn0sImRhdGEiOnsiQzE1MDE0OTQ2NDAwMDQiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjMwIiwiYWN0aXZpdHkiOnsiaXRlbXMiOnsiQzAxMjM0NTY3ODkxMDEiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWRpc2NvdW50LnBuZyIsInRpdGxlIjoiXHU2ZWUxMzVcdTUxY2YxMjtcdTZlZTE2MFx1NTFjZjIwIn0sIkMwMTIzNDU2Nzg5MTAyIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1jb3Vwb25Db2xsZWN0LnBuZyIsInRpdGxlIjoiXHU1M2VmXHU5ODg2Mlx1NTE0M1x1NGVlM1x1OTFkMVx1NTIzOCJ9fSwibnVtIjoiMiJ9LCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMyI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX19LCJDMTUwMTQ5NDY0MDAwNSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0yLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNDUifSwiQzE1MDE0OTQ2NDAwMDYiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjU1IiwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNC5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTUuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX19fSwiaWQiOiJ3YWltYWlfc3RvcmVzIn0sIk0xNTAxNDk0NjY2Nzk1Ijp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZmNjAyZiIsInBhZGRpbmd0b3AiOiIxMCIsInBhZGRpbmdsZWZ0IjoiMTAiLCJsaXN0c3R5bGUiOiIxIiwiZ29vZHNpY29uIjoicmVjb21tYW5kIiwidGl0bGVjb2xvciI6IiMzMzMiLCJwcmljZWNvbG9yIjoiI2ZiNGU0NCIsIm9sZHByaWNlY29sb3IiOiIjOTk5IiwiYnV5YnRuY29sb3IiOiIjZmI0ZTQ0IiwiaWNvbnBhZGRpbmd0b3AiOiIwIiwiaWNvbnBhZGRpbmdsZWZ0IjoiMCIsImljb256b29tIjoiMTAwIiwidGFnYmFja2dyb3VuZCI6IiNmZTU0NTUiLCJzYWxlc2NvbG9yIjoiIzc3Nzc3NyJ9LCJkYXRhIjp7IkMxNTAxNDk0NjY2Nzk1Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5NDY2Njc5NiI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTQ2NjY3OTciOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDk0NjY2Nzk4Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNC5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fSwiaWQiOiJ3YWltYWlfZ29vZHMifSwiTTE1MDE0OTQ2OTI3ODIiOnsic3R5bGUiOnsicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjEwIiwiYmFja2dyb3VuZCI6IiNmZjYwMmYifSwiZGF0YSI6eyJDMTUwMTQ5NDY5Mjc4MiI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2Jhbm5lci0xLmpwZz90PTEiLCJsaW5rdXJsIjoiIn0sIkMxNTAxNDk0NjkyNzgzIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvYmFubmVyLTIuanBnIiwibGlua3VybCI6IiJ9fSwiaWQiOiJiYW5uZXIifSwiTTE1MDE0OTQ3ODk5MDciOnsic3R5bGUiOnsiaGVpZ2h0IjoiMTAiLCJiYWNrZ3JvdW5kIjoiI2ZmNjAyZiJ9LCJpZCI6ImJsYW5rIn0sIk0xNTAxNDk0NzEwODk5Ijp7InBhcmFtcyI6eyJyb3ciOiIxIiwic2hvd3R5cGUiOiIwIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxMCIsInBhZGRpbmdsZWZ0IjoiMTAiLCJzaG93ZG90IjoiMCIsInBhZ2VudW0iOiIyIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJiYWNrZ3JvdW5kIjoiI2ZmNjAyZiJ9LCJkYXRhIjp7IkMxNTAxNDk0NzEwODk5Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvY3ViZS0xLmpwZz90PTEiLCJsaW5rdXJsIjoiIn0sIkMxNTAxNDk0NzEwOTAwIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvY3ViZS0yLmpwZyIsImxpbmt1cmwiOiIifSwiQzE1MDE0OTQ3MTA5MDEiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9jdWJlLTEuanBnIiwibGlua3VybCI6IiJ9LCJDMTUwMTQ5NDcxMDkwMiI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2N1YmUtMi5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6InBpY3R1cmV3In0sIk0xNTAxNDk0ODY2Mjc4Ijp7InBhcmFtcyI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC90ZW1wbGF0ZVwvZGVmYXVsdDFcL2ltZy1jYXJkLTEuanBnIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxMCIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjZmY2MDJmIn0sImlkIjoiaW1nX2NhcmQifSwiTTE1MDE0OTU3NTEyODQiOnsic3R5bGUiOnsiaGVpZ2h0IjoiNSIsImJhY2tncm91bmQiOiIjZmY2MDJmIn0sImlkIjoiYmxhbmsifSwiTTE1MDE0OTUwMDIzNzAiOnsic3R5bGUiOnsicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjEwIiwiZG90YWxpZ24iOiJjZW50ZXIiLCJsZWZ0cmlnaHQiOiI1IiwiYm90dG9tIjoiNSIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIiwiYmFja2dyb3VuZCI6IiNmZjYwMmYifSwiZGF0YSI6eyJDMTUwMTQ5NTAwMjM3MCI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3BpY3R1cmUtMS5qcGciLCJsaW5rdXJsIjoiIn0sIkMxNTAxNDk1MDAyMzcxIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvcGljdHVyZS0yLmpwZyIsImxpbmt1cmwiOiIifX0sImlkIjoicGljdHVyZSJ9LCJNMTUwMTQ5NTEyNTk0OCI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmY2MDJmIiwicGFkZGluZ3RvcCI6IjEwIiwicGFkZGluZ2xlZnQiOiIxMCIsInRpdGxlY29sb3IiOiIjMzMzIiwic2NvcmVjb2xvciI6IiNmZjJkNGIiLCJkZWxpdmVyeXRpdGxlYmdjb2xvciI6IiNmZjJkNGIiLCJkZWxpdmVyeXRpdGxlY29sb3IiOiIjZmZmIn0sImRhdGEiOnsiQzE1MDE0OTUxMjU5NDgiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjMwIiwiYWN0aXZpdHkiOnsiaXRlbXMiOnsiQzAxMjM0NTY3ODkxMDEiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWRpc2NvdW50LnBuZyIsInRpdGxlIjoiXHU2ZWUxMzVcdTUxY2YxMjtcdTZlZTE2MFx1NTFjZjIwIn0sIkMwMTIzNDU2Nzg5MTAyIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1jb3Vwb25Db2xsZWN0LnBuZyIsInRpdGxlIjoiXHU1M2VmXHU5ODg2Mlx1NTE0M1x1NGVlM1x1OTFkMVx1NTIzOCJ9fSwibnVtIjoiMiJ9LCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMyI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX19LCJDMTUwMTQ5NTEyNTk0OSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0yLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNDUifSwiQzE1MDE0OTUxMjU5NTAiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjU1IiwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNC5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTUuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX19fSwiaWQiOiJ3YWltYWlfc3RvcmVzIn0sIk0xNTAxNDk1MTkyNTQ5Ijp7InBhcmFtcyI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC90ZW1wbGF0ZVwvZGVmYXVsdDFcL2ltZy1jYXJkLTIuanBnIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxNSIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjZmY2MDJmIn0sImlkIjoiaW1nX2NhcmQifSwiTTE1MDE0OTUxNTY1NTUiOnsicGFyYW1zIjp7Imdvb2RzdHlwZSI6IjAiLCJzaG93dGl0bGUiOiIxIiwic2hvd3ByaWNlIjoiMSIsInNob3dvbGRwcmljZSI6IjEiLCJzaG93dGFnIjoiMCIsImdvb2RzZGF0YSI6IjAiLCJnb29kc3NvcnQiOiIwIiwiZ29vZHNudW0iOiI2Iiwic2hvd2ljb24iOiIxIiwiaWNvbnBvc2l0aW9uIjoibGVmdCB0b3AiLCJidXlidG50ZXh0IjoiXHU3YWNiXHU1MzczXHU2MmEyXHU4ZDJkIiwiZ29vZHNpY29uc3JjIjoiIn0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmY2MDJmIiwicGFkZGluZ3RvcCI6IjEwIiwicGFkZGluZ2xlZnQiOiIxMCIsImxpc3RzdHlsZSI6IjIiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE0OTUxNTY1NTUiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDk1MTU2NTU2Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5NTE1NjU1NyI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTUxNTY1NTgiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5NjgwNTUzMiI6eyJwYXJhbXMiOnsiY29udGVudCI6IlBHUnBkaUJqYkdGemN6MGlZV04wYVhacGRIa3RjblZzWlMxMGFYUnNaU0JpYjNKa1pYSXRNWEI0TFdJaUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95QmliM0prWlhJdFltOTBkRzl0T2lBeGNIZ2djMjlzYVdRZ2NtZGlLREl5TkN3Z01qSTBMQ0F5TWpRcE95Qm9aV2xuYUhRNklERXVPSEpsYlRzZ2JHbHVaUzFvWldsbmFIUTZJREV1T0hKbGJUc2dabTl1ZEMxemFYcGxPaUF3TGpoeVpXMDdJR052Ykc5eU9pQnlaMklvTlRFc0lEVXhMQ0ExTVNrN0lIUmxlSFF0WVd4cFoyNDZJR05sYm5SbGNqc2dabTl1ZEMxM1pXbG5hSFE2SUdKdmJHUTdJR1p2Ym5RdFptRnRhV3g1T2lBbWNYVnZkRHROYVdOeWIzTnZablFnV1dGb1pXa21jWFZ2ZERzc0lPVytydWk5cittYmhlbTdrU3dnNWE2TDVMMlRMQ0JVWVdodmJXRXNJRUZ5YVdGc0xDQklaV3gyWlhScFkyRXNJRk5VU0dWcGRHazdJSGRvYVhSbExYTndZV05sT2lCdWIzSnRZV3c3SWo3bXRMdmxpcWpvcDRUbGlKazhMMlJwZGo0OFpHbDJJR05zWVhOelBTSmhZM1JwZG1sMGVTMXlkV3hsTFdOdmJuUmxiblFpSUhOMGVXeGxQU0ppYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCd1lXUmthVzVuT2lBd0xqaHlaVzA3SUd4cGJtVXRhR1ZwWjJoME9pQXhjbVZ0T3lCbWIyNTBMWE5wZW1VNklEQXVOM0psYlRzZ1kyOXNiM0k2SUhKbllpZzFNU3dnTlRFc0lEVXhLVHNnWm05dWRDMW1ZVzFwYkhrNklDWnhkVzkwTzAxcFkzSnZjMjltZENCWllXaGxhU1p4ZFc5ME95d2c1YjZ1NkwydjZadUY2YnVSTENEbHJvdmt2Wk1zSUZSaGFHOXRZU3dnUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnVTFSSVpXbDBhVHNnZDJocGRHVXRjM0JoWTJVNklHNXZjbTFoYkRzaVBqRXU1clM3NVlxbzVwZTI2WmUwNzd5YU4rYWNpREV5NXBlbDc3eU5OK2FjaURFMjVwZWxKbTVpYzNBN1BHSnlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUl2UGpJdTVyUzc1WXFvNkl5RDVadTA3N3lhNW9tQTZLYUc1NXVXNVorTzViaUNKbTVpYzNBN1BHSnlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUl2UGpNdTVyUzc1WXFvNVlhRjVhNjU3N3lhNktXXC81NU9jTUM0NU9lV0ZnK2kxdCsrOGpPYVhvT2EwdStXS3FPV1RnZVMrbStlN21lV2NzT1dNdXVTN3BlV3VudW1aaGVTN3QrYWd2T1M0dXVXSGhpWnVZbk53T3p4aWNpQnpkSGxzWlQwaVltOTRMWE5wZW1sdVp6b2dZbTl5WkdWeUxXSnZlRHNnTFhkbFltdHBkQzEwWVhBdGFHbG5hR3hwWjJoMExXTnZiRzl5T2lCMGNtRnVjM0JoY21WdWREc2lMejQwTHVTOG1PYURvT1M3aGVtWmtPZStqdVdib3VXa2x1V05sdVdQaXVlK2p1V2Jva0Z3Y09TNGkrV05sZVM0bE9tQWllYUxxZVdjcU9lNnYrYVVyK1M3bU9lYWhPaXVvdVdObGVTNnErV1Bsend2WkdsMlBnPT0ifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNmZTYxMmUiLCJwYWRkaW5nIjoiMTUiLCJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjEwIn0sImlkIjoicmljaHRleHQifX19',	'../addons/we7_wmall/plugin/diypage/static/template/default1/preview.jpg',	'10002'),
(3,	0,	1,	'系统模板03',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTQ5NTUwMDA2MSI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNDk1NTAwMDYxIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0M1wvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTQ5NTYzNzY5OSI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQzXC9pbWctY2FyZC0xLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2ViYzM2OCJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDk1ODE3MDUxIjp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ViYzM2OCIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjEiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE0OTU4MTcwNTEiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDk1ODE3MDUyIjp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5NTgxNzA1MyI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTU4MTcwNTQiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5NTgzNjc0NyI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQzXC9pbWctY2FyZC0yLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2ViYzM2OCJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDk1ODYzMjI3Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNlYmMzNjgiLCJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTQ5NTg2MzIyNyI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNDk1ODYzMjI4Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTQ5NTg2MzIyOSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE0OTU4ODQyMzQiOnsicGFyYW1zIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0M1wvaW1nLWNhcmQtMy5qcGcifSwic3R5bGUiOnsicGFkZGluZ3RvcCI6IjE1IiwicGFkZGluZ2xlZnQiOiIwIiwiYmFja2dyb3VuZCI6IiNlYmMzNjgifSwiaWQiOiJpbWdfY2FyZCJ9LCJNMTUwMTQ5NTg5OTYwMyI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZWJjMzY4IiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTQ5NTg5OTYwMyI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNDk1ODk5NjA0Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTQ5NTg5OTYwNSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE0OTU5MTc1MzkiOnsic3R5bGUiOnsiaGVpZ2h0IjoiMjAiLCJiYWNrZ3JvdW5kIjoiI2ViYzM2OCJ9LCJpZCI6ImJsYW5rIn0sIk0xNTAxNDk2ODYyNTIzIjp7InBhcmFtcyI6eyJjb250ZW50IjoiUEdScGRpQmpiR0Z6Y3owaVlXTjBhWFpwZEhrdGNuVnNaUzEwYVhSc1pTQmliM0prWlhJdE1YQjRMV0lpSUhOMGVXeGxQU0ozYUdsMFpTMXpjR0ZqWlRvZ2JtOXliV0ZzT3lCaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95QmliM0prWlhJdFltOTBkRzl0T2lBeGNIZ2djMjlzYVdRZ2NtZGlLREl5TkN3Z01qSTBMQ0F5TWpRcE95Qm9aV2xuYUhRNklERXVPSEpsYlRzZ2JHbHVaUzFvWldsbmFIUTZJREV1T0hKbGJUc2dabTl1ZEMxemFYcGxPaUF3TGpoeVpXMDdJR052Ykc5eU9pQnlaMklvTlRFc0lEVXhMQ0ExTVNrN0lIUmxlSFF0WVd4cFoyNDZJR05sYm5SbGNqc2dabTl1ZEMxM1pXbG5hSFE2SUdKdmJHUTdJR1p2Ym5RdFptRnRhV3g1T2lBbWNYVnZkRHROYVdOeWIzTnZablFnV1dGb1pXa21jWFZ2ZERzc0lPVytydWk5cittYmhlbTdrU3dnNWE2TDVMMlRMQ0JVWVdodmJXRXNJRUZ5YVdGc0xDQklaV3gyWlhScFkyRXNJRk5VU0dWcGRHazdJajdtdEx2bGlxam9wNFRsaUprOEwyUnBkajQ4WkdsMklHTnNZWE56UFNKaFkzUnBkbWwwZVMxeWRXeGxMV052Ym5SbGJuUWlJSE4wZVd4bFBTSjNhR2wwWlMxemNHRmpaVG9nYm05eWJXRnNPeUJpYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCd1lXUmthVzVuT2lBd0xqaHlaVzA3SUd4cGJtVXRhR1ZwWjJoME9pQXhjbVZ0T3lCbWIyNTBMWE5wZW1VNklEQXVOM0psYlRzZ1kyOXNiM0k2SUhKbllpZzFNU3dnTlRFc0lEVXhLVHNnWm05dWRDMW1ZVzFwYkhrNklDWnhkVzkwTzAxcFkzSnZjMjltZENCWllXaGxhU1p4ZFc5ME95d2c1YjZ1NkwydjZadUY2YnVSTENEbHJvdmt2Wk1zSUZSaGFHOXRZU3dnUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnVTFSSVpXbDBhVHNpUGpFdTVyUzc1WXFvNXBlMjZaZTA3N3lhTithY2lERXk1cGVsNzd5Tk4rYWNpREUyNXBlbEptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQakl1NXJTNzVZcW82SXlENVp1MDc3eWE1b21BNkthRzU1dVc1WitPNWJpQ0ptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQak11NXJTNzVZcW81WWFGNWE2NTc3eWE2S1dcLzU1T2NNQzQ1T2VXRmcraTF0Kys4ak9hWG9PYTB1K1dLcU9XVGdlUyttK2U3bWVXY3NPV011dVM3cGVXdW51bVpoZVM3dCthZ3ZPUzR1dVdIaGladVluTndPenhpY2lCemRIbHNaVDBpWW05NExYTnBlbWx1WnpvZ1ltOXlaR1Z5TFdKdmVEc2dMWGRsWW10cGRDMTBZWEF0YUdsbmFHeHBaMmgwTFdOdmJHOXlPaUIwY21GdWMzQmhjbVZ1ZERzaUx6NDBMdVM4bU9hRG9PUzdoZW1aa09lK2p1V2JvdVdrbHVXTmx1V1BpdWUranVXYm9rRndjT1M0aStXTmxlUzRsT21BaWVhTHFlV2NxT2U2dithVXIrUzdtT2VhaE9pdW91V05sZVM2cStXUGx6d3ZaR2wyUGc9PSJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ViYzM2OCIsInBhZGRpbmciOiIxNSIsInBhZGRpbmdsZWZ0IjoiMTUiLCJwYWRkaW5ndG9wIjoiMTUifSwiaWQiOiJyaWNodGV4dCJ9fX0=',	'../addons/we7_wmall/plugin/diypage/static/template/default3/preview.jpg',	'10003'),
(4,	0,	1,	'系统模板04',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTQ5NzQzNTU4OCI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNDk3NDM1NTg5Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0NFwvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTQ5NzQ2OTkwNyI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ0XC9pbWctY2FyZC0xLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2EyZDlmMiJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDk3NTMzMjk4Ijp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2EyZDlmMiIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjIiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE0OTc1MzMyOTgiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDk3NTMzMjk5Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5NzUzMzMwMCI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTc1MzMzMDEiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5NzU1OTk0NSI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ0XC9pbWctY2FyZC0yLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2EyZDlmMiJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDk3NTg1NjQ2Ijp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2EyZDlmMiIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjEiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE0OTc1ODU2NDYiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNDk3NTg1NjQ3Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTQ5NzU4NTY0OCI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE0OTc1ODU2NDkiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTQ5NzYwNjQxNSI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ0XC9pbWctY2FyZC0zLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2EyZDlmMiJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNDk3NjI2MTA2Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNhMmQ5ZjIiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNDk3NjI2MTA2Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE0OTc2MjYxMDciOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNDk3NjI2MTA4Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1MjQxNzE2NiI6eyJzdHlsZSI6eyJoZWlnaHQiOiIxMCIsImJhY2tncm91bmQiOiIjYTFkOWYyIn0sImlkIjoiYmxhbmsifSwiTTE1MDE0OTc2NjY4NjgiOnsic3R5bGUiOnsicGFkZGluZ3RvcCI6IjEwIiwicGFkZGluZ2xlZnQiOiIxNSIsImJhY2tncm91bmQiOiIjYTJkOWYyIn0sImRhdGEiOnsiQzE1MDE0OTc2NjY4NjgiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ0XC9iYW5uZXItMi1sb2dvLmpwZyIsImxpbmt1cmwiOiIifX0sImlkIjoiYmFubmVyIn0sIk0xNTAxNDk3NzUzNTI0Ijp7InBhcmFtcyI6eyJjb250ZW50IjoiUEdScGRpQmpiR0Z6Y3owaVlXTjBhWFpwZEhrdGNuVnNaUzEwYVhSc1pTQmliM0prWlhJdE1YQjRMV0lpSUhOMGVXeGxQU0ozYUdsMFpTMXpjR0ZqWlRvZ2JtOXliV0ZzT3lCaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95QmliM0prWlhJdFltOTBkRzl0T2lBeGNIZ2djMjlzYVdRZ2NtZGlLREl5TkN3Z01qSTBMQ0F5TWpRcE95Qm9aV2xuYUhRNklERXVPSEpsYlRzZ2JHbHVaUzFvWldsbmFIUTZJREV1T0hKbGJUc2dabTl1ZEMxemFYcGxPaUF3TGpoeVpXMDdJR052Ykc5eU9pQnlaMklvTlRFc0lEVXhMQ0ExTVNrN0lIUmxlSFF0WVd4cFoyNDZJR05sYm5SbGNqc2dabTl1ZEMxM1pXbG5hSFE2SUdKdmJHUTdJR1p2Ym5RdFptRnRhV3g1T2lBbWNYVnZkRHROYVdOeWIzTnZablFnV1dGb1pXa21jWFZ2ZERzc0lPVytydWk5cittYmhlbTdrU3dnNWE2TDVMMlRMQ0JVWVdodmJXRXNJRUZ5YVdGc0xDQklaV3gyWlhScFkyRXNJRk5VU0dWcGRHazdJajdtdEx2bGlxam9wNFRsaUprOEwyUnBkajQ4WkdsMklHTnNZWE56UFNKaFkzUnBkbWwwZVMxeWRXeGxMV052Ym5SbGJuUWlJSE4wZVd4bFBTSjNhR2wwWlMxemNHRmpaVG9nYm05eWJXRnNPeUJpYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCd1lXUmthVzVuT2lBd0xqaHlaVzA3SUd4cGJtVXRhR1ZwWjJoME9pQXhjbVZ0T3lCbWIyNTBMWE5wZW1VNklEQXVOM0psYlRzZ1kyOXNiM0k2SUhKbllpZzFNU3dnTlRFc0lEVXhLVHNnWm05dWRDMW1ZVzFwYkhrNklDWnhkVzkwTzAxcFkzSnZjMjltZENCWllXaGxhU1p4ZFc5ME95d2c1YjZ1NkwydjZadUY2YnVSTENEbHJvdmt2Wk1zSUZSaGFHOXRZU3dnUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnVTFSSVpXbDBhVHNpUGpFdTVyUzc1WXFvNXBlMjZaZTA3N3lhTithY2lERXk1cGVsNzd5Tk4rYWNpREUyNXBlbEptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQakl1NXJTNzVZcW82SXlENVp1MDc3eWE1b21BNkthRzU1dVc1WitPNWJpQ0ptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQak11NXJTNzVZcW81WWFGNWE2NTc3eWE2S1dcLzU1T2NNQzQ1T2VXRmcraTF0Kys4ak9hWG9PYTB1K1dLcU9XVGdlUyttK2U3bWVXY3NPV011dVM3cGVXdW51bVpoZVM3dCthZ3ZPUzR1dVdIaGladVluTndPenhpY2lCemRIbHNaVDBpWW05NExYTnBlbWx1WnpvZ1ltOXlaR1Z5TFdKdmVEc2dMWGRsWW10cGRDMTBZWEF0YUdsbmFHeHBaMmgwTFdOdmJHOXlPaUIwY21GdWMzQmhjbVZ1ZERzaUx6NDBMdVM4bU9hRG9PUzdoZW1aa09lK2p1V2JvdVdrbHVXTmx1V1BpdWUranVXYm9rRndjT1M0aStXTmxlUzRsT21BaWVhTHFlV2NxT2U2dithVXIrUzdtT2VhaE9pdW91V05sZVM2cStXUGx6d3ZaR2wyUGc9PSJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ExZDlmMiIsInBhZGRpbmciOiIxNSIsInBhZGRpbmdsZWZ0IjoiMTUiLCJwYWRkaW5ndG9wIjoiMTUifSwiaWQiOiJyaWNodGV4dCJ9fX0=',	'../addons/we7_wmall/plugin/diypage/static/template/default4/preview.jpg',	'10004'),
(5,	0,	1,	'系统模板05',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTU1MjgyODUzNSI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNTUyODI4NTM2Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0NVwvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTU1MjkyNDA3OCI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ1XC9pbWctY2FyZC0xLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzZmZDljZSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTUzMDk1MzIwIjp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiM2ZmQ5Y2UiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNTUzMDk1MzIwIjp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE1NTMwOTUzMjEiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNTUzMDk1MzIyIjp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1MzExNzQ0NiI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ1XC9pbWctY2FyZC0yLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzZmZDljZSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTUzMTQ4NTc1Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiM2ZmQ5Y2UiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNTUzMTQ4NTc1Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE1NTMxNDg1NzYiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNTUzMTQ4NTc3Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1MzIyNTcxMyI6eyJwYXJhbXMiOnsiY29udGVudCI6IlBHUnBkaUJqYkdGemN6MGlZV04wYVhacGRIa3RjblZzWlMxMGFYUnNaU0JpYjNKa1pYSXRNWEI0TFdJaUlITjBlV3hsUFNKM2FHbDBaUzF6Y0dGalpUb2dibTl5YldGc095QmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUJpYjNKa1pYSXRZbTkwZEc5dE9pQXhjSGdnYzI5c2FXUWdjbWRpS0RJeU5Dd2dNakkwTENBeU1qUXBPeUJvWldsbmFIUTZJREV1T0hKbGJUc2diR2x1WlMxb1pXbG5hSFE2SURFdU9ISmxiVHNnWm05dWRDMXphWHBsT2lBd0xqaHlaVzA3SUdOdmJHOXlPaUJ5WjJJb05URXNJRFV4TENBMU1TazdJSFJsZUhRdFlXeHBaMjQ2SUdObGJuUmxjanNnWm05dWRDMTNaV2xuYUhRNklHSnZiR1E3SUdadmJuUXRabUZ0YVd4NU9pQW1jWFZ2ZER0TmFXTnliM052Wm5RZ1dXRm9aV2ttY1hWdmREc3NJT1crcnVpOXIrbWJoZW03a1N3ZzVhNkw1TDJUTENCVVlXaHZiV0VzSUVGeWFXRnNMQ0JJWld4MlpYUnBZMkVzSUZOVVNHVnBkR2s3SWo3bXRMdmxpcWpvcDRUbGlKazhMMlJwZGo0OFpHbDJJR05zWVhOelBTSmhZM1JwZG1sMGVTMXlkV3hsTFdOdmJuUmxiblFpSUhOMGVXeGxQU0ozYUdsMFpTMXpjR0ZqWlRvZ2JtOXliV0ZzT3lCaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95QndZV1JrYVc1bk9pQXdMamh5WlcwN0lHeHBibVV0YUdWcFoyaDBPaUF4Y21WdE95Qm1iMjUwTFhOcGVtVTZJREF1TjNKbGJUc2dZMjlzYjNJNklISm5ZaWcxTVN3Z05URXNJRFV4S1RzZ1ptOXVkQzFtWVcxcGJIazZJQ1p4ZFc5ME8wMXBZM0p2YzI5bWRDQlpZV2hsYVNaeGRXOTBPeXdnNWI2dTZMMnY2WnVGNmJ1UkxDRGxyb3ZrdlpNc0lGUmhhRzl0WVN3Z1FYSnBZV3dzSUVobGJIWmxkR2xqWVN3Z1UxUklaV2wwYVRzaVBqRXU1clM3NVlxbzVwZTI2WmUwNzd5YU4rYWNpREV5NXBlbDc3eU5OK2FjaURFMjVwZWxKbTVpYzNBN1BHSnlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUl2UGpJdTVyUzc1WXFvNkl5RDVadTA3N3lhNW9tQTZLYUc1NXVXNVorTzViaUNKbTVpYzNBN1BHSnlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUl2UGpNdTVyUzc1WXFvNVlhRjVhNjU3N3lhNktXXC81NU9jTUM0NU9lV0ZnK2kxdCsrOGpPYVhvT2EwdStXS3FPV1RnZVMrbStlN21lV2NzT1dNdXVTN3BlV3VudW1aaGVTN3QrYWd2T1M0dXVXSGhpWnVZbk53T3p4aWNpQnpkSGxzWlQwaVltOTRMWE5wZW1sdVp6b2dZbTl5WkdWeUxXSnZlRHNnTFhkbFltdHBkQzEwWVhBdGFHbG5hR3hwWjJoMExXTnZiRzl5T2lCMGNtRnVjM0JoY21WdWREc2lMejQwTHVTOG1PYURvT1M3aGVtWmtPZStqdVdib3VXa2x1V05sdVdQaXVlK2p1V2Jva0Z3Y09TNGkrV05sZVM0bE9tQWllYUxxZVdjcU9lNnYrYVVyK1M3bU9lYWhPaXVvdVdObGVTNnErV1Bsend2WkdsMlBnPT0ifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiM2ZmQ5Y2UiLCJwYWRkaW5nbGVmdCI6IjE1IiwicGFkZGluZ3RvcCI6IjE1In0sImlkIjoicmljaHRleHQifX19',	'../addons/we7_wmall/plugin/diypage/static/template/default5/preview.jpg',	'10005'),
(6,	0,	1,	'系统模板06',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTU1MzQyMjA3MiI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNTUzNDIyMDcyIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0NlwvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTU1MzQ1NjMzOCI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ2XC9pbWctY2FyZC0xLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2ZkYmFhZiJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTUzNTIzMTkxIjp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1Njc2NVx1NGUwMFx1NGVmZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZkYmFhZiIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjIiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE1NTM1MjMxOTEiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNTUzNTIzMTkyIjp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTU1MzUyMzE5NCI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE1NTM1MjMxOTUiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTU1MzU5NjAzMCI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ2XC9pbWctY2FyZC0yLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2ZkYmFhZiJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTUzNjI2MTYxIjp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1N2FjYlx1NTM3M1x1NjJhMlx1OGQyZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZkYmFhZiIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjEiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE1NTM2MjYxNjEiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNTUzNjI2MTYyIjp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTU1MzYyNjE2MyI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE1NTM2MjYxNjQiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTU1MzY1MjExMCI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ2XC9pbWctY2FyZC0zLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2ZkYmFhZiJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTUzNjgxMDI0Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNmZGJhYWYiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNTUzNjgxMDI0Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE1NTM2ODEwMjUiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNTUzNjgxMDI2Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1MzcwMDg2NCI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjE1IiwiYmFja2dyb3VuZCI6IiNmZGJhYWYifSwiZGF0YSI6eyJDMTUwMTU1MzcwMDg2NCI6eyJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC90ZW1wbGF0ZVwvZGVmYXVsdDZcL2Jhbm5lci0yLWxvZ28uanBnIiwibGlua3VybCI6IiJ9fSwiaWQiOiJiYW5uZXIifSwiTTE1MDE1NTM3NTUyMzAiOnsicGFyYW1zIjp7ImNvbnRlbnQiOiJQR1JwZGlCamJHRnpjejBpWVdOMGFYWnBkSGt0Y25Wc1pTMTBhWFJzWlNCaWIzSmtaWEl0TVhCNExXSWlJSE4wZVd4bFBTSjNhR2wwWlMxemNHRmpaVG9nYm05eWJXRnNPeUJpYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCaWIzSmtaWEl0WW05MGRHOXRPaUF4Y0hnZ2MyOXNhV1FnY21kaUtESXlOQ3dnTWpJMExDQXlNalFwT3lCb1pXbG5hSFE2SURFdU9ISmxiVHNnYkdsdVpTMW9aV2xuYUhRNklERXVPSEpsYlRzZ1ptOXVkQzF6YVhwbE9pQXdMamh5WlcwN0lHTnZiRzl5T2lCeVoySW9OVEVzSURVeExDQTFNU2s3SUhSbGVIUXRZV3hwWjI0NklHTmxiblJsY2pzZ1ptOXVkQzEzWldsbmFIUTZJR0p2YkdRN0lHWnZiblF0Wm1GdGFXeDVPaUFtY1hWdmREdE5hV055YjNOdlpuUWdXV0ZvWldrbWNYVnZkRHNzSU9XK3J1aTlyK21iaGVtN2tTd2c1YTZMNUwyVExDQlVZV2h2YldFc0lFRnlhV0ZzTENCSVpXeDJaWFJwWTJFc0lGTlVTR1ZwZEdrN0lqN210THZsaXFqb3A0VGxpSms4TDJScGRqNDhaR2wySUdOc1lYTnpQU0poWTNScGRtbDBlUzF5ZFd4bExXTnZiblJsYm5RaUlITjBlV3hsUFNKM2FHbDBaUzF6Y0dGalpUb2dibTl5YldGc095QmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUJ3WVdSa2FXNW5PaUF3TGpoeVpXMDdJR3hwYm1VdGFHVnBaMmgwT2lBeGNtVnRPeUJtYjI1MExYTnBlbVU2SURBdU4zSmxiVHNnWTI5c2IzSTZJSEpuWWlnMU1Td2dOVEVzSURVeEtUc2dabTl1ZEMxbVlXMXBiSGs2SUNaeGRXOTBPMDFwWTNKdmMyOW1kQ0JaWVdobGFTWnhkVzkwT3l3ZzViNnU2TDJ2Nlp1RjZidVJMQ0Rscm92a3ZaTXNJRlJoYUc5dFlTd2dRWEpwWVd3c0lFaGxiSFpsZEdsallTd2dVMVJJWldsMGFUc2lQakV1NXJTNzVZcW81cGUyNlplMDc3eWFOK2FjaURFeTVwZWw3N3lOTithY2lERTI1cGVsSm01aWMzQTdQR0p5SUhOMGVXeGxQU0ppYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lJdlBqSXU1clM3NVlxbzZJeUQ1WnUwNzd5YTVvbUE2S2FHNTV1VzVaK081YmlDSm01aWMzQTdQR0p5SUhOMGVXeGxQU0ppYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lJdlBqTXU1clM3NVlxbzVZYUY1YTY1Nzd5YTZLV1wvNTVPY01DNDVPZVdGZytpMXQrKzhqT2FYb09hMHUrV0txT1dUZ2VTK20rZTdtZVdjc09XTXV1UzdwZVd1bnVtWmhlUzd0K2Fndk9TNHV1V0hoaVp1WW5Od096eGljaUJ6ZEhsc1pUMGlZbTk0TFhOcGVtbHVaem9nWW05eVpHVnlMV0p2ZURzZ0xYZGxZbXRwZEMxMFlYQXRhR2xuYUd4cFoyaDBMV052Ykc5eU9pQjBjbUZ1YzNCaGNtVnVkRHNpTHo0MEx1UzhtT2FEb09TN2hlbVprT2UranVXYm91V2tsdVdObHVXUGl1ZStqdVdib2tGd2NPUzRpK1dObGVTNGxPbUFpZWFMcWVXY3FPZTZ2K2FVcitTN21PZWFoT2l1b3VXTmxlUzZxK1dQbHp3dlpHbDJQZz09In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmRiYWFmIiwicGFkZGluZ2xlZnQiOiIxNSIsInBhZGRpbmd0b3AiOiIwIn0sImlkIjoicmljaHRleHQifSwiTTE1MDE1NTM3ODE4NjIiOnsic3R5bGUiOnsiaGVpZ2h0IjoiMjAiLCJiYWNrZ3JvdW5kIjoiI2ZkYmFhZiJ9LCJpZCI6ImJsYW5rIn19fQ==',	'../addons/we7_wmall/plugin/diypage/static/template/default6/preview.jpg',	'10006'),
(7,	0,	1,	'系统模板07',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTU1NDAwNDI4OCI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNTU0MDA0Mjg4Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0N1wvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTU1NDA3NTU5OCI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ3XC9pbWctY2FyZC0xLnBuZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzZmYTU3MSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTU0MTEzMjU2Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiM2ZmE1NzEiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNTU0MTEzMjU2Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE1NTQxMTMyNTciOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNTU0MTEzMjU4Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1NDE1NjQwNiI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ3XC9pbWctY2FyZC0yLnBuZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiIzZmYTU3MSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTU0MTg3NDA4Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiM2ZmE1NzEiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNTU0MTg3NDA4Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE1NTQxODc0MDkiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNTU0MTg3NDEwIjp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1NDI2NTYxNSI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjNmZhNTcxIiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTU1NDI2NTYxNiI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNTU0MjY1NjE3Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTU1NDI2NTYxOCI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE1NTQyMDQ0NzkiOnsicGFyYW1zIjp7ImNvbnRlbnQiOiJQR1JwZGlCamJHRnpjejBpWVdOMGFYWnBkSGt0Y25Wc1pTMTBhWFJzWlNCaWIzSmtaWEl0TVhCNExXSWlJSE4wZVd4bFBTSjNhR2wwWlMxemNHRmpaVG9nYm05eWJXRnNPeUJpYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCaWIzSmtaWEl0WW05MGRHOXRPaUF4Y0hnZ2MyOXNhV1FnY21kaUtESXlOQ3dnTWpJMExDQXlNalFwT3lCb1pXbG5hSFE2SURFdU9ISmxiVHNnYkdsdVpTMW9aV2xuYUhRNklERXVPSEpsYlRzZ1ptOXVkQzF6YVhwbE9pQXdMamh5WlcwN0lHTnZiRzl5T2lCeVoySW9OVEVzSURVeExDQTFNU2s3SUhSbGVIUXRZV3hwWjI0NklHTmxiblJsY2pzZ1ptOXVkQzEzWldsbmFIUTZJR0p2YkdRN0lHWnZiblF0Wm1GdGFXeDVPaUFtY1hWdmREdE5hV055YjNOdlpuUWdXV0ZvWldrbWNYVnZkRHNzSU9XK3J1aTlyK21iaGVtN2tTd2c1YTZMNUwyVExDQlVZV2h2YldFc0lFRnlhV0ZzTENCSVpXeDJaWFJwWTJFc0lGTlVTR1ZwZEdrN0lqN210THZsaXFqb3A0VGxpSms4TDJScGRqNDhaR2wySUdOc1lYTnpQU0poWTNScGRtbDBlUzF5ZFd4bExXTnZiblJsYm5RaUlITjBlV3hsUFNKM2FHbDBaUzF6Y0dGalpUb2dibTl5YldGc095QmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUJ3WVdSa2FXNW5PaUF3TGpoeVpXMDdJR3hwYm1VdGFHVnBaMmgwT2lBeGNtVnRPeUJtYjI1MExYTnBlbVU2SURBdU4zSmxiVHNnWTI5c2IzSTZJSEpuWWlnMU1Td2dOVEVzSURVeEtUc2dabTl1ZEMxbVlXMXBiSGs2SUNaeGRXOTBPMDFwWTNKdmMyOW1kQ0JaWVdobGFTWnhkVzkwT3l3ZzViNnU2TDJ2Nlp1RjZidVJMQ0Rscm92a3ZaTXNJRlJoYUc5dFlTd2dRWEpwWVd3c0lFaGxiSFpsZEdsallTd2dVMVJJWldsMGFUc2lQakV1NXJTNzVZcW81cGUyNlplMDc3eWFOK2FjaURFeTVwZWw3N3lOTithY2lERTI1cGVsSm01aWMzQTdQR0p5SUhOMGVXeGxQU0ppYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lJdlBqSXU1clM3NVlxbzZJeUQ1WnUwNzd5YTVvbUE2S2FHNTV1VzVaK081YmlDSm01aWMzQTdQR0p5SUhOMGVXeGxQU0ppYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lJdlBqTXU1clM3NVlxbzVZYUY1YTY1Nzd5YTZLV1wvNTVPY01DNDVPZVdGZytpMXQrKzhqT2FYb09hMHUrV0txT1dUZ2VTK20rZTdtZVdjc09XTXV1UzdwZVd1bnVtWmhlUzd0K2Fndk9TNHV1V0hoaVp1WW5Od096eGljaUJ6ZEhsc1pUMGlZbTk0TFhOcGVtbHVaem9nWW05eVpHVnlMV0p2ZURzZ0xYZGxZbXRwZEMxMFlYQXRhR2xuYUd4cFoyaDBMV052Ykc5eU9pQjBjbUZ1YzNCaGNtVnVkRHNpTHo0MEx1UzhtT2FEb09TN2hlbVprT2UranVXYm91V2tsdVdObHVXUGl1ZStqdVdib2tGd2NPUzRpK1dObGVTNGxPbUFpZWFMcWVXY3FPZTZ2K2FVcitTN21PZWFoT2l1b3VXTmxlUzZxK1dQbHp3dlpHbDJQZz09In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjNmZhNTcxIiwicGFkZGluZ2xlZnQiOiIxNSIsInBhZGRpbmd0b3AiOiIxNSJ9LCJpZCI6InJpY2h0ZXh0In19fQ==',	'../addons/we7_wmall/plugin/diypage/static/template/default7/preview.jpg',	'10007'),
(8,	0,	1,	'系统模板08',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTU1NDQwNzA2NCI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNTU0NDA3MDY0Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0OFwvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn0sIk0xNTAxNTU0NTUzNzU5Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0OFwvYmFubmVyLTItbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTU1NDU3MDA5NiI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ4XC9pbWctY2FyZC0xLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiNSIsInBhZGRpbmdsZWZ0IjoiMCIsImJhY2tncm91bmQiOiIjZmY5YzdmIn0sImlkIjoiaW1nX2NhcmQifSwiTTE1MDE1NTQ3Mzg5ODMiOnsic3R5bGUiOnsiaGVpZ2h0IjoiMTAiLCJiYWNrZ3JvdW5kIjoiI2ZmOWM3ZiJ9LCJpZCI6ImJsYW5rIn0sIk0xNTAxNTU0NjE3Mjk3Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIiwic2hvd2hvdGdvb2RzIjoiMSIsInN0b3JlZGF0YSI6IjAiLCJzdG9yZW51bSI6IjYifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNmZjljN2YiLCJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMTUiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiJ9LCJkYXRhIjp7IkMxNTAxNTU0NjE3Mjk3Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7Imljb24iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3RlbXBsYXRlXC9tb2JpbGVcL3dtYWxsXC9kZWZhdWx0XC9zdGF0aWNcL2ltZ1wvaWNvbi1kaXNjb3VudC5wbmciLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tY291cG9uQ29sbGVjdC5wbmciLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifSwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fSwiQzE1MDE1NTQ2MTcyOTgiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjQ1In0sIkMxNTAxNTU0NjE3Mjk5Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI1NSIsImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy01LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19fX0sImlkIjoid2FpbWFpX3N0b3JlcyJ9LCJNMTUwMTU1NDY1MjkwNSI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmY5YzdmIiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTU1NDY1MjkwNSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNTU0NjUyOTA2Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTU1NDY1MjkwNyI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE1NTQ2NjkxNjEiOnsicGFyYW1zIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0OFwvaW1nLWNhcmQtMi5qcGcifSwic3R5bGUiOnsicGFkZGluZ3RvcCI6IjE1IiwicGFkZGluZ2xlZnQiOiIwIiwiYmFja2dyb3VuZCI6IiNmZjljN2YifSwiaWQiOiJpbWdfY2FyZCJ9LCJNMTUwMTU1NTIzNjQzMiI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmY5YzdmIiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTU1NTIzNjQzMyI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNTU1MjM2NDM0Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTU1NTIzNjQzNSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE1NTUyNTEyMzEiOnsicGFyYW1zIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0OFwvaW1nLWNhcmQtMy5qcGcifSwic3R5bGUiOnsicGFkZGluZ3RvcCI6IjE1IiwicGFkZGluZ2xlZnQiOiIwIiwiYmFja2dyb3VuZCI6IiNmZjljN2YifSwiaWQiOiJpbWdfY2FyZCJ9LCJNMTUwMTU1NTMzNzEyMiI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjEiLCJzdG9yZWRhdGEiOiIwIiwic3RvcmVudW0iOiI2In0sInN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmY5YzdmIiwicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjE1IiwidGl0bGVjb2xvciI6IiMzMzMiLCJzY29yZWNvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGViZ2NvbG9yIjoiI2ZmMmQ0YiIsImRlbGl2ZXJ5dGl0bGVjb2xvciI6IiNmZmYifSwiZGF0YSI6eyJDMTUwMTU1NTMzNzEyMiI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiMzAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJpY29uIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvbW9iaWxlXC93bWFsbFwvZGVmYXVsdFwvc3RhdGljXC9pbWdcL2ljb24tZGlzY291bnQucG5nIiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsiaWNvbiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvdGVtcGxhdGVcL21vYmlsZVwvd21hbGxcL2RlZmF1bHRcL3N0YXRpY1wvaW1nXC9pY29uLWNvdXBvbkNvbGxlY3QucG5nIiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTAxNTU1MzM3MTIzIjp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiI0NSJ9LCJDMTUwMTU1NTMzNzEyNCI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNTUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJpZCI6IndhaW1haV9zdG9yZXMifSwiTTE1MDE1NTU0MjU3MjciOnsic3R5bGUiOnsiaGVpZ2h0IjoiMTAiLCJiYWNrZ3JvdW5kIjoiI2ZmOWM3ZiJ9LCJpZCI6ImJsYW5rIn0sIk0xNTAxNTU1MzU2Mzk2Ijp7InBhcmFtcyI6eyJjb250ZW50IjoiUEdScGRpQmpiR0Z6Y3owaVlXTjBhWFpwZEhrdGNuVnNaUzEwYVhSc1pTQmliM0prWlhJdE1YQjRMV0lpSUhOMGVXeGxQU0ozYUdsMFpTMXpjR0ZqWlRvZ2JtOXliV0ZzT3lCaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95QmliM0prWlhJdFltOTBkRzl0T2lBeGNIZ2djMjlzYVdRZ2NtZGlLREl5TkN3Z01qSTBMQ0F5TWpRcE95Qm9aV2xuYUhRNklERXVPSEpsYlRzZ2JHbHVaUzFvWldsbmFIUTZJREV1T0hKbGJUc2dabTl1ZEMxemFYcGxPaUF3TGpoeVpXMDdJR052Ykc5eU9pQnlaMklvTlRFc0lEVXhMQ0ExTVNrN0lIUmxlSFF0WVd4cFoyNDZJR05sYm5SbGNqc2dabTl1ZEMxM1pXbG5hSFE2SUdKdmJHUTdJR1p2Ym5RdFptRnRhV3g1T2lBbWNYVnZkRHROYVdOeWIzTnZablFnV1dGb1pXa21jWFZ2ZERzc0lPVytydWk5cittYmhlbTdrU3dnNWE2TDVMMlRMQ0JVWVdodmJXRXNJRUZ5YVdGc0xDQklaV3gyWlhScFkyRXNJRk5VU0dWcGRHazdJajdtdEx2bGlxam9wNFRsaUprOEwyUnBkajQ4WkdsMklHTnNZWE56UFNKaFkzUnBkbWwwZVMxeWRXeGxMV052Ym5SbGJuUWlJSE4wZVd4bFBTSjNhR2wwWlMxemNHRmpaVG9nYm05eWJXRnNPeUJpYjNndGMybDZhVzVuT2lCaWIzSmtaWEl0WW05NE95QXRkMlZpYTJsMExYUmhjQzFvYVdkb2JHbG5hSFF0WTI5c2IzSTZJSFJ5WVc1emNHRnlaVzUwT3lCd1lXUmthVzVuT2lBd0xqaHlaVzA3SUd4cGJtVXRhR1ZwWjJoME9pQXhjbVZ0T3lCbWIyNTBMWE5wZW1VNklEQXVOM0psYlRzZ1kyOXNiM0k2SUhKbllpZzFNU3dnTlRFc0lEVXhLVHNnWm05dWRDMW1ZVzFwYkhrNklDWnhkVzkwTzAxcFkzSnZjMjltZENCWllXaGxhU1p4ZFc5ME95d2c1YjZ1NkwydjZadUY2YnVSTENEbHJvdmt2Wk1zSUZSaGFHOXRZU3dnUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnVTFSSVpXbDBhVHNpUGpFdTVyUzc1WXFvNXBlMjZaZTA3N3lhTithY2lERXk1cGVsNzd5Tk4rYWNpREUyNXBlbEptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQakl1NXJTNzVZcW82SXlENVp1MDc3eWE1b21BNkthRzU1dVc1WitPNWJpQ0ptNWljM0E3UEdKeUlITjBlV3hsUFNKaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95SXZQak11NXJTNzVZcW81WWFGNWE2NTc3eWE2S1dcLzU1T2NNQzQ1T2VXRmcraTF0Kys4ak9hWG9PYTB1K1dLcU9XVGdlUyttK2U3bWVXY3NPV011dVM3cGVXdW51bVpoZVM3dCthZ3ZPUzR1dVdIaGladVluTndPenhpY2lCemRIbHNaVDBpWW05NExYTnBlbWx1WnpvZ1ltOXlaR1Z5TFdKdmVEc2dMWGRsWW10cGRDMTBZWEF0YUdsbmFHeHBaMmgwTFdOdmJHOXlPaUIwY21GdWMzQmhjbVZ1ZERzaUx6NDBMdVM4bU9hRG9PUzdoZW1aa09lK2p1V2JvdVdrbHVXTmx1V1BpdWUranVXYm9rRndjT1M0aStXTmxlUzRsT21BaWVhTHFlV2NxT2U2dithVXIrUzdtT2VhaE9pdW91V05sZVM2cStXUGx6d3ZaR2wyUGc9PSJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZmOWM3ZiIsInBhZGRpbmdsZWZ0IjoiMTUiLCJwYWRkaW5ndG9wIjoiMTUifSwiaWQiOiJyaWNodGV4dCJ9fX0=',	'../addons/we7_wmall/plugin/diypage/static/template/default8/preview.jpg',	'10008'),
(9,	0,	1,	'系统模板09',	'eyJwYWdlIjp7InR5cGUiOiIwIiwidGl0bGUiOiJcdThiZjdcdThmOTNcdTUxNjVcdTk4NzVcdTk3NjJcdTY4MDdcdTk4OTgiLCJuYW1lIjoiXHU2NzJhXHU1NDdkXHU1NDBkXHU5ODc1XHU5NzYyIiwiZGVzYyI6IiIsInRodW1iIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjZmFmYWZhIiwiZGl5bWVudSI6Ii0xIiwiZGFubXUiOiIwIiwiZGl5Z290b3AiOiIwIiwiZm9sbG93YmFyIjoiMCJ9LCJpdGVtcyI6eyJNMTUwMTU1NTYwNjgyNSI6eyJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMCIsInBhZGRpbmdsZWZ0IjoiMCJ9LCJkYXRhIjp7IkMxNTAxNTU1NjA2ODI2Ijp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL3RlbXBsYXRlXC9kZWZhdWx0OVwvYmFubmVyLTEtbG9nby5qcGciLCJsaW5rdXJsIjoiIn19LCJpZCI6ImJhbm5lciJ9LCJNMTUwMTU1NTY1MzMwMyI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ5XC9pbWctY2FyZC0xLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2MzYmNiYSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTU1NjkzMzc1Ijp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1Njc2NVx1NGUwMFx1NGVmZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2MzYmNiYSIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjEiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE1NTU2OTMzNzUiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNTU1NjkzMzc2Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTU1NTY5MzM3NyI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE1NTU2OTMzNzgiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIk0xNTAxNTU1ODQzNTI3Ijp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJNMTUwMTU1NTg0NDg3MCI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiTTE1MDE1NTU4NDY2MDYiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn19LCJpZCI6IndhaW1haV9nb29kcyJ9LCJNMTUwMTU1NTczNzA3MiI6eyJwYXJhbXMiOnsiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvdGVtcGxhdGVcL2RlZmF1bHQ5XC9pbWctY2FyZC0yLmpwZyJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTUiLCJwYWRkaW5nbGVmdCI6IjAiLCJiYWNrZ3JvdW5kIjoiI2MzYmNiYSJ9LCJpZCI6ImltZ19jYXJkIn0sIk0xNTAxNTU1NzYwNTAyIjp7InBhcmFtcyI6eyJnb29kc3R5cGUiOiIwIiwic2hvd3RpdGxlIjoiMSIsInNob3dwcmljZSI6IjEiLCJzaG93b2xkcHJpY2UiOiIxIiwic2hvd3RhZyI6IjAiLCJnb29kc2RhdGEiOiIwIiwiZ29vZHNzb3J0IjoiMCIsImdvb2RzbnVtIjoiNiIsInNob3dpY29uIjoiMSIsImljb25wb3NpdGlvbiI6ImxlZnQgdG9wIiwiYnV5YnRudGV4dCI6Ilx1Njc2NVx1NGUwMFx1NGVmZCIsImdvb2RzaWNvbnNyYyI6IiJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2MzYmNiYSIsInBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIxNSIsImxpc3RzdHlsZSI6IjEiLCJnb29kc2ljb24iOiJyZWNvbW1hbmQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmI0ZTQ0Iiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmYjRlNDQiLCJpY29ucGFkZGluZ3RvcCI6IjAiLCJpY29ucGFkZGluZ2xlZnQiOiIwIiwiaWNvbnpvb20iOiIxMDAiLCJ0YWdiYWNrZ3JvdW5kIjoiI2ZlNTQ1NSIsInNhbGVzY29sb3IiOiIjNzc3Nzc3In0sImRhdGEiOnsiQzE1MDE1NTU3NjA1MDIiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMxNTAxNTU1NzYwNTAzIjp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMTUwMTU1NTc2MDUwNCI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzE1MDE1NTU3NjA1MDUiOnsic2lkIjoiMCIsImdvb2RzX2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIk0xNTAxNTU1Nzc1MTgzIjp7InNpZCI6IjAiLCJnb29kc19pZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJNMTUwMTU1NTc3NzEyNiI6eyJzaWQiOiIwIiwiZ29vZHNfaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX0sImlkIjoid2FpbWFpX2dvb2RzIn0sIk0xNTAxNTU1OTExNTk1Ijp7InN0eWxlIjp7ImhlaWdodCI6IjEwIiwiYmFja2dyb3VuZCI6IiNjM2JjYmEifSwiaWQiOiJibGFuayJ9LCJNMTUwMTU1NTc4NDM5OCI6eyJwYXJhbXMiOnsiY29udGVudCI6IlBHUnBkaUJqYkdGemN6MGlZV04wYVhacGRIa3RjblZzWlMxMGFYUnNaU0JpYjNKa1pYSXRNWEI0TFdJaUlITjBlV3hsUFNKM2FHbDBaUzF6Y0dGalpUb2dibTl5YldGc095QmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUJpYjNKa1pYSXRZbTkwZEc5dE9pQXhjSGdnYzI5c2FXUWdjbWRpS0RJeU5Dd2dNakkwTENBeU1qUXBPeUJvWldsbmFIUTZJREV1T0hKbGJUc2diR2x1WlMxb1pXbG5hSFE2SURFdU9ISmxiVHNnWm05dWRDMXphWHBsT2lBd0xqaHlaVzA3SUdOdmJHOXlPaUJ5WjJJb05URXNJRFV4TENBMU1TazdJSFJsZUhRdFlXeHBaMjQ2SUdObGJuUmxjanNnWm05dWRDMTNaV2xuYUhRNklHSnZiR1E3SUdadmJuUXRabUZ0YVd4NU9pQW1jWFZ2ZER0TmFXTnliM052Wm5RZ1dXRm9aV2ttY1hWdmREc3NJT1crcnVpOXIrbWJoZW03a1N3ZzVhNkw1TDJUTENCVVlXaHZiV0VzSUVGeWFXRnNMQ0JJWld4MlpYUnBZMkVzSUZOVVNHVnBkR2s3SWo3bXRMdmxpcWpvcDRUbGlKazhMMlJwZGo0OFpHbDJJR05zWVhOelBTSmhZM1JwZG1sMGVTMXlkV3hsTFdOdmJuUmxiblFpSUhOMGVXeGxQU0ozYUdsMFpTMXpjR0ZqWlRvZ2JtOXliV0ZzT3lCaWIzZ3RjMmw2YVc1bk9pQmliM0prWlhJdFltOTRPeUF0ZDJWaWEybDBMWFJoY0Mxb2FXZG9iR2xuYUhRdFkyOXNiM0k2SUhSeVlXNXpjR0Z5Wlc1ME95QndZV1JrYVc1bk9pQXdMamh5WlcwN0lHeHBibVV0YUdWcFoyaDBPaUF4Y21WdE95Qm1iMjUwTFhOcGVtVTZJREF1TjNKbGJUc2dZMjlzYjNJNklISm5ZaWcxTVN3Z05URXNJRFV4S1RzZ1ptOXVkQzFtWVcxcGJIazZJQ1p4ZFc5ME8wMXBZM0p2YzI5bWRDQlpZV2hsYVNaeGRXOTBPeXdnNWI2dTZMMnY2WnVGNmJ1UkxDRGxyb3ZrdlpNc0lGUmhhRzl0WVN3Z1FYSnBZV3dzSUVobGJIWmxkR2xqWVN3Z1UxUklaV2wwYVRzaVBqRXU1clM3NVlxbzVwZTI2WmUwNzd5YU4rYWNpREV5NXBlbDc3eU5OK2FjaURFMjVwZWxKbTVpYzNBN1BHSnlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUl2UGpJdTVyUzc1WXFvNkl5RDVadTA3N3lhNW9tQTZLYUc1NXVXNVorTzViaUNKbTVpYzNBN1BHSnlJSE4wZVd4bFBTSmliM2d0YzJsNmFXNW5PaUJpYjNKa1pYSXRZbTk0T3lBdGQyVmlhMmwwTFhSaGNDMW9hV2RvYkdsbmFIUXRZMjlzYjNJNklIUnlZVzV6Y0dGeVpXNTBPeUl2UGpNdTVyUzc1WXFvNVlhRjVhNjU3N3lhNktXXC81NU9jTUM0NU9lV0ZnK2kxdCsrOGpPYVhvT2EwdStXS3FPV1RnZVMrbStlN21lV2NzT1dNdXVTN3BlV3VudW1aaGVTN3QrYWd2T1M0dXVXSGhpWnVZbk53T3p4aWNpQnpkSGxzWlQwaVltOTRMWE5wZW1sdVp6b2dZbTl5WkdWeUxXSnZlRHNnTFhkbFltdHBkQzEwWVhBdGFHbG5hR3hwWjJoMExXTnZiRzl5T2lCMGNtRnVjM0JoY21WdWREc2lMejQwTHVTOG1PYURvT1M3aGVtWmtPZStqdVdib3VXa2x1V05sdVdQaXVlK2p1V2Jva0Z3Y09TNGkrV05sZVM0bE9tQWllYUxxZVdjcU9lNnYrYVVyK1M3bU9lYWhPaXVvdVdObGVTNnErV1Bsend2WkdsMlBnPT0ifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNjM2JjYmEiLCJwYWRkaW5nbGVmdCI6IjE1IiwicGFkZGluZ3RvcCI6IjE1In0sImlkIjoicmljaHRleHQifX19',	'../addons/we7_wmall/plugin/diypage/static/template/default9/preview.jpg',	'10009');

DROP TABLE IF EXISTS `ims_tiny_wmall_errander_category`;
CREATE TABLE `ims_tiny_wmall_errander_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'buy',
  `label` varchar(1000) NOT NULL,
  `start_fee` varchar(10) NOT NULL,
  `start_km` varchar(10) NOT NULL,
  `pre_km_fee` varchar(10) NOT NULL DEFAULT '0',
  `tip_min` varchar(10) NOT NULL DEFAULT '0',
  `tip_max` varchar(10) NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rule` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `goods_thumbs_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_on_upload` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_within_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_times` text NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `pre_km` varchar(10) NOT NULL DEFAULT '1',
  `weight_fee_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `weight_fee` text NOT NULL,
  `multiaddress` varchar(500) NOT NULL,
  `group_discount` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_errander_order`;
CREATE TABLE `ims_tiny_wmall_errander_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `order_type` varchar(20) NOT NULL DEFAULT 'buy',
  `order_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(60) NOT NULL,
  `goods_price` varchar(30) NOT NULL,
  `goods_weight` varchar(10) NOT NULL,
  `buy_username` varchar(20) NOT NULL,
  `buy_sex` varchar(5) NOT NULL,
  `buy_mobile` varchar(15) NOT NULL,
  `buy_address` varchar(100) NOT NULL,
  `buy_location_x` varchar(20) NOT NULL,
  `buy_location_y` varchar(20) NOT NULL,
  `accept_username` varchar(20) NOT NULL,
  `accept_sex` varchar(5) NOT NULL,
  `accept_mobile` varchar(15) NOT NULL,
  `accept_address` varchar(100) NOT NULL,
  `accept_location_x` varchar(20) NOT NULL,
  `accept_location_y` varchar(20) NOT NULL,
  `distance` varchar(20) NOT NULL,
  `delivery_time` varchar(30) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_assign_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_instore_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_success_location_x` varchar(15) NOT NULL,
  `delivery_success_location_y` varchar(15) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_fee` varchar(10) NOT NULL,
  `delivery_tips` varchar(10) NOT NULL,
  `total_fee` varchar(10) NOT NULL,
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `deliveryer_fee` varchar(10) NOT NULL,
  `deliveryer_total_fee` varchar(10) NOT NULL DEFAULT '0',
  `note` varchar(200) NOT NULL,
  `is_remind` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `anonymous_username` varchar(15) NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `refund_out_no` varchar(40) NOT NULL,
  `refund_apply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_channel` varchar(30) NOT NULL,
  `refund_account` varchar(30) NOT NULL,
  `delivery_handle_type` varchar(15) NOT NULL DEFAULT 'wechat',
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  `stat_year` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `thumbs` varchar(1000) NOT NULL,
  `transaction_id` varchar(60) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `agent_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `agent_serve` varchar(1000) NOT NULL,
  `agent_final_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `plateform_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `plateform_serve` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `is_pay` (`is_pay`),
  KEY `pay_type` (`pay_type`),
  KEY `refund_status` (`refund_status`),
  KEY `delivery_status` (`delivery_status`),
  KEY `status` (`status`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_errander_order_status_log`;
CREATE TABLE `ims_tiny_wmall_errander_order_status_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `oid` (`oid`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_fans`;
CREATE TABLE `ims_tiny_wmall_fans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `sex` varchar(3) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_freelunch`;
CREATE TABLE `ims_tiny_wmall_freelunch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `max_partake_times` int(10) unsigned NOT NULL DEFAULT '0',
  `partake_grant_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reward_grant_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pre_partaker_num` int(10) unsigned NOT NULL DEFAULT '0',
  `pre_partaker_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pre_reward_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `plus_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `plus_thumb` varchar(255) NOT NULL,
  `plus_partaker_num` int(10) unsigned NOT NULL DEFAULT '0',
  `plus_reward_num` int(10) unsigned NOT NULL DEFAULT '1',
  `plus_pre_partaker_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pre_plus_reward_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `plus_serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `share` varchar(3000) NOT NULL,
  `agreement` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `pre_max_partake_times` int(10) unsigned NOT NULL DEFAULT '0',
  `plus_pre_max_partake_times` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_freelunch_partaker`;
CREATE TABLE `ims_tiny_wmall_freelunch_partaker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `freelunch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `number` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `order_sn` varchar(50) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `freelunch_id` (`freelunch_id`),
  KEY `record_id` (`record_id`),
  KEY `uid` (`uid`),
  KEY `serial_sn` (`serial_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_freelunch_record`;
CREATE TABLE `ims_tiny_wmall_freelunch_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `freelunch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL DEFAULT 'common',
  `partaker_total` int(10) unsigned NOT NULL DEFAULT '0',
  `partaker_dosage` int(10) unsigned NOT NULL DEFAULT '0',
  `partaker_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `reward_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `reward_uid` varchar(1000) NOT NULL,
  `reward_number` int(10) unsigned NOT NULL DEFAULT '0',
  `startime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `freelunch_id` (`freelunch_id`),
  KEY `serial_sn` (`serial_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_goods`;
CREATE TABLE `ims_tiny_wmall_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `price` varchar(500) NOT NULL,
  `discount_price` varchar(500) NOT NULL,
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `min_buy_limit` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '最少购买数量',
  `is_options` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `unitname` varchar(10) NOT NULL DEFAULT '份',
  `total` int(10) NOT NULL DEFAULT '0',
  `sailed` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `slides` varchar(1000) NOT NULL,
  `label` varchar(5) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `comment_total` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_good` int(10) unsigned NOT NULL DEFAULT '0',
  `print_label` int(10) unsigned NOT NULL DEFAULT '0',
  `total_update_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `content` varchar(100) NOT NULL,
  `number` varchar(50) NOT NULL,
  `attrs` varchar(1000) NOT NULL,
  `old_price` varchar(10) NOT NULL,
  `elemeId` varchar(50) NOT NULL DEFAULT '0',
  `meituanId` varchar(50) NOT NULL DEFAULT '0',
  `openplateformCode` varchar(50) NOT NULL DEFAULT '0',
  `total_warning` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `cid` (`cid`),
  KEY `title` (`title`),
  KEY `is_hot` (`is_hot`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`),
  KEY `elemeId` (`elemeId`),
  KEY `meituanId` (`meituanId`),
  KEY `openplateformCode` (`openplateformCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_goods_category`;
CREATE TABLE `ims_tiny_wmall_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_fee` int(10) unsigned NOT NULL DEFAULT '0',
  `elemeId` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`),
  KEY `elemeId` (`elemeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_goods_options`;
CREATE TABLE `ims_tiny_wmall_goods_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `price` varchar(50) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '-1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `total_warning` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_help`;
CREATE TABLE `ims_tiny_wmall_help` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_members`;
CREATE TABLE `ims_tiny_wmall_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `setmeal_id` int(10) unsigned NOT NULL DEFAULT '0',
  `setmeal_day_free_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `setmeal_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `setmeal_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `first_order_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_order_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_num` int(10) unsigned DEFAULT '0',
  `success_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cancel_num` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_sys` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:系统会员, 2:模块兼容会员',
  `search_data` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile_audit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `salt` varchar(10) NOT NULL,
  `password` varchar(32) NOT NULL,
  `openid_qq` varchar(50) NOT NULL,
  `openid_wx` varchar(50) NOT NULL,
  `register_type` varchar(20) NOT NULL DEFAULT 'wechat',
  `success_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `credit1` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `credit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `token` varchar(50) NOT NULL,
  `uid_qianfan` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_majia` int(10) unsigned NOT NULL DEFAULT '0',
  `is_spread` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spreadcredit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `spreadtime` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `spread1` int(10) unsigned NOT NULL DEFAULT '0',
  `spread2` int(10) unsigned NOT NULL DEFAULT '0',
  `spread_groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `spread_status` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid_updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadfixed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `cancel_first_time` (`cancel_first_time`),
  KEY `cancel_last_time` (`cancel_last_time`),
  KEY `success_first_time` (`success_first_time`),
  KEY `success_last_time` (`success_last_time`),
  KEY `first_order_time` (`success_first_time`),
  KEY `last_order_time` (`success_last_time`),
  KEY `uid_qianfan` (`uid_qianfan`),
  KEY `is_spread` (`is_spread`),
  KEY `spreadtime` (`spreadtime`),
  KEY `openid` (`openid`),
  KEY `uid_majia` (`uid_majia`),
  KEY `spread1` (`spread1`),
  KEY `spread2` (`spread2`),
  KEY `spead_groupid` (`spread_groupid`),
  KEY `spead_status` (`spread_status`),
  KEY `speadid1` (`spread1`),
  KEY `speadid2` (`spread2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_member_footmark`;
CREATE TABLE `ims_tiny_wmall_member_footmark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `sid` (`sid`),
  KEY `stat_day` (`stat_day`),
  KEY `agentid` (`agentid`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_member_groups`;
CREATE TABLE `ims_tiny_wmall_member_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL DEFAULT '',
  `group_condition` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_member_recharge`;
CREATE TABLE `ims_tiny_wmall_member_recharge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `order_sn` varchar(40) NOT NULL,
  `fee` varchar(10) NOT NULL,
  `final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type` varchar(15) NOT NULL DEFAULT 'credit',
  `tag` varchar(1000) NOT NULL,
  `is_pay` tinyint(1) NOT NULL DEFAULT '0',
  `pay_type` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_news`;
CREATE TABLE `ims_tiny_wmall_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `cateid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(50) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_display` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_show_home` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `cateid` (`cateid`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_news_category`;
CREATE TABLE `ims_tiny_wmall_news_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_notice`;
CREATE TABLE `ims_tiny_wmall_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'member',
  `title` varchar(60) NOT NULL,
  `link` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_notice_read_log`;
CREATE TABLE `ims_tiny_wmall_notice_read_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `is_new` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `notice_id` (`notice_id`),
  KEY `is_new` (`is_new`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_oauth_fans`;
CREATE TABLE `ims_tiny_wmall_oauth_fans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(50) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `oauth_openid` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `appid` (`appid`),
  KEY `openid` (`openid`),
  KEY `oauth_openid` (`oauth_openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order`;
CREATE TABLE `ims_tiny_wmall_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordersn` varchar(20) NOT NULL,
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `location_x` varchar(20) NOT NULL,
  `location_y` varchar(20) NOT NULL,
  `note` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `num` tinyint(3) unsigned NOT NULL,
  `delivery_day` varchar(20) NOT NULL,
  `delivery_time` varchar(20) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_assign_time` int(10) NOT NULL DEFAULT '0',
  `delivery_success_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '3:待配送, 4:配送中, 5: 配送成功, 6: 配送失败',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_comment` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '外卖配送费',
  `pack_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `serve_fee` varchar(10) NOT NULL,
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `total_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `vip_free_delivery_fee` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `invoice` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `is_remind` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_refund` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `person_num` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `table_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `reserve_type` varchar(10) NOT NULL,
  `reserve_time` varchar(30) NOT NULL,
  `transaction_id` varchar(60) NOT NULL COMMENT '第三方支付交易号',
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `delivery_handle_type` varchar(20) NOT NULL DEFAULT 'wechat',
  `delivery_success_location_x` varchar(15) NOT NULL,
  `delivery_success_location_y` varchar(15) NOT NULL,
  `delivery_instore_time` int(10) NOT NULL DEFAULT '0',
  `deliveryingtime` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryinstoretime` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryedtime` int(10) unsigned NOT NULL,
  `deliverysuccesstime` int(10) unsigned NOT NULL DEFAULT '0',
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '1',
  `handletime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `store_final_fee` varchar(10) NOT NULL DEFAULT '0',
  `store_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_serve_rate` varchar(10) NOT NULL DEFAULT '0',
  `plateform_serve_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_delivery_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_deliveryer_fee` varchar(10) NOT NULL DEFAULT '0',
  `refund_fee` varchar(10) NOT NULL DEFAULT '0',
  `out_trade_no` varchar(50) NOT NULL,
  `stat_year` smallint(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  `mall_first_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `plateform_serve` varchar(500) NOT NULL,
  `deductcredit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `is_timeout` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `agent_serve` varchar(500) NOT NULL,
  `agent_final_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_serve_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `spread1` int(10) unsigned NOT NULL DEFAULT '0',
  `spread2` int(10) unsigned NOT NULL DEFAULT '0',
  `clerk_notify_collect_time` int(10) NOT NULL DEFAULT '0',
  `distance` varchar(20) NOT NULL DEFAULT '0.00',
  `last_notify_deliveryer_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_notify_clerk_time` int(10) unsigned NOT NULL DEFAULT '0',
  `notify_deliveryer_total` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_clerk_total` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `elemeOrderId` varchar(60) NOT NULL,
  `elemeDowngraded` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eleme_store_final_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `order_plateform` varchar(20) NOT NULL DEFAULT 'we7_wmall',
  `spreadbalance` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `meituanOrderId` varchar(50) NOT NULL DEFAULT '0',
  `meituan_store_final_fee` varchar(10) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid_sid` (`uniacid`,`sid`),
  KEY `delivery_status` (`delivery_status`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `delivery_type` (`delivery_type`),
  KEY `uid` (`uid`),
  KEY `order_type` (`order_type`),
  KEY `status` (`status`),
  KEY `refund_status` (`refund_status`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `endtime` (`endtime`),
  KEY `pay_type` (`pay_type`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`),
  KEY `is_pay` (`is_pay`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `agentid` (`agentid`),
  KEY `clerk_notify_collect_time` (`clerk_notify_collect_time`),
  KEY `handletime` (`handletime`),
  KEY `elemeOrderId` (`elemeOrderId`),
  KEY `order_plateform` (`order_plateform`),
  KEY `elemeDowngraded` (`elemeDowngraded`),
  KEY `spread1` (`spread1`),
  KEY `spread2` (`spread2`),
  KEY `spreadbalance` (`spreadbalance`),
  KEY `meituanOrderId` (`meituanOrderId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_cart`;
CREATE TABLE `ims_tiny_wmall_order_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `num` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `data` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `box_price` varchar(10) NOT NULL DEFAULT '0',
  `original_price` varchar(10) NOT NULL DEFAULT '0.00',
  `original_data` text NOT NULL,
  `bargain_use_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_comment`;
CREATE TABLE `ims_tiny_wmall_order_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `goods_quality` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_service` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `note` varchar(255) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `thumbs` varchar(3000) NOT NULL,
  `reply` varchar(500) NOT NULL,
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0',
  `is_share` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`),
  KEY `addtime` (`addtime`),
  KEY `agentid` (`agentid`),
  KEY `delivery_service` (`delivery_service`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_current_log`;
CREATE TABLE `ims_tiny_wmall_order_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `orderid` int(10) unsigned NOT NULL DEFAULT '0',
  `fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_type` varchar(15) NOT NULL,
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态',
  `trade_status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '交易记录1:成功,2:进行中,3:失败,4:交易关闭.',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `out_trade_no` varchar(40) NOT NULL COMMENT '商户支付订单号',
  `out_refund_no` varchar(40) NOT NULL COMMENT '商户退款订单号',
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1:申请中, 2:退款中, 3:退款成功, 4:退款失败',
  `refund_time` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_channel` varchar(20) NOT NULL,
  `refund_account` varchar(50) NOT NULL,
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_fee` varchar(10) NOT NULL,
  `store_deliveryer_fee` varchar(10) NOT NULL,
  `vip_free_delivery_fee` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_discount`;
CREATE TABLE `ims_tiny_wmall_order_discount` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `note` varchar(50) NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '0.00',
  `store_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `plateform_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  `agent_discount_fee` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_grant`;
CREATE TABLE `ims_tiny_wmall_order_grant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `max` int(10) unsigned NOT NULL DEFAULT '0',
  `continuous` int(10) unsigned NOT NULL DEFAULT '0',
  `sum` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `continuous` (`continuous`),
  KEY `sum` (`sum`),
  KEY `updatetime` (`updatetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_grant_record`;
CREATE TABLE `ims_tiny_wmall_order_grant_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `grant` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `credittype` varchar(20) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `mark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `times` (`days`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_peerpay`;
CREATE TABLE `ims_tiny_wmall_order_peerpay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `plid` int(10) unsigned NOT NULL DEFAULT '0',
  `orderid` int(11) NOT NULL DEFAULT '0',
  `peerpay_type` tinyint(1) NOT NULL DEFAULT '0',
  `peerpay_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_maxprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_realprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_selfpay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `peerpay_message` varchar(500) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `orderid` (`orderid`),
  KEY `plid` (`plid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_peerpay_payinfo`;
CREATE TABLE `ims_tiny_wmall_order_peerpay_payinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `uname` varchar(255) NOT NULL DEFAULT '',
  `usay` varchar(500) NOT NULL DEFAULT '',
  `final_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `headimg` varchar(255) DEFAULT NULL,
  `refundstatus` tinyint(1) NOT NULL DEFAULT '0',
  `refundprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `openid` (`openid`),
  KEY `order_sn` (`order_sn`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_refund`;
CREATE TABLE `ims_tiny_wmall_order_refund` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `fee` varchar(10) NOT NULL DEFAULT '0',
  `out_trade_no` varchar(60) NOT NULL DEFAULT '0',
  `out_refund_no` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `apply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `handle_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_time` int(10) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(30) NOT NULL,
  `account` varchar(30) NOT NULL,
  `order_channel` varchar(20) NOT NULL DEFAULT 'wap',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_refund_log`;
CREATE TABLE `ims_tiny_wmall_order_refund_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(20) NOT NULL DEFAULT 'order',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_remind_log`;
CREATE TABLE `ims_tiny_wmall_order_remind_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `remindid` varchar(50) NOT NULL DEFAULT '0',
  `channel` varchar(15) NOT NULL DEFAULT 'system',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reply` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `addtime` (`addtime`),
  KEY `uniacid` (`uniacid`),
  KEY `oid` (`oid`),
  KEY `remindid` (`remindid`),
  KEY `channel` (`channel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_stat`;
CREATE TABLE `ims_tiny_wmall_order_stat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_num` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_title` varchar(30) NOT NULL,
  `goods_unit_price` varchar(10) NOT NULL,
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `print_label` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0',
  `goods_category_title` varchar(20) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `option_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_discount_num` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_original_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `bargain_id` int(10) unsigned NOT NULL DEFAULT '0',
  `total_update_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stat_year` smallint(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_week` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_number` varchar(30) NOT NULL,
  `order_plateform` varchar(20) NOT NULL DEFAULT 'we7_wmall',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `addtime` (`addtime`),
  KEY `bargain_id` (`bargain_id`),
  KEY `uid` (`uid`),
  KEY `agentid` (`agentid`),
  KEY `oid` (`oid`),
  KEY `order_plateform` (`order_plateform`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_order_status_log`;
CREATE TABLE `ims_tiny_wmall_order_status_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `role` varchar(30) NOT NULL,
  `role_cn` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `oid` (`oid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_paybill_order`;
CREATE TABLE `ims_tiny_wmall_paybill_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `serial_sn` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(50) NOT NULL,
  `pay_type` varchar(15) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `total_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `no_discount_part` varchar(10) NOT NULL DEFAULT '0.00',
  `discount_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `final_fee` varchar(20) NOT NULL DEFAULT '0.00',
  `plateform_serve_fee` varchar(10) NOT NULL DEFAULT '0.00',
  `plateform_serve` varchar(1000) NOT NULL,
  `store_final_fee` varchar(10) NOT NULL DEFAULT '0',
  `out_trade_no` varchar(50) NOT NULL,
  `transaction_id` varchar(60) NOT NULL,
  `stat_year` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_month` int(10) unsigned NOT NULL DEFAULT '0',
  `stat_day` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `stat_year` (`stat_year`),
  KEY `stat_month` (`stat_month`),
  KEY `stat_day` (`stat_day`),
  KEY `addtime` (`addtime`),
  KEY `paytime` (`paytime`),
  KEY `is_pay` (`is_pay`),
  KEY `pay_type` (`pay_type`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_paylog`;
CREATE TABLE `ims_tiny_wmall_paylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(30) NOT NULL,
  `fee` varchar(10) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `out_trade_order_id` varchar(50) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `order_sn` (`order_sn`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_perm_account`;
CREATE TABLE `ims_tiny_wmall_perm_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `plugins` text,
  `max_store` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_perm_role`;
CREATE TABLE `ims_tiny_wmall_perm_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `rolename` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `perms` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_perm_user`;
CREATE TABLE `ims_tiny_wmall_perm_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `roleid` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `perms` text NOT NULL,
  `realname` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_plugin`;
CREATE TABLE `ims_tiny_wmall_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL,
  `thumb` varchar(255) DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  `ability` varchar(255) NOT NULL,
  `status` int(10) DEFAULT '0',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_show` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `ims_tiny_wmall_plugin` (`id`, `name`, `type`, `title`, `thumb`, `version`, `ability`, `status`, `displayorder`, `is_show`) VALUES
(1,	'errander',	'biz',	'啦啦跑腿',	'../addons/we7_wmall/static/img/plugin/errander.jpg',	'',	'随意购，帮人取，帮人送，跑腿功能一网打尽',	1,	0,	1),
(2,	'deliveryCard',	'biz',	'配送会员卡',	'../addons/we7_wmall/static/img/plugin/deliveryCard.jpg',	'',	'配送会员卡',	1,	0,	1),
(3,	'bargain',	'activity',	'天天特价',	'../addons/we7_wmall/static/img/plugin/bargain.jpg',	'',	'天天特价',	1,	0,	1),
(4,	'shareRedpacket',	'activity',	'分享有礼',	'../addons/we7_wmall/static/img/plugin/shareRedpacket.jpg',	'',	'分享有礼',	1,	0,	1),
(5,	'freeLunch',	'activity',	'霸王餐',	'../addons/we7_wmall/static/img/plugin/freeLunch.jpg',	'',	'霸王餐',	1,	0,	1),
(6,	'ordergrant',	'biz',	'下单有礼',	'../addons/we7_wmall/static/img/plugin/ordergrant.jpg',	'',	'下单给顾客送积分或余额',	1,	0,	1),
(7,	'creditshop',	'biz',	'积分商城',	'../addons/we7_wmall/static/img/plugin/creditshop.jpg',	'',	'积分兑换好礼活动利器',	1,	0,	1),
(9,	'diypage',	'tool',	'平台装修',	'../addons/we7_wmall/static/img/plugin/diypage.jpg',	'',	'可自定义底部菜单，设置订单弹幕等',	1,	0,	1),
(10,	'deliveryerApp',	'biz',	'配送员APP',	'../addons/we7_wmall/static/img/plugin/deliveryerApp.jpg',	'',	'可在手机上接单 顾客可实时查看配送员的位置',	1,	0,	0),
(11,	'superRedpacket',	'biz',	'超级红包',	'../addons/we7_wmall/static/img/plugin/superRedpacket.jpg',	'',	'超级红包可实现主动发送和分享红包',	1,	0,	1),
(12,	'spread',	'biz',	'分销推广',	'../addons/we7_wmall/static/img/plugin/spread.jpg',	'',	'分销推广',	1,	0,	1);

DROP TABLE IF EXISTS `ims_tiny_wmall_printer`;
CREATE TABLE `ims_tiny_wmall_printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'feie',
  `print_no` varchar(30) NOT NULL,
  `member_code` varchar(50) NOT NULL COMMENT '商户编号',
  `key` varchar(50) NOT NULL,
  `api_key` varchar(100) NOT NULL COMMENT '易联云打印机api_key',
  `print_nums` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `print_label` varchar(50) NOT NULL,
  `is_print_all` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `qrcode_link` varchar(100) NOT NULL,
  `print_header` varchar(50) NOT NULL,
  `print_footer` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `qrcode_type` varchar(20) NOT NULL DEFAULT 'custom',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_printer_label`;
CREATE TABLE `ims_tiny_wmall_printer_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_reply`;
CREATE TABLE `ims_tiny_wmall_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(50) DEFAULT NULL,
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_report`;
CREATE TABLE `ims_tiny_wmall_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `thumbs` varchar(2000) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_reserve`;
CREATE TABLE `ims_tiny_wmall_reserve` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `time` varchar(15) NOT NULL,
  `table_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_shareredpacket`;
CREATE TABLE `ims_tiny_wmall_shareredpacket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `share_redpacket_condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_min` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_max` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `follow_redpacket_min` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_max` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `share` varchar(3000) NOT NULL,
  `agreement` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_shareredpacket_invite_record`;
CREATE TABLE `ims_tiny_wmall_shareredpacket_invite_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `share_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `follow_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `share_redpacket_condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `share_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `follow_redpacket_condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `follow_redpacket_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `activity_id` (`activity_id`),
  KEY `share_uid` (`share_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_slide`;
CREATE TABLE `ims_tiny_wmall_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_spread_current_log`;
CREATE TABLE `ims_tiny_wmall_spread_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `spreadid` (`spreadid`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_spread_getcash_log`;
CREATE TABLE `ims_tiny_wmall_spread_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `spreadid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `channel` varchar(20) NOT NULL DEFAULT 'wechat',
  `account` varchar(500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`),
  KEY `agentid` (`agentid`),
  KEY `spreadid` (`spreadid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_spread_groups`;
CREATE TABLE `ims_tiny_wmall_spread_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL DEFAULT '',
  `commission1` varchar(20) NOT NULL DEFAULT '0',
  `commission2` varchar(20) NOT NULL DEFAULT '0',
  `group_condition` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store`;
CREATE TABLE `ims_tiny_wmall_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` varchar(50) NOT NULL,
  `title` varchar(30) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `business_hours` varchar(200) NOT NULL,
  `is_in_business` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `send_price` smallint(5) unsigned NOT NULL DEFAULT '0',
  `delivery_price` varchar(255) NOT NULL DEFAULT '0',
  `delivery_free_price` int(10) unsigned NOT NULL DEFAULT '0',
  `pack_price` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `delivery_time` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:商家配送,2:到店自提,3:两种都支持',
  `delivery_within_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_reserve_days` tinyint(3) unsigned DEFAULT '0',
  `serve_radius` varchar(30) NOT NULL DEFAULT '0.00',
  `serve_fee` varchar(255) NOT NULL,
  `delivery_area` varchar(50) NOT NULL,
  `thumbs` varchar(1000) NOT NULL,
  `address` varchar(50) NOT NULL,
  `location_x` varchar(15) NOT NULL,
  `location_y` varchar(15) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sns` varchar(255) NOT NULL,
  `notice` varchar(100) NOT NULL COMMENT '公告',
  `tips` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `payment` varchar(255) NOT NULL,
  `invoice_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `token_status` tinyint(3) unsigned DEFAULT '0',
  `remind_time_limit` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `remind_reply` varchar(1500) NOT NULL,
  `comment_reply` varchar(2000) NOT NULL,
  `sailed` int(10) unsigned NOT NULL DEFAULT '0',
  `score` varchar(10) NOT NULL,
  `first_order_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `discount_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `grant_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bargain_price_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reserve_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `collect_coupon_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `grant_coupon_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `comment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论审核.1:直接通过',
  `sms_use_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '短信使用条数',
  `wechat_qrcode` varchar(500) NOT NULL,
  `custom_url` varchar(1000) NOT NULL,
  `addtype` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:后台添加,2:申请入驻',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `template` varchar(20) NOT NULL DEFAULT 'index',
  `pc_notice_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `not_in_serve_radius` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `auto_handel_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `auto_get_address` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `auto_notice_deliveryer` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_assign` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_reserve` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_meal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forward_mode` varchar(15) NOT NULL,
  `assign_mode` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `assign_qrcode` varchar(255) NOT NULL,
  `delivery_mode` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `order_note` varchar(255) NOT NULL COMMENT '订单备注',
  `forward_url` varchar(100) NOT NULL,
  `delivery_fee_mode` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `delivery_times` varchar(10000) NOT NULL,
  `qualification` varchar(1000) NOT NULL,
  `label` int(10) NOT NULL DEFAULT '0',
  `delivery_areas` text NOT NULL,
  `push_token` varchar(50) NOT NULL,
  `is_rest` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_stick` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `is_paybill` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `self_audit_comment` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_extra` varchar(255) NOT NULL,
  `elemeShopId` varchar(30) NOT NULL DEFAULT '0',
  `eleme_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `meituan_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `openplateform_extra` varchar(1000) NOT NULL,
  `meituanShopId` varchar(30) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `title` (`title`),
  KEY `is_recommend` (`is_recommend`),
  KEY `cid` (`cid`),
  KEY `status` (`status`),
  KEY `label` (`label`),
  KEY `displayorder` (`displayorder`),
  KEY `is_stick` (`is_stick`),
  KEY `agentid` (`agentid`),
  KEY `elemeShopId` (`elemeShopId`),
  KEY `meituanShopId` (`meituanShopId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_account`;
CREATE TABLE `ims_tiny_wmall_store_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_rate` varchar(10) NOT NULL DEFAULT '0',
  `fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  `wechat` varchar(1000) NOT NULL,
  `fee_takeout` varchar(500) NOT NULL,
  `fee_instore` varchar(500) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_paybill` varchar(500) NOT NULL,
  `fee_eleme` varchar(500) NOT NULL,
  `fee_meituan` varchar(500) NOT NULL,
  `fee_selfDelivery` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_activity`;
CREATE TABLE `ims_tiny_wmall_store_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_category`;
CREATE TABLE `ims_tiny_wmall_store_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `slide_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `slide` varchar(1500) NOT NULL,
  `nav_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `nav` varchar(1500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_clerk`;
CREATE TABLE `ims_tiny_wmall_store_clerk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `clerk_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime|154|errander_deliveryerApp|9.0.0|20171010094422` int(10) unsigned NOT NULL DEFAULT '0',
  `extra` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `clerk_id` (`clerk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_current_log`;
CREATE TABLE `ims_tiny_wmall_store_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:订单入账, 2: 申请提现',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_deliveryer`;
CREATE TABLE `ims_tiny_wmall_store_deliveryer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  `work_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `extra` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`),
  KEY `work_status` (`work_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_delivery_times`;
CREATE TABLE `ims_tiny_wmall_store_delivery_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `start` varchar(20) NOT NULL,
  `end` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_favorite`;
CREATE TABLE `ims_tiny_wmall_store_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid_sid` (`uid`,`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_getcash_log`;
CREATE TABLE `ims_tiny_wmall_store_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `account` varchar(500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1:申请成功,2:申请中',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_members`;
CREATE TABLE `ims_tiny_wmall_store_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `first_order_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_order_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_num` int(10) unsigned DEFAULT '0',
  `success_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cancel_num` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_sys` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:系统会员, 2:模块兼容会员',
  `success_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `success_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_first_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cancel_last_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `uniacid` (`uniacid`),
  KEY `first_order_time` (`success_first_time`),
  KEY `last_order_time` (`success_last_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_store_settle_config`;
CREATE TABLE `ims_tiny_wmall_store_settle_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `audit_status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `mobile_verify_status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `get_cash_fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_rate` varchar(10) NOT NULL,
  `get_cash_fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  `agreement` text,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_superredpacket`;
CREATE TABLE `ims_tiny_wmall_superredpacket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `grant_object` longtext NOT NULL,
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `addtime` (`addtime`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_superredpacket_grant`;
CREATE TABLE `ims_tiny_wmall_superredpacket_grant` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `packet_dosage` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `packet_total` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_superredpacket_share`;
CREATE TABLE `ims_tiny_wmall_superredpacket_share` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT '0',
  `activity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `condition` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `grant_days_effect` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_days_limit` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `times_limit` text NOT NULL,
  `category_limit` text NOT NULL,
  `nums` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_system_log`;
CREATE TABLE `ims_tiny_wmall_system_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `params` varchar(5000) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_tables`;
CREATE TABLE `ims_tiny_wmall_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前对应的订单id',
  `guest_num` tinyint(3) unsigned DEFAULT '0',
  `scan_num` int(10) unsigned NOT NULL DEFAULT '0',
  `qrcode` varchar(500) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_sid` (`uniacid`,`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_tables_category`;
CREATE TABLE `ims_tiny_wmall_tables_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `limit_price` varchar(20) NOT NULL,
  `reservation_price` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_tables_scan`;
CREATE TABLE `ims_tiny_wmall_tables_scan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `table_id` (`table_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_tiny_wmall_text`;
CREATE TABLE `ims_tiny_wmall_text` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `agentid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `agentid` (`agentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `ims_tiny_wmall_plugin` (`id`, `name`, `type`, `title`, `thumb`,
`version`, `ability`, `status`, `displayorder`, `is_show`) VALUES
(13,	'eleme',	'biz',	'饿了么订单',	'../addons/we7_wmall/static/img/plugin/ele.jpg',	'',	'饿了么订单',	1,	0,	1);


INSERT INTO `ims_tiny_wmall_plugin` (`id`, `name`, `type`, `title`, `thumb`,
`version`, `ability`, `status`, `displayorder`, `is_show`) VALUES
(14,	'meituan',	'biz',	'美团订单',	'../addons/we7_wmall/static/img/plugin/meituan.jpg',	'',	'美团订单',	1,	0,	1);


");