<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'index';
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

$config = $_W['we7_wmall']['config']['mall'];
$config_takeout = $_W['we7_wmall']['config']['takeout'];

$_W['page']['title'] = $config['title'];
if($config['version'] == 2) {
	$url = imurl('wmall/store/goods', array('sid' => $config['default_sid']));
	header('location:' . $url);
	die;
}

$slides = sys_fetch_slide(2);
$categorys = store_fetchall_category();
$categorys_chunk = array_chunk($categorys, 8);
$notices = pdo_fetchall('select id,title,link,displayorder,status from' .tablename('tiny_wmall_notice') ." where uniacid = :uniacid and status = 1 order by displayorder desc", array(':uniacid' => $_W['uniacid']));
$discounts = store_discounts();
$cubes = pdo_fetchall('select * from ' . tablename('tiny_wmall_cube') . ' where uniacid = :uniacid order by displayorder desc', array(':uniacid' => $_W['uniacid']));
if($ta == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	isetcookie('__lat', $lat, 600);
	isetcookie('__lng', $lng, 600);
	$recommends = pdo_fetchall('select id,title,logo,content,is_rest,location_x,location_y,forward_mode,forward_url from' .tablename('tiny_wmall_store') . 'where uniacid = :uniacid and is_recommend = 1 and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']));
	$min = 0;
	if(!empty($recommends)) {
		foreach($recommends as $key => &$da) {
			$da['logo'] = tomedia($da['logo']);
			if(!empty($lng) && !empty($lat)) {
				$da['distance'] = distanceBetween($da['location_y'], $da['location_x'], $lng, $lat);
				$da['distance'] = round($da['distance'] / 1000, 2);
				$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);
				$in = is_in_store_radius($da, array($lng, $lat));
				if($config['store_overradius_display'] == 2 && !$in) {
					unset($recommends[$key]);
				}
			} else {
				$da['distance'] = 0;
			}
		}
	}
	$recommends = array_values($recommends);
	$respon = array('error' => 0, 'message' => $recommends, 'min' => $min);
	imessage($respon, '', 'ajax');
}
include itemplate('home/home');