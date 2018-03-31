<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '城市代理';
$store['delivery_areas'] = array(
	'M0123456789101' => array(
		'send_price'     => 10,
		'delivery_price' => 5,
		'colorType'      => 1,
		'path'           => array(
			array('116.403322', '39.920255'),
			array('116.410703', '39.897555'),
			array('116.402292', '39.892353'),
			array('116.389846', '39.891365')
			)
		),
	'M0123456789102' => array(
		'send_price'     => 20,
		'delivery_price' => 10,
		'colorType'      => 2,
		'path'           => array(
			array('116.355973', '39.922133'),
			array('116.371079', '39.922857'),
			array('116.366788', '39.914365')
			)
		)
	);
include itemplate('order/agent');

?>
