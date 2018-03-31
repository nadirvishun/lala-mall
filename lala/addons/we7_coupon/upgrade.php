<?php
load()->model('cache');
$we7_coupon = pdo_get('modules', array('name' => 'we7_coupon'));
if (!empty($we7_coupon)) {
	pdo_update('modules', array('issystem' => '1', 'settings' => '2'), array('mid' => $we7_coupon['mid']));
}
cache_build_account_modules();

//处理activity_store字段

if (pdo_tableexists('activity_stores')) {
	if (pdo_fieldexists('activity_stores', 'type')) {
		if (pdo_fieldexists('activity_stores', 'source')) {
			$sql = "ALTER TABLE `ims_activity_stores` DROP `source`;";
			pdo_run($sql);
		}
		$sql = "ALTER TABLE `ims_activity_stores` CHANGE `type` `source` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1为系统门店，2为微信门店';";
		pdo_run($sql);
	}
	if (!pdo_indexexists('activity_stores', 'location_id')) {
		$sql = "ALTER TABLE `ims_activity_stores` ADD INDEX `location_id` (`location_id`);";
		pdo_run($sql);
	}

	if (pdo_fieldexists('activity_stores', 'sid')) {
		$sql = "ALTER TABLE `ims_activity_stores` DROP `sid`;";
		pdo_run($sql);
		$sql = "ALTER TABLE `ims_activity_stores` DROP `offset_type`;";
		pdo_run($sql);
	}
	if (pdo_fieldexists('activity_stores', 'opentime')) {
		$sql = "ALTER TABLE `ims_activity_stores` DROP `opentime`;";
		pdo_run($sql);
	}
}

if (pdo_tableexists('coupon')) {
	//coupon表加dosage字段
	if (!pdo_fieldexists('coupon', 'dosage')) {
		$sql = "ALTER TABLE `ims_coupon` ADD `dosage` INT UNSIGNED NOT NULL DEFAULT '0';";
		pdo_run($sql);
	}

	//修改coupon表source字段
	if (pdo_fieldexists('coupon', 'source')) {
		$sql = "ALTER TABLE " . tablename('coupon') . " CHANGE `source` `source` TINYINT(3) UNSIGNED NOT NULL DEFAULT '2'";
		pdo_query($sql);
	}

//修改之前type类型的值
$sql = <<<EOF
UPDATE `ims_coupon` SET type = '1' WHERE type = 'discount';
UPDATE `ims_coupon` SET type = '2' WHERE type = 'cash';
UPDATE `ims_coupon` SET type = '3' WHERE type = 'groupon';
UPDATE `ims_coupon` SET type = '4' WHERE type = 'gift';
UPDATE `ims_coupon` SET type = '5' WHERE type = 'general_coupon';
EOF;
	pdo_run($sql);
	if (pdo_fieldexists('coupon', 'location_id_list')) {
		$sql = "ALTER TABLE `ims_coupon` DROP `location_id_list`;";
		pdo_run($sql);
	}
	if (pdo_fieldexists('coupon', 'url_name_type')) {
		$sql = "ALTER TABLE `ims_coupon` DROP `url_name_type`;";
		pdo_run($sql);
	}
	if (pdo_fieldexists('coupon', 'custom_url')) {
		$sql = "ALTER TABLE `ims_coupon` DROP `custom_url`;";
		pdo_run($sql);
	}
}

if (pdo_tableexists('coupon_record')) {
	if (!pdo_fieldexists('coupon_record', 'uid')) {
		pdo_run("ALTER TABLE `ims_coupon_record` ADD `uid` INT(10) UNSIGNED NOT NULL DEFAULT '0';");
	}
	if (!pdo_fieldexists('coupon_record', 'remark')) {
		pdo_run("ALTER TABLE `ims_coupon_record` ADD `grantmodule` VARCHAR(255) NOT NULL DEFAULT '', ADD `remark` VARCHAR(255) NOT NULL DEFAULT '';");
	}
	if (pdo_fieldexists('coupon_record', 'outer_id')) {
		pdo_run("ALTER TABLE ims_coupon_record DROP outer_id, DROP INDEX outer_id;");
	}
	if (!pdo_fieldexists('coupon_record', 'couponid')) {
		pdo_run("ALTER TABLE `ims_coupon_record` ADD `couponid` INT(10) UNSIGNED NOT NULL DEFAULT '0';");
	}
}

if (pdo_tableexists('coupon_modules')) {
	if (pdo_fieldexists('coupon_modules', 'card_id')) {
		$sql = "ALTER TABLE `ims_coupon_modules` DROP `card_id`;";
		pdo_run($sql);
	}
	//修改coupon_modules表cid改为couponid
	if (pdo_fieldexists('coupon_modules', 'cid')) {
		if (pdo_fieldexists('coupon_modules', 'couponid')) {
			$sql = "ALTER TABLE " . tablename('coupon_modules') . " DROP `couponid`";
			pdo_query($sql);
		}
		$sql = "ALTER TABLE " . tablename('coupon_modules') . " CHANGE `cid` `couponid` INT(10) UNSIGNED NOT NULL DEFAULT '0'";
		pdo_query($sql);
	}
}

if (pdo_tableexists('activity_exchange')) {
	if (!pdo_indexexists('activity_exchange', 'extra')) {
		//修改activity_exchange表extra字段加索引
		$sql = "ALTER TABLE `ims_activity_exchange` ADD INDEX(`extra`);";
		pdo_query($sql);
	}
}

//修改qrcode表url字段的长度
if (pdo_fieldexists('qrcode', 'url')) {
	$sql ="ALTER TABLE " .tablename('qrcode') . " CHANGE `url` `url` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
	pdo_query($sql);
}

//会员卡数据修复
if (pdo_tableexists('mc_card')) {
	$setting = pdo_getall('mc_card', '', '', 'id');
	if (!empty($setting)) {
		foreach ($setting as $k => &$val) {
			$color = iunserializer($val['color']);
			if (!empty($color)) {
				if (!is_array($color)) {
					$val['color'] = array(
						'title' => $color,
						'rank' => '#333',
						'name' => '#333',
						'number' => '#333',
					);
				} else {
					$val['color'] = $color;
				}
			} else {
				$val['color'] = array(
					'title' => '#333',
					'rank' => '#333',
					'name' => '#333',
					'number' => '#333',
				);
			}
			$update['color'] = iserializer($val['color']);
			$params = json_decode($val['params'], true);
			if (!empty($params)) {
				foreach ($params as $key => &$value) {
					if ($value['id'] == 'cardBasic') {
						$value['params']['color'] = $val['color'];
					}
				}
			}
			$update['params'] = json_encode($params);
			pdo_update('mc_card', $update, array('id' => $k));
		}
	}
}
//增加reply_wxcard表
$sql="
	CREATE TABLE IF NOT EXISTS `ims_wxcard_reply` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0',
	  `title` varchar(30) NOT NULL,
	  `card_id` varchar(50) NOT NULL,
	  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应卡券表里的id',
	  `brand_name` varchar(30) NOT NULL,
	  `logo_url` varchar(255) NOT NULL,
	  `success` varchar(255) NOT NULL,
	  `error` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `rid` (`rid`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_run($sql);
//增加微信卡券回复菜单
$wxcardreply_menu = pdo_get('modules_bindings', array('module' => 'we7_coupon', 'entry' => 'menu', 'title' => '微信卡券回复', 'do' => 'wxcardreply'));
if (empty($wxcardreply_menu)) {
	pdo_insert('modules_bindings', array('module' => 'we7_coupon', 'entry' => 'menu', 'title' => '微信卡券回复', 'do' => 'wxcardreply', 'icon' => 'fa fa-puzzle-piece'));
}

//修改activity_clerk_menu数据
if (pdo_tableexists('activity_clerk_menu')) {
	if (pdo_fieldexists('activity_clerk_menu', 'url')) {
		pdo_run("ALTER TABLE `ims_activity_clerk_menu` CHANGE `url` `url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
	}
}

$menu_info = pdo_getall('activity_clerk_menu');
if (empty($menu_info)) {
	$sql = "
		INSERT INTO `ims_activity_clerk_menu` (`id`, `uniacid`, `displayorder`, `pid`, `group_name`, `title`, `icon`, `url`, `type`, `permission`, `system`) VALUES
		(1, 0, 0, 0, 'mc', '快捷交易', '', '', '', 'mc_manage', 1),
		(2, 0, 0, 1, '', '积分充值', 'fa fa-money', 'credit1', 'modal', 'mc_credit1', 1),
		(3, 0, 0, 1, '', '余额充值', 'fa fa-cny', 'credit2', 'modal', 'mc_credit2', 1),
		(4, 0, 0, 1, '', '消费', 'fa fa-usd', 'consume', 'modal', 'mc_consume', 1),
		(5, 0, 0, 1, '', '发放会员卡', 'fa fa-credit-card', 'card', 'modal', 'mc_card', 1),
		(6, 0, 0, 0, 'stat', '数据统计', '', '', '', 'stat_manage', 1),
		(7, 0, 0, 6, '', '积分统计', 'fa fa-bar-chart', './index.php?c=site&a=entry&op=chart&do=statcredit1&m=we7_coupon', 'url', 'stat_credit1', 1),
		(8, 0, 0, 6, '', '余额统计', 'fa fa-bar-chart', './index.php?c=site&a=entry&op=chart&do=statcredit2&m=we7_coupon', 'url', 'stat_credit2', 1),
		(9, 0, 0, 6, '', '现金消费统计', 'fa fa-bar-chart', './index.php?c=site&a=entry&op=chart&do=statcash&m=we7_coupon', 'url', 'stat_cash', 1),
		(10, 0, 0, 6, '', '会员卡统计', 'fa fa-bar-chart', './index.php?c=site&a=entry&op=chart&do=statcard&m=we7_coupon', 'url', 'stat_card', 1),
		(11, 0, 0, 6, '', '收银台收款统计', 'fa fa-bar-chart', './index.php?c=site&a=entry&op=chart&do=statpaycenter&m=we7_coupon', 'url', 'stat_paycenter', 1),
		(12, 0, 0, 0, 'activity', '卡券核销', '', '', '', 'activity_card_manage', 1),
		(16, 0, 0, 12, '', '卡券核销', 'fa fa-money', 'cardconsume', 'modal', 'coupon_consume', 1),
		(17, 0, 0, 0, 'paycenter', '收银台', '', '', '', 'paycenter_manage', 1),
		(18, 0, 0, 17, '', '微信刷卡收款', 'fa fa-money', './index.php?c=paycenter&a=wxmicro&do=pay', 'url', 'paycenter_wxmicro_pay', 1);
		";
	pdo_run($sql);
}

//删除店员工作台菜单设置
pdo_delete('modules_bindings', array('module' => 'we7_coupon', 'entry' => 'menu', 'do' => 'clerkdeskmenu'));