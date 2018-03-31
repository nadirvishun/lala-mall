<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
/*
 * $timelimit 单位:秒
 * */
function check_cache_status($key, $timelimit = 300) {
	global $_W;
	$cache = cache_read($key);
	if(empty($cache) || ($cache['starttime'] > 0 && ($cache['starttime'] + $timelimit < TIMESTAMP))) {
		return false;
	}
	return true;
}

function set_cache($key, $value) {
	global $_W;
	if(empty($value['starttime'])) {
		$value['starttime'] = TIMESTAMP;
	}
	cache_write($key, $value);
	return true;
}

function set_system_config($key, $value) {
	global $_W;
	$sysset = get_system_config();
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$sysset[$keys[0]] = $value;
	} elseif($counts == 2) {
		if(!is_array($sysset[$keys[0]])) {
			$sysset[$keys[0]] = array();
		}
		$sysset[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		if(!is_array($sysset[$keys[0]])) {
			$sysset[$keys[0]] = array();
		} elseif(!is_array($sysset[$keys[0]][$keys[1]])) {
			$sysset[$keys[0]][$keys[1]] = array();
		}
		$sysset[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_config', array('sysset' => iserializer($sysset)), array('uniacid' => $_W['uniacid']));
	return true;
}

if(!function_exists('get_system_config')) {
	function get_system_config($key = '') {
		global $_W;
		$config = pdo_get('tiny_wmall_config', array('uniacid' => $_W['uniacid']), array('sysset', 'id'));
		if(empty($config)) {
			if(empty($_config['id'])) {
				$init_config = array(
					'uniacid' => $_W['uniacid']
				);
				pdo_insert('tiny_wmall_config', $init_config);
			}
			return array();
		}
		$sysset = iunserializer($config['sysset']);
		if(!is_array($sysset)) {
			$sysset = array();
		}
		if($_W['agentid'] > 0) {
			$sysset_agent = get_agent_system_config();
			if(!empty($sysset_agent)) {
				$sysset = multimerge($sysset, $sysset_agent);
			}
		}
		if(empty($key)) {
			return $sysset;
		}
		$keys = explode('.', $key);
		$counts = count($keys);
		if($counts == 1) {
			return $sysset[$key];
		} elseif($counts == 2) {
			return $sysset[$keys[0]][$keys[1]];
		} elseif($counts == 3) {
			return $sysset[$keys[0]][$keys[1]][$keys[2]];
		}
	}
}

function set_global_config($key, $value) {
	global $_W;
	$_W['uniacid'] = 0;
	$status = set_system_config($key, $value);
	return $status;
}

function get_global_config($key = '') {
	global $_W;
	$_W['uniacid'] = 0;
	$result = get_system_config($key);
	return $result;
}

function get_available_payment($order_type = '', $sid = 0) {
	global $_W;
	$payment = $_W['we7_wmall']['config']['payment'];
	if(empty($order_type)) {
		return is_array($payment['weixin']) ? $payment['weixin'] : array();
	}
	if(is_weixin()) {
		$payment = $payment['weixin'];
	} elseif(is_h5app()) {
		$payment = $payment['app'];
	} elseif(is_qianfan() || is_majia()) {
		$payment = $payment['weixin'];
	} else {
		$payment = $payment['wap'];
	}
	if(empty($payment)) {
		return array();
	}
	if($order_type == 'takeout') {
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('payment'));
		if(empty($store['payment'])) {
			return array();
		}
		$store['payment'] = iunserializer($store['payment']);
		foreach($payment as $key => $row) {
			if(!in_array($row, $store['payment']) && $row != 'peerpay') {
				unset($payment[$key]);
			}
		}
	} elseif($order_type == 'paybill') {
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('payment'));
		if(empty($store['payment'])) {
			return array();
		}
		$store['payment'] = iunserializer($store['payment']);
		foreach($payment as $key => $row) {
			if(!in_array($row, $store['payment']) || $row == 'delivery') {
				unset($payment[$key]);
			}
		}
	} elseif($order_type == 'recharge') {
		$index = array_search('delivery', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
		$index = array_search('credit', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
		$index = array_search('peerpay', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
	}  elseif($order_type == 'freelunch') {
		$index = array_search('delivery', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
		$index = array_search('peerpay', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
	} else {
		$index = array_search('delivery', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
		$index = array_search('peerpay', $payment);
		if($index !== false) {
			unset($payment[$index]);
		}
	}
	return $payment;
}

if(!function_exists('get_plugin_config')) {
	function get_plugin_config($key = '') {
		global $_W;
		$config = pdo_get('tiny_wmall_config', array('uniacid' => $_W['uniacid']), array('pluginset'));
		if(empty($config)) {
			return array();
		}
		$pluginset = iunserializer($config['pluginset']);
		if(!is_array($pluginset)) {
			return array();
		}
		if($_W['agentid'] > 0) {
			$plugin_agent = get_agent_plugin_config();
			if(!empty($plugin_agent)) {
				$pluginset = multimerge($pluginset, $plugin_agent);
			}
		}
		if(empty($key)) {
			return $pluginset;
		}
		$keys = explode('.', $key);
		$plugin = $keys[0];
		if(!empty($plugin)) {
			$config_plugin = $pluginset[$plugin];
			if(!is_array($config_plugin)) {
				return array();
			}
			$count = count($keys);
			if($count == 2) {
				return $config_plugin[$keys[1]];
			}
			if($count == 3) {
				return $config_plugin[$keys[1]][$keys[2]];
			}
			return $config_plugin;
		}
	}
}

function set_plugin_config($key, $value) {
	global $_W;
	$keys = explode('.', $key);
	$counts = count($keys);
	$pluginset = get_plugin_config();
	$config_plugin = $pluginset[$keys[0]];
	if($counts == 1) {
		$config_plugin = $value;
	} elseif($counts == 2) {
		$config_plugin[$keys[1]] = $value;
	} elseif($counts == 3) {
		$config_plugin[$keys[1]][$keys[2]] = $value;
	}
	$pluginset[$keys[0]] = $config_plugin;
	pdo_update('tiny_wmall_config', array('pluginset' => iserializer($pluginset)), array('uniacid' => $_W['uniacid']));
	return true;
}

function set_config_text($name, $value = '') {
	global $_W;
	$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'name' => $name));
	if(empty($config)) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $name,
			'value' => $value,
		);
		pdo_insert('tiny_wmall_text', $data);
	} else {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'value' => $value,
		);
		pdo_update('tiny_wmall_text', $data, array('uniacid' => $_W['uniacid'], 'name' => $name));
	}
	return true;
}

function get_config_text($name) {
	global $_W;
	$config = pdo_get('tiny_wmall_text', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'name' => $name));
	if($name = 'takeout_delivery_time') {
		$config['value'] = iunserializer($config['value']);
	}
	return $config['value'];
}

function get_account_perm($key = '', $uniacid = 0) {
	global $_W;
	if(empty($uniacid)) {
		$uniacid = $_W['uniacid'];
	}
	$perm = pdo_get('tiny_wmall_perm_account', array('uniacid' => $uniacid));
	if(empty($perm)) {
		return false;
	}
	if(!empty($perm)) {
		$perm['plugins'] = iunserializer($perm['plugins']);
		if(!is_array($perm['plugins'])) {
			$perm['plugins'] = array();
		}
		if(empty($perm['plugins'])) {
			$perm['plugins'] = array('none');
		}
		if(!empty($key)) {
			return $perm[$key];
		}
	}
	return $perm;
}

function check_max_store_perm() {
	global $_W;
	$max_store = intval(get_account_perm('max_store'));
	if(!$max_store) {
		return true;
	}
	$now_store = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$now_store = intval($now_store);
	if($max_store <= $now_store) {
		return false;
	}
	return true;
}

function get_mall_share() {
	global $_W;
	$config_share = $_W['we7_wmall']['config']['share'];
	$_share = array(
		'title' => $config_share['title'],
		'desc' => $config_share['desc'],
		'link' => !empty($config_share['link']) ? $config_share['link'] : imurl('wmall/home/index', array(), true),
		'imgUrl' => tomedia($config_share['imgUrl'])
	);
	return $_share;
}

function is_agent() {
	$status = 0;
	if(check_plugin_perm('agent') && get_plugin_config('agent.basic.status') == 1) {
		$status = 1;
	}
	return $status;
}

function set_agent_system_config($key, $value) {
	global $_W;
	$sysset = get_agent_system_config();
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$sysset[$keys[0]] = $value;
	} elseif($counts == 2) {
		$sysset[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		$sysset[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_agent', array('sysset' => iserializer($sysset)), array('uniacid' => $_W['uniacid'], 'id' => $_W['agentid']));
	return true;
}

function get_agent_system_config($key = '') {
	global $_W;
	$config = pdo_get('tiny_wmall_agent', array('uniacid' => $_W['uniacid'], 'id' => $_W['agentid']), array('sysset', 'id'));
	if(empty($config)) {
		return array();
	}
	$sysset = iunserializer($config['sysset']);
	if(!is_array($sysset)) {
		$sysset = array();
	}
	if(empty($key)) {
		return $sysset;
	}
	$keys = explode('.', $key);
	$counts = count($key);
	if($counts == 1) {
		return $sysset[$key];
	} elseif($counts == 2) {
		return $sysset[$keys[0]][$keys[1]];
	} elseif($counts == 3) {
		return $sysset[$keys[0]][$keys[1]][$keys[1]];
	}
}

function get_agent_plugin_config($key = '') {
	global $_W;
	$config = pdo_get('tiny_wmall_agent', array('uniacid' => $_W['uniacid'], 'id' => $_W['agentid']), array('pluginset'));
	if(empty($config)) {
		return array();
	}
	$pluginset = iunserializer($config['pluginset']);
	if(!is_array($pluginset)) {
		return array();
	}
	if(empty($key)) {
		return $pluginset;
	}
	$keys = explode('.', $key);
	$plugin = $keys[0];
	if(!empty($plugin)) {
		$config_plugin = $pluginset[$plugin];
		if(!is_array($config_plugin)) {
			return array();
		}
		if(!empty($keys[1])) {
			return $config_plugin[$keys[1]];
		}
		return $config_plugin;
	}
}

function set_agent_plugin_config($key, $value) {
	global $_W;
	$keys = explode('.', $key);
	$counts = count($keys);
	$pluginset = get_agent_plugin_config();
	$config_plugin = $pluginset[$keys[0]];
	if($counts == 1) {
		$config_plugin = $value;
	} elseif($counts == 2) {
		$config_plugin[$keys[1]] = $value;
	} elseif($counts == 3) {
		$config_plugin[$keys[1]][$keys[2]] = $value;
	}
	$pluginset[$keys[0]] = $config_plugin;
	pdo_update('tiny_wmall_agent', array('pluginset' => iserializer($pluginset)), array('uniacid' => $_W['uniacid'], 'id' => $_W['agentid']));
	return true;
}

function get_agent_perm($key = '', $agentid = 0) {
	global $_W;
	if(empty($agentid)) {
		$agentid = $_W['agentid'];
	}
	$agent = pdo_get('tiny_wmall_agent', array('uniacid' => $_W['uniacid'], 'id' => $agentid));
	if(empty($agent)) {
		return false;
	}
	$agent['plugins'] = array(
		'errander', 'diypage', 'deliveryCard', 'bargain',
	);
	if(!empty($key)) {
		return $agent[$key];
	}
	return $agent;
}

function get_user($uid = 0) {
	global $_W;
	if(empty($uid)) {
		$uid = $_W['uid'];
	}
	$user = pdo_fetch('select a.*,b.perms as perms_role from ' . tablename('tiny_wmall_perm_user') . ' as a left join ' . tablename('tiny_wmall_perm_role') . ' as b on a.roleid = b.id where a.uniacid = :uniacid and a.uid = :uid', array(':uid' => $uid, ':uniacid' => $_W['uniacid']));
	if(empty($user)) {
		return false;
	}
	$user['perms_role'] = explode(',', $user['perms_role']);
	$user['perms'] = explode(',', $user['perms']);
	$user['perms'] = array_merge($user['perms'], $user['perms_role']);
	return $user;
}

function check_perm($perm, $redirct = false) {
	global $_W;
	$redirct = array('common', 'store');
	if(in_array($_W['_controller'], $redirct)) {
		return true;
	}
	if($_W['perms'] == 'all') {
		return true;
	}
	if(empty($_W['perms'])) {
		return false;
	}
	if($perm == 'plugin.index') {
		return true;
	}
	if(in_array($perm, $_W['perms'])) {
		return true;
	}
	if($redirct) {
		$perms_init = array(
			'dashboard.index',
			'merchant.store',
			'order.takeout',
			'statcenter.takeout',
			'paycenter.paybill',
			'merchant.store',
			'service.comment',
			'deliveryer.account',
			'clerk.account',
			'member.index',
			'config.mall',
			'errander.index',
			'bargain.index',
			'deliveryCard.index',
			'qianfanApp.index',
			'majiaApp.index',
			'shareRedpacket.index',
			'freeLunch.index',
			'diypage.index',
			'ordergrant.index',
			'superRedpacket.index',
			'creditshop.index',
			'agent.index',
		);
		if(in_array($perm, $perms_init)) {
			$perm_arr = explode('.', $perm);
			foreach($_W['perms'] as $row) {
				if(strexists($row, "{$perm_arr[0]}.")) {
					$perm = explode('.', $row);
					header('location:' . iurl("{$perm['0']}/{$perm['1']}"));
					die;
				}
			}
			return false;
		}
	}
	return false;
}

function get_all_perms() {
	$all_perms = array(
		'dashboard' => array(
			'title' => '概括',
			'perms' => array(
				'dashboard.index' => '运营概况',
				'dashboard.ad' => '全屏引导页',
				'dashboard.slide' => '幻灯片',
				'dashboard.nav' => '导航图标',
				'dashboard.notice' => '公告',
				'dashboard.cube' => '图片魔方'
			)
		),
		'order' => array(
			'title' => '订单',
			'perms' => array(
				'order.takeout' => '外卖',
				'order.tangshi' => '店内',
				'order.dispatch,order.records' => '调度中心'
			)
		),
		'paycenter' => array(
			'title' => '收银台',
			'perms' => array(
				'paycenter.paybill' => '买单订单',
			)
		),
		'statcenter' => array(
			'title' => '数据',
			'perms' => array(
				'statcenter.takeout' => '外卖统计',
				'statcenter.delivery' => '配送统计',
			)
		),
		'merchant' => array(
			'title' => '商户',
			'perms' => array(
				'merchant.store' => '商户列表',
				'merchant.account' => '商户账户',
				'merchant.getcash' => '申请提现',
				'merchant.current' => '账户明细',
				'merchant.settle' => '入驻',
				'merchant.newsCategory' => '资讯分类',
				'merchant.news' => '资讯列表',
				'merchant.ad' => '广告',
				'merchant.notice' => '公告列表',
				'merchant.report' => '投诉列表'
			)
		),
		'service' => array(
			'title' => '售后',
			'perms' => array(
				'service.comment' => '用户评价'
			)
		),
		'deliveryer' => array(
			'title' => '配送员',
			'perms' => array(
				'deliveryer.account' => '入驻列表',
				'deliveryer.plateform' => '平台配送员',
				'deliveryer.getcash' => '申请提现',
				'deliveryer.current' => '账户明细',
				'deliveryer.cover' => '注册&登陆'
			)
		),
		'clerk' => array(
			'title' => '店员',
			'perms' => array(
				'clerk.account' => '店员列表',
				'clerk.cover' => '注册&登陆'
			)
		),
		'member' => array(
			'title' => '顾客',
			'perms' => array(
				'member.index' => '顾客概况',
				'member.list' => '顾客列表',
				'member.address' => '顾客地址',
				'member.coupon' => '顾客代金券',
				'member.redpacket' => '顾客红包'
			)
		),
		'config' => array(
			'title' => '设置',
			'perms' => array(
				'config.mall' => '平台',
				'config.trade' => '交易',
				'config.notice' => '消息推送',
				'config.sms' => '短信平台',
				'config.store' => '商户',
				'config.takeout' => '外卖',
				'config.deliveryer' => '配送员',
				'config.cover' => '入口',
				'config.label' => '商户标签',
				'config.report' => '商户举报类型',
				'config.help' => '常见问题',
			)
		)
	);
	if(check_plugin_perm('errander')) {
		$all_perms['errander'] = array(
			'title' => '跑腿',
			'perms' => array(
				'errander.order' => '订单',
				'errander.statcenter' => '数据',
				'errander.category' => '跑腿分类',
				'errander.config' => '跑腿设置',
				'errander.cover' => '入口设置',
			)
		);
	}
	if(check_plugin_perm('deliveryCard')) {
		$all_perms['deliveryCard'] = array(
			'title' => '配送会员卡',
			'perms' => array(
				'deliveryCard.order' => '订单',
				'deliveryCard.setmeal' => '会员卡套餐',
				'deliveryCard.config' => '会员卡设置',
				'deliveryCard.cover' => '入口设置',
			)
		);
	}
	if(check_plugin_perm('qianfanApp')) {
		$all_perms['qianfanApp'] = array(
			'title' => '千帆APP整合',
			'perms' => array(
				'qianfanApp.config' => '设置',
			)
		);
	}
	if(check_plugin_perm('majiaApp')) {
		$all_perms['majiaApp'] = array(
			'title' => '马甲APP整合',
			'perms' => array(
				'majiaApp.config' => '设置',
			)
		);
	}
	if(check_plugin_perm('creditshop')) {
		$all_perms['creditshop'] = array(
			'title' => '积分兑换',
			'perms' => array(
				'creditshop.order' => '兑换记录',
				'creditshop.config' => '系统设置',
				'creditshop.cover' => '入口设置'
			)
		);
	}
	if(check_plugin_perm('shareRedpacket')) {
		$all_perms['shareRedpacket'] = array(
			'title' => '分享有礼',
			'perms' => array(
				'shareRedpacket.activity' => '红包活动',
				'shareRedpacket.cover' => '入口设置'
			)
		);
	}
	if(check_plugin_perm('freeLunch')) {
		$all_perms['freeLunch'] = array(
			'title' => '霸王餐',
			'perms' => array(
				'freeLunch.activity' => '霸王餐',
				'freeLunch.cover' => '入口设置'
			)
		);
	}
	if(check_plugin_perm('bargain')) {
		$all_perms['bargain'] = array(
			'title' => '天天特价',
			'perms' => array(
				'bargain.index' => '活动设置',
				'bargain.cover' => '入口设置',
				'bargain.goods' => '活动商品'
			)
		);
	}
	if(check_plugin_perm('ordergrant')) {
		$all_perms['ordergrant'] = array(
			'title' => '下单有礼',
			'perms' => array(
				'ordergrant.config' => '活动设置',
				'ordergrant.record' => '奖励记录',
				'ordergrant.share' => '分享订单',
				'ordergrant.cover' => '入口设置',
			)
		);
	}
	if(check_plugin_perm('superRedpacket')) {
		$all_perms['superRedpacket'] = array(
			'title' => '超级红包',
			'perms' => array(
				'superRedpacket.grant' => '发放红包',
				'superRedpacket.share' => '分享红包'
			)
		);
	}
	if(check_plugin_perm('diypage')) {
		$all_perms['diypage'] = array(
			'title' => '平台装修',
			'perms' => array(
				'diypage.menu' => '自定义菜单',
				'diypage.mall' => '商城设置',
				'diypage.danmu' => '订单弹幕',
				'diypage.diy' => '页面管理',
				'diypage.template' => '模板管理'
			)
		);
	}
	return $all_perms;
}