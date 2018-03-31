<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('file');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'deliveryer';

mkdirs(MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/ios");
mkdirs(MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/android");
$downurls = array(
	'deliveryer' => array(
		'ios' => MODULE_URL . "resource/apps/{$_W['uniacid']}/ios/deliveryman_1.0.apk",
		'android' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/deliveryman_1.0.apk",
	),
	'manager' => array(
		'ios' => MODULE_URL . "resource/apps/{$_W['uniacid']}/ios/manager.apk",
		'android' => MODULE_URL . "resource/apps/{$_W['uniacid']}/android/manager.apk",
	),
);

if($op == 'deliveryer') {
	$_W['page']['title'] = '配送员app设置';
	if($_W['ispost']) {
		$data = array(
			'serial_sn' => trim($_GPC['deliveryer']['serial_sn']),
			'push_key' => trim($_GPC['deliveryer']['push_key']),
			'push_secret' => trim($_GPC['deliveryer']['push_secret']),
			'push_tags' => $_config['app']['deliveryer']['push_tags'],
			'ios_build_type' => intval($_GPC['deliveryer']['ios_build_type']),
			'android_version' => intval($_GPC['deliveryer']['android_version']),
			'version' => array(
				'ios' => trim($_GPC['deliveryer']['version']['ios']),
				'android' => 1,
			),
		);
		if(empty($_config['app']['deliveryer']['push_tags'])) {
			$data['push_tags'] = array(
				'working' => random(10),
				'rest' => random(10),
			);
		}
		set_system_config('app.deliveryer', $data);
		imessage(error(0, '设置app参数成功'), 'refresh', 'ajax');
	}
	$app = $_config['app'];
}

if($op == 'manager') {
	$_W['page']['title'] = '商家app设置';
	$app = $_config['app'];
	load()->func('file');
	$path = "resource/mp3/{$_W['uniacid']}/";
	mkdirs(MODULE_ROOT . '/' . $path);
	$files = array();
	if($_W['ispost']) {
		foreach($_FILES as $key => $val) {
			if(!empty($val['name']) && $val['error'] == 0) {
				$pathinfo = pathinfo($val['name']);
				$ext = strtolower($pathinfo['extension']);
				$basename =  "{$key}.{$ext}";
				if(!file_move($val['tmp_name'],  MODULE_ROOT . '/' . $path . $basename)) {
					imessage(error(-1, '保存上传文件失败'), referer(), 'ajax');
				}
				$files[$key] = $basename;
			}
			if(empty($files[$key])) {
				$files[$key] = $app['manager']['phonic'][$key];
			}
		}
		$data = array(
			'serial_sn' => trim($_GPC['manager']['serial_sn']),
			'push_key' => trim($_GPC['manager']['push_key']),
			'push_secret' => trim($_GPC['manager']['push_secret']),
			'ios_build_type' => intval($_GPC['manager']['ios_build_type']),
			'version' => array(
				'ios' => trim($_GPC['manager']['version']['ios']),
				'android' => 1,
			),
			'phonic' => $files
		);
		set_system_config('app.manager', $data);
		imessage(error(0, '设置app参数成功'), 'refresh', 'ajax');
	}
}

if($op == 'customer') {
	$_W['page']['title'] = '顾客app设置';
	if($_W['ispost']) {
		$login = array(
			'qq' => intval($_GPC['qq']),
			'wx' => intval($_GPC['wx']),
		);
		$data = array(
			'serial_sn' => trim($_GPC['customer']['serial_sn']),
			'appid' => trim($_GPC['customer']['appid']),
			'key' => trim($_GPC['customer']['key']),
			'login' => $login
		);
		set_system_config('app.customer', $data);
		imessage(error(0, '设置app参数成功'), 'refresh', 'ajax');
	}
	$app = $_config['app'];
}
include itemplate('config/app');