<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'payment';

if($op == 'payment') {
	$_W['page']['title'] = '支付方式';
	if($_W['ispost']) {
		load()->func('file');
		$config_old_payment = $_config['payment'];
		$config_payment = array(
			'wechat' => array(
				'type' => trim($_GPC['wechat']['type']) ? trim($_GPC['wechat']['type']) : 'default',
				'default' => array(
					'version' => intval($_GPC['wechat']['core']['version']) ? intval($_GPC['wechat']['core']['version']) : 2,
					'appid' => trim($_GPC['wechat']['core']['appid']),
					'appsecret' => trim($_GPC['wechat']['core']['appsecret']),
					'mchid' => trim($_GPC['wechat']['core']['mchid']),
					'apikey' => trim($_GPC['wechat']['core']['apikey']),
					'partner' => trim($_GPC['wechat']['core']['partner']),
					'key' => trim($_GPC['wechat']['core']['key']),
					'signkey' => trim($_GPC['wechat']['core']['signkey']),
					'apiclient_cert' => $config_old_payment['wechat']['default']['apiclient_cert'],
					'apiclient_key' => $config_old_payment['wechat']['default']['apiclient_key'],
					'rootca' => $config_old_payment['wechat']['default']['rootca'],
				),
				'borrow' => array(
					'version' => intval($_GPC['wechat']['core']['version']) ? intval($_GPC['wechat']['core']['version']) : 2,
					'appid' => trim($_GPC['wechat']['core']['appid']),
					'appsecret' => trim($_GPC['wechat']['core']['appsecret']),
					'mchid' => trim($_GPC['wechat']['core']['mchid']),
					'apikey' => trim($_GPC['wechat']['core']['apikey']),
					'partner' => trim($_GPC['wechat']['core']['partner']),
					'key' => trim($_GPC['wechat']['core']['key']),
					'signkey' => trim($_GPC['wechat']['core']['signkey']),
					'apiclient_cert' => $config_old_payment['wechat']['borrow']['apiclient_cert'],
					'apiclient_key' => $config_old_payment['wechat']['borrow']['apiclient_key'],
					'rootca' => $config_old_payment['wechat']['borrow']['rootca'],
				),
				'partner' => array(
					'version' => 2,
					'appid' => trim($_GPC['wechat']['partner']['appid']),
					'appsecret' => trim($_GPC['wechat']['partner']['appsecret']),
					'sub_appid' => trim($_GPC['wechat']['partner']['sub_appid']),
					'mchid' => trim($_GPC['wechat']['partner']['mchid']),
					'sub_mch_id' => trim($_GPC['wechat']['partner']['sub_mch_id']),
					'apikey' => trim($_GPC['wechat']['partner']['apikey']),
					'apiclient_cert' => $config_old_payment['wechat']['partner']['apiclient_cert'],
					'apiclient_key' => $config_old_payment['wechat']['partner']['apiclient_key'],
					'rootca' => $config_old_payment['wechat']['partner']['rootca'],
				),
				'borrow_partner' => array(
					'version' => 2,
					'appid' => trim($_GPC['wechat']['partner']['appid']),
					'appsecret' => trim($_GPC['wechat']['partner']['appsecret']),
					'sub_appid' => trim($_GPC['wechat']['partner']['sub_appid']),
					'mchid' => trim($_GPC['wechat']['partner']['mchid']),
					'sub_mch_id' => trim($_GPC['wechat']['partner']['sub_mch_id']),
					'apikey' => trim($_GPC['wechat']['partner']['apikey']),
					'apiclient_cert' => $config_old_payment['wechat']['borrow_partner']['apiclient_cert'],
					'apiclient_key' => $config_old_payment['wechat']['borrow_partner']['apiclient_key'],
					'rootca' => $config_old_payment['wechat']['borrow_partner']['rootca'],
				),
			),
			'alipay' => array(
				'account' => trim($_GPC['alipay']['account']),
				'partner' => trim($_GPC['alipay']['partner']),
				'secret' => trim($_GPC['alipay']['secret']),
				'appid' => trim($_GPC['alipay']['appid']),
				'private_key' => $config_old_payment['alipay']['private_key'],
				'public_key' => $config_old_payment['alipay']['public_key'],
			),
			'app_wechat' => array(
				'appid' => trim($_GPC['app']['wechat']['appid']),
				'appsecret' => trim($_GPC['app']['wechat']['appsecret']),
				'mchid' => trim($_GPC['app']['wechat']['mchid']),
				'merchname' => trim($_GPC['app']['wechat']['merchname']),
				'apikey' => trim($_GPC['app']['wechat']['apikey']),
				'apiclient_cert' => $config_old_payment['app_wechat']['apiclient_cert'],
				'apiclient_key' => $config_old_payment['app_wechat']['apiclient_key'],
				'rootca' => $config_old_payment['app_wechat']['rootca'],
			),
			'app_alipay' => array(
				'appid' => trim($_GPC['app']['alipay']['appid']),
				'private_key' => $config_old_payment['app_alipay']['private_key'],
				'public_key' => $config_old_payment['app_alipay']['public_key'],
			),
			'weixin' => array(),
			'wap' => array(),
			'app' => array(),
			'peerpay' => array(),
		);
		if($config_payment['wechat']['type'] == 'default') {
			$config_payment['wechat']['borrow'] = $config_old_payment['wechat']['borrow'];
		} elseif($config_payment['wechat']['type'] == 'borrow') {
			$config_payment['wechat']['default'] = $config_old_payment['wechat']['default'];
		}
		foreach($_GPC['weixin'] as $key => $row) {
			if($row == 1) {
				$config_payment['weixin'][] = $key;
			}
		}
		$config_payment['available'] = $config_payment['weixin'];
		if(!empty($_GPC['wap'])) {
			foreach($_GPC['wap'] as $key => $row) {
				if($row == 1) {
					$config_payment['wap'][] = $key;
				}
			}
		}
		if(!empty($_GPC['app_type'])) {
			foreach($_GPC['app_type'] as $key => $row) {
				if($row == 1) {
					$config_payment['app'][] = $key;
				}
			}
		}

		unset($config_payment['weixin']['help_words'], $config_payment['weixin']['notes']);
		if(!empty($_GPC['help_words'])) {
			$config_payment['peerpay']['help_words'] = array_filter($_GPC['help_words']);
		}
		if(!empty($_GPC['notes'])) {
			$config_payment['peerpay']['notes'] = array_filter($_GPC['notes']);
		}
		if(intval($_GPC['peerpay_max_limit']) > 0) {
			$config_payment['peerpay']['peerpay_max_limit'] = intval($_GPC['peerpay_max_limit']);
		}
	//echo $config_payment['wechat']['type'];die;
		$keys = array('apiclient_cert', 'apiclient_key', 'rootca');
		foreach($keys as $key) {
			if(!empty($_GPC['wechat']['core'][$key]) || !empty($_GPC['wechat']['partner'][$key])) {
				$text = trim($_GPC['wechat']['core'][$key]) ? trim($_GPC['wechat']['core'][$key]) : trim($_GPC['wechat']['partner'][$key]);
				@unlink(MODULE_ROOT . "/cert/{$config_payment['wechat'][$config_payment['wechat']['type']][$key]}/{$key}.pem");
				@rmdir(MODULE_ROOT . "/cert/{$config_payment['wechat'][$config_payment['wechat']['type']][$key]}");
				$name = random(10);
				$status = ifile_put_contents("cert/{$name}/{$key}.pem", $text);
				$config_payment['wechat'][$config_payment['wechat']['type']][$key] = $name;
			}
		}
		$keys = array('apiclient_cert', 'apiclient_key', 'rootca');
		foreach($keys as $key) {
			if(!empty($_GPC['app']['wechat'][$key])) {
				$text = trim($_GPC['app']['wechat'][$key]);
				@unlink(MODULE_ROOT . "/cert/{$config_payment['app_wechat'][$key]}/{$key}.pem");
				@rmdir(MODULE_ROOT . "/cert/{$config_payment['app_wechat'][$key]}");
				$name = random(10);
				$status = ifile_put_contents("cert/{$name}/{$key}.pem", $text);
				$config_payment['app_wechat'][$key] = $name;
			}
		}

		$keys = array('private_key', 'public_key');
		foreach($keys as $key) {
			if(!empty($_GPC['alipay'][$key])) {
				$text = $_GPC['alipay'][$key];
				$text = str_replace('\\r', '', $text);
				$text = str_replace('\\n', '', $text);
				$text = implode(str_split($text, 64), "\n");
				if($key == 'private_key') {
					$text = "-----BEGIN RSA PRIVATE KEY-----\n" .  $text . "\n-----END RSA PRIVATE KEY-----";
				} else {
					$text = "-----BEGIN PUBLIC KEY-----\n" .  $text . "\n-----END PUBLIC KEY-----";
				}
				@unlink(MODULE_ROOT . "/cert/{$config_payment['alipay'][$key]}/{$key}.pem");
				@rmdir(MODULE_ROOT . "/cert/{$config_payment['alipay'][$key]}");
				$name = random(10);
				$status = ifile_put_contents("cert/{$name}/{$key}.pem", $text);
				$config_payment['alipay'][$key] = $name;
			}
		}

		$keys = array('private_key', 'public_key');
		foreach($keys as $key) {
			if(!empty($_GPC['app']['alipay'][$key])) {
				$text = $_GPC['app']['alipay'][$key];
				$text = str_replace('\\r', '', $text);
				$text = str_replace('\\n', '', $text);
				$text = implode(str_split($text, 64), "\n");
				if($key == 'private_key') {
					$text = "-----BEGIN RSA PRIVATE KEY-----\n" .  $text . "\n-----END RSA PRIVATE KEY-----";
				} else {
					$text = "-----BEGIN PUBLIC KEY-----\n" .  $text . "\n-----END PUBLIC KEY-----";
				}
				@unlink(MODULE_ROOT . "/cert/{$config_payment['app_alipay'][$key]}/{$key}.pem");
				@rmdir(MODULE_ROOT . "/cert/{$config_payment['app_alipay'][$key]}");
				$name = random(10);
				$status = ifile_put_contents("cert/{$name}/{$key}.pem", $text);
				$config_payment['app_alipay'][$key] = $name;
			}
		}

		set_system_config('payment', $config_payment);
		imessage(error(0, '支付方式设置成功'), referer(), 'ajax');
	}
	$payment = $_config['payment'];
	if($payment['wechat']['type'] == 'default' || $payment['wechat']['type'] == 'borrow') {
		$payment['wechat']['core'] = $payment['wechat'][$payment['wechat']['type']];
	} else {
		$payment['wechat']['partner'] = $payment['wechat'][$payment['wechat']['type']];
	}
	include itemplate('config/payment');
}

if($op == 'recharge') {
	$_W['page']['title'] = '充值优惠';
	if($_W['ispost']) {
		if(!empty($_GPC['recharge'])) {
			$_GPC['recharge'] = str_replace('&nbsp;', '#nbsp;', $_GPC['recharge']);
			$_GPC['recharge'] = json_decode(str_replace('#nbsp;', '&nbsp;', html_entity_decode(urldecode($_GPC['recharge']))), true);
			foreach($_GPC['recharge'] as $recharge) {
				$charge = floatval($recharge['charge']);
				$back = floatval($recharge['back']);
				$type = trim($recharge['type']);
				if($charge && $back && $type) {
					$recharges[] = array(
						'charge' => $charge,
						'back' => $back,
						'type' => $type,
					);
				}
			}
		}
		$recharges = array(
			'status' => intval($_GPC['status']),
			'items' => $recharges,
		);
		set_system_config('recharge', $recharges);
		imessage(error(0, '设置充值活动成功'), referer(), 'ajax');
	}
	$recharge = $_config['recharge'];
	include itemplate('config/recharge');
}
