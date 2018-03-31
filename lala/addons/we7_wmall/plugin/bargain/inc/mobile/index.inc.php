<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
if (!(check_plugin_perm('bargain'))) 
{
	imessage('暂未开放此功能', '', 'error');
}
$config_bargain = get_plugin_config('bargain');
if ($config_bargain['status'] == 0) 
{
	imessage('暂未开放此功能', '', 'error');
}
$_W['page']['title'] = '天天特价';
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'index'));
if ($op == 'index') 
{
	$condition = ' where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1';
	$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']);
	$id = intval($_GPC['min']);
	if (0 < $id) 
	{
		$condition .= ' and a.goods_id < :id';
		$params[':id'] = $id;
	}
	$bargains = pdo_fetchall('select a.discount_price,a.goods_id,a.discount_available_total,b.title,b.thumb,b.price,b.sid,b.sailed from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_goods') . ' as b on a.goods_id = b.id ' . $condition . ' order by a.mall_displayorder desc limit 100', $params, 'goods_id');
	$min = 0;
	if (!(empty($bargains))) 
	{
		$stores = pdo_fetchall('select distinct(a.sid),b.title,b.is_in_business from ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'sid');
		foreach ($bargains as &$row ) 
		{
			if ($row['discount_available_total'] == -1) 
			{
				$row['discount_available_total'] = '无限';
			}
			$row['store'] = $stores[$row['sid']];
			$row['thumb'] = tomedia($row['thumb']);
			$row['discount'] = round(($row['discount_price'] / $row['price']) * 10, 1);
		}
		$min = min(array_keys($bargains));
	}
	if ($_W['ispost']) 
	{
		$bargains = array_values($bargains);
		$respon = array('errno' => 0, 'message' => $bargains, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}
if ($op == 'detail') 
{
	$id = intval($_GPC['id']);
	mload()->model('goods');
	$goods = goods_fetch($id);
	if (is_error($goods)) 
	{
		imessage(error(-1, '商品不存在或已删除'), '', 'ajax');
	}
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $goods['sid']), array('is_in_business'));
	$goods['is_in_business'] = $store['is_in_business'];
	imessage(error(0, $goods), '', 'ajax');
}
$_share = array('title' => $config_bargain['share']['title'], 'desc' => $config_bargain['share']['desc'], 'link' => (!(empty($config_bargain['share']['link'])) ? $config_bargain['share']['link'] : imurl('bargain/index', array(), true)), 'imgUrl' => tomedia($config_bargain['share']['imgUrl']));
include itemplate('index');
?>