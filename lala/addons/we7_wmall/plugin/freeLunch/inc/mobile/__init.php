<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$freelunch = freelunch_record_init();

if (is_error($freelunch)) {
	imessage($freelunch['message'], '', 'info');
}

$_share = array('title' => $freelunch['share']['title'], 'desc' => $freelunch['share']['desc'], 'link' => !empty($freelunch['share']['link']) ? $freelunch['share']['link'] : imurl('freeLunch/freeLunch/index', array(), true), 'imgUrl' => tomedia($freelunch['share']['imgUrl']));

?>
