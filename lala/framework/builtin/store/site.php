<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

class StoreModuleSite extends WeModuleSite {
	public $modulename = 'store';
	private $left_menus;

	public function __construct() {
		load()->model('store');
		$this->left_menus = $this->leftMenu();
	}

	public function storeIsOpen() {
		global $_W;
		if ((!$_W['isfounder'] || user_is_vice_founder()) && $_W['setting']['store']['status'] == 1) {
			itoast('商城已被创始人关闭！', referer(), 'error');
		}
		return true;
	}

	public function getTypeName($type) {
		$sign = array(
			STORE_TYPE_MODULE => '应用模块',
			STORE_TYPE_ACCOUNT => '公众号个数',
			STORE_TYPE_WXAPP => '小程序个数',
			STORE_TYPE_WXAPP_MODULE => '小程序模块',
			STORE_TYPE_PACKAGE => '应用权限组',
			STORE_TYPE_API => '应用访问流量(API)',
			STORE_TYPE_ACCOUNT_RENEW => '公众号续费',
			STORE_TYPE_WXAPP_RENEW => '小程序续费'
		);
		return $sign[$type];
	}

	public function payResult($params) {
		global $_W;
		if($params['result'] == 'success' && $params['from'] == 'notify') {
			$order = pdo_get('site_store_order', array('id' => $params['tid'], 'type' => 1));
			if(!empty($order)) {
				$goods = pdo_get('site_store_goods', array('id' => $order['goodsid']));
				pdo_update('site_store_order', array('type' => 3), array('id' => $params['tid']));
				$type_name = $this->getTypeName($goods['type']);
				$message_notice_log = array(
					'message' => $order['buyer'] . date("Y-m-d H:i:s", $order['createtime']) . '在商城购买了' . $type_name . ', 支付金额' . $order['amount'] . ',支付成功',
					'uid' => $order['buyerid'],
					'sign' => $order['orderid'],
					'type' => MESSAGE_ORDER_TYPE,
					'create_time' => TIMESTAMP
				);
				pdo_insert('message_notice_log', $message_notice_log);
				if (in_array($goods['type'], array(STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW))) {
					$account_type = $goods['type'] == STORE_TYPE_ACCOUNT_RENEW ? 'uniacid' : 'wxapp';
					$account_num = $goods['type'] == STORE_TYPE_ACCOUNT_RENEW ? $goods['account_num'] : $goods['wxapp_num'];
					$account_info = uni_fetch($order[$account_type]);
					$account_endtime = strtotime('+' . $order['duration'] * $account_num . $goods['unit'], max(TIMESTAMP, $account_info['endtime']));
					pdo_update('account', array('endtime' => $account_endtime), array('uniacid' => $order[$account_type]));
					cache_delete("uniaccount:{$order[$account_type]}");
				}
				cache_delete(cache_system_key($order['uniacid'] . ':site_store_buy_' . $goods['type']));
				cache_build_account_modules($order['uniacid']);
			}
		}
		if($params['result'] == 'success' && $params['from'] == 'return') {
			header('Location: ' . $_W['siteroot'] . $this->createWebUrl('orders', array('direct' => 1)));
		}
	}

	public function doWebPaySetting() {
		$this->storeIsOpen();
		global $_W, $_GPC;
		if (!$_W['isfounder'] || user_is_vice_founder()) {
			itoast('', referer(), 'info');
		}
		$operate = $_GPC['operate'];
		$operates = array('alipay', 'wechat');
		$operate = in_array($operate, $operates) ? $operate : 'alipay';

		$_W['page']['title'] = '支付设置 - 商城';
		$settings = $_W['setting']['store_pay'];

		if (checksubmit('submit')) {
			if ($operate == 'alipay') {
				$settings['alipay'] = array(
					'switch' => intval($_GPC['switch']),
					'account' => trim($_GPC['account']),
					'partner' => trim($_GPC['partner']),
					'secret' => trim($_GPC['secret']),
				);
			} elseif ($operate == 'wechat') {
				if ($_GPC['switch'] == 1 && (empty($_GPC['appid']) || empty($_GPC['mchid']) || empty($_GPC['signkey']))) {
					itoast('请完善支付设置。', referer(), 'info');
				}
				$settings['wechat'] = array(
					'switch' => intval($_GPC['switch']),
					'appid' => $_GPC['appid'],
					'mchid' => $_GPC['mchid'],
					'signkey' => $_GPC['signkey'],
				);
			}
			setting_save($settings, 'store_pay');
			itoast('设置成功！', referer(), 'success');
		}
		if ($operate == 'alipay') {
			$alipay = $settings['alipay'];
		} elseif ($operate == 'wechat') {
			$wechat = $settings['wechat'];
		}
		include $this->template('paysetting');
	}

	public function doWebOrders() {
		$this->storeIsOpen();
		global $_GPC, $_W;
		load()->model('module');

		$operates = array('display', 'change_price', 'delete');
		$operate = $_GPC['operate'];
		$operate = in_array($operate, $operates) ? $operate : 'display';

		$_W['page']['title'] = '订单管理 - 商城';
		if (user_is_vice_founder()) {
			$role = 'buyer';
		} elseif (!empty($_W['isfounder'])) {
			$role = 'seller';
		} else {
			$role = 'buyer';
		}

		if ($operate == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;

			$store_table = table('store');
			if (isset($_GPC['type']) && intval($_GPC['type']) > 0) {
				$order_type = intval($_GPC['type']);
				$store_table->searchOrderType($order_type);
			}
			$store_table->searchWithOrderid($_GPC['orderid']);
			if (empty($_W['isfounder']) || user_is_vice_founder()) {
				$store_table->searchOrderWithUid($_W['uid']);
			}
			$order_list = $store_table->searchOrderList($pindex, $psize);
			$total = $store_table->getLastQueryTotal();
			$pager = pagination($total, $pindex, $psize);
			if (!empty($order_list)) {
				foreach ($order_list as $key => &$order) {
					if (empty($_W['isfounder']) && $order['type'] == 2) {
						unset($order_list[$key]);
					}
					$order['createtime'] = date('Y-m-d H:i:s', $order['createtime']);
					$order['goods_info'] = store_goods_info($order['goodsid']);
					$order['abstract_amount'] = $order['duration'] * $order['goods_info']['price'];
					if (!empty($order['goods_info']) && ($order['goods_info']['type'] == STORE_TYPE_MODULE || $order['goods_info']['type'] == STORE_TYPE_WXAPP_MODULE)) {
						$order['goods_info']['module_info'] = module_fetch($order['goods_info']['module']);
					}
				}
				unset($order);
			}
		}

		if ($operate == 'change_price') {
			if (user_is_vice_founder() || empty($_W['isfounder'])) {
				iajax(-1, '无权限更改！');
			}
			$id = intval($_GPC['id']);
			$price = floatval($_GPC['price']);
			$if_exists = store_order_info($id);
			if (empty($if_exists)) {
				iajax(-1, '订单不存在！');
			}
			$result = store_order_change_price($id, $price);
			if (!empty($result)) {
				iajax(0, '修改成功！');
			} else {
				iajax(-1, '修改失败！');
			}
		}

		if ($operate == 'delete') {
			$id = intval($_GPC['id']);
			if (empty($id)) {
				itoast('订单错误，请刷新后重试！');
			}
			$order_info = store_order_info($id);
			if (empty($order_info)) {
				itoast('订单不存在！');
			}
			if ($order_info['type'] != STORE_ORDER_PLACE) {
				itoast('只可删除未完成交易的订单！');
			}
			$result = store_order_delete($id);
			if (!empty($result)) {
				itoast('删除成功！', referer(), 'success');
			} else {
				itoast('删除失败，请稍候重试！', referer(), 'error');
			}
		}
		include $this->template('orders');
	}

	public function doWebSetting() {
		$this->storeIsOpen();
		global $_GPC, $_W;
		if (!$_W['isfounder'] || user_is_vice_founder()) {
			itoast('', referer(), 'info');
		}
		$operate = $_GPC['operate'];
		$operates = array('store_status', 'menu');
		$operate = in_array($operate, $operates) ? $operate : 'store_status';

		$_W['page']['title'] = '商城设置 - 商城';

		$settings = (array)$_W['setting']['store'];
		if ($operate == 'store_status') {
			if (checksubmit('submit')) {
				$status = intval($_GPC['status']) > 0 ? 1 : 0;
				$settings['status'] = $status;
				setting_save($settings, 'store');
				itoast('更新设置成功！', referer(), 'success');
			}
		}
		if ($operate == 'menu') {
			$left_menu = $this->leftMenu();
			$goods_menu = !empty($left_menu['store_goods']) ? $left_menu['store_goods']['menu'] : array();
			if (checksubmit('submit')) {
				foreach ($goods_menu as $key => $menu) {
					$settings[$key] = intval($_GPC['hide'][$key]) > 0 ? 1 : 0;
				}
				setting_save($settings, 'store');
				itoast('更新设置成功！', referer(), 'success');
			}
		}
		include $this->template('storesetting');
	}

	public function doWebGoodsSeller() {
		$this->storeIsOpen();
		global $_GPC, $_W;
		load()->model('module');
		if (!$_W['isfounder'] || user_is_vice_founder()) {
			itoast('', referer(), 'info');
		}
		$operate = $_GPC['operate'];
		$operates = array('display', 'delete', 'changestatus');
		$operate = in_array($operate, $operates) ? $operate : 'display';
		$type = intval($_GPC['type']) > 0 ? intval($_GPC['type']) : STORE_TYPE_MODULE;

		$_W['page']['title'] = '商品列表 - 商城管理 - 商城';
		if ($operate == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;

			$store_table = table('store');
			$keyword = trim($_GPC['keyword']);
			if (!empty($keyword)) {
				$store_table->searchWithKeyword($keyword);
			}
			$status = isset($_GPC['online']) && $_GPC['online'] == 0 ? 0 : 1;
			$store_table->searchWithStatus($status);
			if(isset($_GPC['letter']) && strlen($_GPC['letter']) == 1) {
				$store_table->searchWithLetter($_GPC['letter']);
			}
			$goods_list = $store_table->searchGoodsList($type, $pindex, $psize);
			$total = $goods_list['total'];
			$goods_list = $goods_list['goods_list'];
			$pager = pagination($total, $pindex, $psize);
			if (!empty($goods_list)) {
				foreach ($goods_list as &$good) {
					$good['module_info'] = module_fetch($good['module']);
				}
				unset($good);
			}
			$module_list = array();
			if (in_array($type, array(STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE))) {
				$modules = user_uniacid_modules($_W['uid']);
				$have_module_goods = $store_table->searchHaveModule($type);
				$have_module_goods = array_keys($have_module_goods);
				$have_module_goods = array_unique($have_module_goods);
				if (!empty($modules)) {
					foreach ($modules as $module) {
						if (in_array ($module['name'], $have_module_goods) || $type == STORE_TYPE_MODULE && $module['app_support'] != 2 || $type == STORE_TYPE_WXAPP_MODULE && $module['wxapp_support'] != 2) {
							continue;
						}
						$module = module_fetch ($module['name']);
						$module_list[] = $module;
					}
				}
			}
			if ($type == STORE_TYPE_PACKAGE) {
				$groups = uni_groups();
			}
		}

		if ($operate == 'changestatus' || $operate == 'delete') {
			$id = intval($_GPC['id']);
			$if_exist = store_goods_info($id);
			if (empty($if_exist)) {
				itoast('商品不存在，请刷新后重试！', referer(), 'error');
			}
		}
		if ($operate == 'changestatus') {
			$result = store_goods_changestatus($id);
			if (!empty($result)) {
				itoast('更新成功！', referer(), 'success');
			} else {
				itoast('更新失败！', referer(), 'error');
			}
		}

		if ($operate == 'delete') {
			$result = store_goods_delete($id);
			if (!empty($result)) {
				itoast('删除成功！', referer(), 'success');
			} else {
				itoast('删除失败！', referer(), 'error');
			}
		}
		include $this->template('goodsseller');
	}

	public function doWebGoodsPost() {
		$this->storeIsOpen();
		global $_GPC, $_W;
		if (!$_W['isfounder'] || user_is_vice_founder()) {
			itoast('', referer(), 'info');
		}
		$operate = $_GPC['operate'];
		$operates = array('post', 'add');
		$operate = in_array($operate, $operates) ? $operate : 'post';
		$type = intval($_GPC['type']) > 0 ? intval($_GPC['type']) : STORE_TYPE_MODULE;
		$_W['page']['title'] = '编辑商品 - 商城管理 - 商城';

		if ($operate == 'post') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				if (!empty($_GPC['price']) && !is_numeric($_GPC['price'])) {
					itoast('请填写有效数字！', referer(), 'error');
				}
				$data = array(
					'unit' => $_GPC['unit'],
					'account_num' => $_GPC['account_num'],
					'wxapp_num' => $_GPC['wxapp_num'],
					'module_group' => $_GPC['module_group'],
					'type' => $_GPC['type'],
					'title' => !empty($_GPC['title']) ? trim($_GPC['title']) : '',
					'price' => is_numeric($_GPC['price']) ? floatval($_GPC['price']) : 0,
					'slide' => !empty($_GPC['slide']) ? iserializer($_GPC['slide']) : '',
					'api_num' => is_numeric($_GPC['api_num']) ? intval($_GPC['api_num']) : 0,
					'description' => htmlspecialchars_decode(remove_xss($_GPC['description'])),
				);
				if ($_GPC['type'] == STORE_TYPE_API) {
					$data['title'] = '应用访问量';
				}
				if ($_GPC['type'] == STORE_TYPE_PACKAGE) {
					$data['title'] = '应用权限组';
				}
				if ($_GPC['submit'] == '保存并上架') {
					$data['status'] = 1;
				}
				if (!empty($id)) {
					$data['id'] = $id;
				}
				$result = store_goods_post($data);
				if (!empty($result)) {
					if (!empty($id)) {
						itoast('编辑成功！', $this->createWebUrl('goodsseller', array('direct' =>1, 'type' => $type, 'online' => $data['status'])), 'success');
					} else {
						itoast('添加成功！', $this->createWebUrl('goodsSeller', array('direct' =>1, 'type' => $type)), 'success');
					}
				} else {
					itoast('未作任何更改或编辑/添加失败！', referer(), 'error');
				}
			}

			if (!empty($id)) {
				$goods_info = store_goods_info($id);
				$goods_info['slide'] = !empty($goods_info['slide']) ? (array)iunserializer($goods_info['slide']) : array();
				$goods_info['price'] = floatval($goods_info['price']);
			}
			if ($_GPC['type'] == STORE_TYPE_PACKAGE) {
				$module_groups = uni_groups();
			}
		}
		if ($operate == 'add') {
			if (empty($_GPC['module']) && $type == STORE_TYPE_MODULE) {
				iajax(-1, '请选择一个模块！');
			}
			$data = array(
				'type' => $type,
				'title' => !empty($_GPC['module']['title']) ? trim($_GPC['module']['title']) : trim($_GPC['title']),
				'module' => !empty($_GPC['module']['name']) ? trim($_GPC['module']['name']) : '',
				'synopsis' => !empty($_GPC['module']['ability']) ? trim($_GPC['module']['ability']) : '',
				'description' => !empty($_GPC['module']['description']) ? trim($_GPC['module']['description']) : '',
				'api_num' => is_numeric($_GPC['visit_times']) ? intval($_GPC['visit_times']) : 0,
				'price' => is_numeric($_GPC['price']) ? floatval($_GPC['price']) : 0,
				'status' => !empty($_GPC['online']) ? STATUS_ON : STATUS_OFF,
			);
			$result = store_goods_post($data);
			if (!empty($result)) {
				if (isset($_GPC['toedit']) && !empty($_GPC['toedit'])) {
					$id = pdo_insertid();
					iajax(0, $id);
				} else {
					iajax(0, '添加成功！');
				}
			} else {
				iajax(-1, '添加失败！');
			}
		}
		include $this->template('goodspost');
	}

	public function doWebGoodsBuyer() {
		$this->storeIsOpen();
		global $_GPC, $_W;
		load ()->model ('module');
		load ()->model ('payment');
		load ()->func ('communication');
		load()->library('qrcode');
		$operate = $_GPC['operate'];
		$operates = array ('display', 'goods_info', 'get_expiretime', 'submit_order', 'pay_order');
		$operate = in_array ($operate, $operates) ? $operate : 'display';
		$_W['page']['title'] = '商品列表 - 商城';

		if ($operate == 'display') {
			$pageindex = max (intval ($_GPC['page']), 1);
			$pagesize = 24;
			$type = !empty($_GPC['type']) ? $_GPC['type'] : 1;
			$store_table = table ('store');
			$store_table->searchWithStatus (1);
			$store_table = $store_table->searchGoodsList ($type, $pageindex, $pagesize);
			$store_goods = $store_table['goods_list'];
			if (in_array($type, array(STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE)) && is_array ($store_goods)) {
				foreach ($store_goods as $key => &$goods) {
					$goods['module'] = module_fetch ($goods['module']);
				}
				unset($goods);
			}
			if ($_GPC['type'] == STORE_TYPE_PACKAGE) {
				$module_groups = uni_groups();
			}
			$pager = pagination ($store_table['total'], $pageindex, $pagesize);
		}

		if ($operate == 'goods_info') {
			$goods = intval ($_GPC['goods']);
			if (empty($goods)) {
				itoast ('商品不存在', '', 'info');
			}
			$goods = pdo_get ('site_store_goods', array ('id' => $goods));
			if (in_array($goods['type'], array(STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE, STORE_TYPE_API))) {
				$goods['module'] = module_fetch ($goods['module']);
				$goods['slide'] = iunserializer ($goods['slide']);
			} elseif (in_array($goods['type'], array(STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP))) {
				$goods['title'] = $goods['type'] == STORE_TYPE_ACCOUNT ? '公众号' : '小程序';
				$goods['num'] = $goods['type'] == STORE_TYPE_ACCOUNT ? $goods['account_num'] : $goods['wxapp_num'];
			} elseif ($goods['type'] == STORE_TYPE_PACKAGE) {
				$module_groups = uni_groups();
			}
			$account_table = table ('account');
			$user_account = $account_table->userOwnedAccount();
			$wxapp_account_list = array();
			if (!empty($user_account) && is_array($user_account)) {
				foreach ($user_account as $key => $account) {
					$default_account = uni_fetch($account['uniacid']);
					if (in_array($goods['type'],  array(STORE_TYPE_MODULE, STORE_TYPE_ACCOUNT_RENEW)) && !in_array($default_account['type'], array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH)) || in_array($goods['type'], array(STORE_TYPE_WXAPP_MODULE, STORE_TYPE_WXAPP_RENEW)) && $default_account['type'] != 4) {
						unset($user_account[$key]);
					}
					if (in_array($goods['type'], array(STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW)) && $default_account['endtime'] <= 0) {
						unset($user_account[$key]);
					}
					if ($goods['type'] == STORE_TYPE_PACKAGE && !empty($module_groups[$goods['module_group']]['wxapp']) && $default_account['type'] == 4) {
						$wxapp_account_list[] = array('uniacid' => $default_account['uniacid'], 'name' => $default_account['name']);
					}
				}
			}
			reset($user_account);
			reset($wxapp_account_list);
			$default_account = current($user_account);
			$default_account = !empty($_GPC['uniacid']) ? $_GPC['uniacid'] : $default_account['uniacid'];
			$default_wxapp = current($wxapp_account_list);
			$default_wxapp = !empty($_GPC['wxapp']) ? $_GPC['wxapp'] : $default_wxapp['uniacid'];
			if (in_array($goods['type'], array(STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE)) && empty($user_account)) {
				$type_name = $goods['type'] == STORE_TYPE_MODULE ? '公众号' : '小程序';
				itoast("您没有可操作的{$type_name}，请先创建{$type_name}后购买模块.", referer(), 'info');
			}
			$pay_way = array();
			if (!empty($_W['setting']['store_pay']) && is_array($_W['setting']['store_pay']) && ($_W['setting']['store_pay']['alipay']['switch'] == 1 || $_W['setting']['store_pay']['wechat']['switch'] == 1)) {
				foreach ($_W['setting']['store_pay'] as $way =>  $setting) {
					if ($setting['switch'] == 1) {
						$pay_way[$way] = $setting;
						if ($way == 'alipay') {
							$pay_way[$way]['title'] = '支付宝';
						} elseif ($way == 'wechat') {
							$pay_way[$way]['title'] = '微信';
						}
					}
				}
			} else {
				itoast('没有有效的支付方式.', referer(), 'info');
			}
		}


		if ($operate == 'get_expiretime') {
			$duration = intval ($_GPC['duration']);
			$date = date ('Y-m-d', strtotime ('+' . $duration . $_GPC['unit'], time ()));
			iajax (0, $date);
		}

		if ($operate == 'submit_order') {
			$uniacid = intval ($_GPC['uniacid']);
			$goodsid = intval($_GPC['goodsid']);
			if (empty($_GPC['type'])) {
				iajax(-1, '请选择支付方式。');
			}
			if (empty($goodsid)) {
				iajax(-1, '参数错误！');
			}
			$user_account = table('account')->userOwnedAccount();
			$goods_info = store_goods_info($goodsid);
			if (in_array($goods_info['type'], array(STORE_TYPE_PACKAGE, STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE, STORE_TYPE_API, STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW))) {
				if (empty($uniacid)) {
					iajax(-1, '请选择公众号！');
				}
				if (empty($user_account[$uniacid])) {
					iajax(-1, '非法公众号！');
				}
			}
			if (empty($goods_info)) {
				iajax(-1, '商品不存在！');
			}
			$uid = empty($_W['uid']) ? '000000' : sprintf ("%06d", $_W['uid']);
			$orderid = date ('YmdHis') . $uid . random (8, 1);
			$duration = intval ($_GPC['duration']);
			$order = array (
				'orderid' => $orderid,
				'duration' => $duration,
				'amount' => $goods_info['price'] * $duration,
				'goodsid' => $goodsid,
				'buyer' => $_W['user']['username'],
				'buyerid' => $_W['uid'],
				'type' => STORE_ORDER_PLACE,
				'createtime' => time(),
				'uniacid' => $uniacid,
				'wxapp' => intval($_GPC['wxapp'])
			);
			if (in_array($goods_info['type'], array(STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP, STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE, STORE_TYPE_PACKAGE))) {
				$history_order_endtime = pdo_getcolumn('site_store_order', array('goodsid' => $goodsid, 'buyerid' => $_W['uid']), 'max(endtime)');
				$order['endtime'] = strtotime('+' . $duration . $goods_info['unit'],  max($history_order_endtime, time()));
			}
			if (in_array($goods_info['type'], array(STORE_TYPE_WXAPP, STORE_TYPE_WXAPP_RENEW))) {
				$order['wxapp'] = $order['uniacid'];
				$order['uniacid'] = 0;
			}
			pdo_insert ('site_store_order', $order);
			$type_name = $this->getTypeName($goods_info['type']);
			$message_notice_log = array(
				'message' => $_W['user']['username'] . date("Y-m-d H:i:s") . '在商城购买了' . $type_name . ', 需支付金额' . $order['amount'] . '，处于未支付状态',
				'sign' => $orderid,
				'type' => MESSAGE_ORDER_TYPE,
				'create_time' => TIMESTAMP
			);
			pdo_insert('message_notice_log', $message_notice_log);
			$store_orderid = pdo_insertid();
			$pay_log = array(
				'type' => $_GPC['type'],
				'uniontid' => $orderid,
				'tid' => $store_orderid,
				'fee' => $order['amount'],
				'card_fee' => $order['amount'],
				'module' => 'store'
			);
			pdo_insert('core_paylog', $pay_log);
			iajax (0, $store_orderid);
		}

		if ($operate == 'pay_order') {
			$orderid = intval ($_GPC['orderid']);
			$order = pdo_get ('site_store_order', array ('id' => $orderid));
			$goods = pdo_get ('site_store_goods', array ('id' => $order['goodsid']));
			if (empty($order)) {
				itoast ('订单不存在', referer (), 'info');
			}
			if ($order['type'] != 1) {
				$message = $order['type'] == 2 ? '订单已删除.' : '订单已付款成功';
				itoast ($message, referer (), 'info');
			} else {
				if ($order['amount'] == 0) {
					pdo_update('site_store_order', array('type' => 3), array('id' => $order['id']));
					pdo_update('core_paylog', array('status' => 1), array('uniontid' => $order['orderid']));
					if (in_array($goods['type'], array(STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW))) {
						$account_type = $goods['type'] == STORE_TYPE_ACCOUNT_RENEW ? 'uniacid' : 'wxapp';
						$account_num = $goods['type'] == STORE_TYPE_ACCOUNT_RENEW ? $goods['account_num'] : $goods['wxapp_num'];
						$account_info = uni_fetch($order[$account_type]);
						$account_endtime = strtotime('+' . $order['duration'] * $account_num . $goods['unit'], max(TIMESTAMP, $account_info['endtime']));
						pdo_update('account', array('endtime' => $account_endtime), array('uniacid' => $order[$account_type]));
						cache_delete("uniaccount:{$order[$account_type]}");
					}
					cache_delete(cache_system_key($order['uniacid'] . ':site_store_buy_modules'));
					cache_build_account_modules($order['uniacid']);
					itoast('支付成功!', $this->createWebUrl('orders', array('direct' => 1)), 'success');
				}
			}
			$setting = setting_load ('store_pay');

			$core_paylog = pdo_get('core_paylog', array('module' => 'store', 'status' => 0, 'module' => 'store', 'uniontid' => $order['orderid'], 'tid' => $order['id']));
			if ($core_paylog['type'] == 'wechat') {
				$wechat_setting = $setting['store_pay']['wechat'];
				$params = array(
					'pay_way' => 'web',
					'title' => $goods['title'],
					'uniontid' => $order['orderid'],
					'fee' => $order['amount'],
					'goodsid' => $goods['id'],
				);
				$wechat_setting['version'] = 2;
				$wechat_result = wechat_build($params, $wechat_setting);
				if (is_error($wechat_result)) {
					itoast($wechat_result['message'], $this->createWebUrl('goodsBuyer', array('direct' => 1)), 'info');
				}
				file_delete('store_wechat_pay_' . $_W['uid'] . '.png');
				$picture_attach = 'store_wechat_pay_' . $_W['uid'] . '.png';
				$picture = $_W['siteroot'] . 'attachment/' . $picture_attach;
				QRcode::png($wechat_result['code_url'], ATTACHMENT_ROOT . $picture_attach);
				include $this->template('wechat_pay_qrcode');
			} elseif ($core_paylog['type'] == 'alipay') {
				$alipay_setting = $setting['store_pay']['alipay'];
				$alipay_params = array (
					'service' => 'create_direct_pay_by_user',
					'title' => $goods['title'],
					'fee' => $order['amount'],
					'uniontid' => $order['orderid'],
				);
				$alipay_result = alipay_build($alipay_params, $alipay_setting);
				header ('Location: ' . $alipay_result['url']);
			}
			exit();
		}
		include $this->template ('goodsbuyer');
	}

	public function leftMenu() {
		$this->storeIsOpen();
		$menu = array(
			'store_goods' => array(
				'title' => '商品分类',
				'menu' => array(
					'store_goods_module' =>array(
						'title' => '应用模块',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1, 'type' => STORE_TYPE_MODULE)),
						'icon' => 'wi wi-apply',
						'type' => STORE_TYPE_MODULE,
					),
					'store_goods_wxapp_module' => array(
						'title' => '小程序模块',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1, 'type' => STORE_TYPE_WXAPP_MODULE)),
						'icon' => 'wi wi-small-routine',
						'type' => STORE_TYPE_WXAPP_MODULE,
					),
					'store_goods_account' => array(
						'title' => '公众号个数',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1, 'type' => STORE_TYPE_ACCOUNT)),
						'icon' => 'wi wi-wechat',
						'type' => STORE_TYPE_ACCOUNT,
					),
					'store_goods_wxapp' => array(
						'title' => '小程序个数',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1,  'type' => STORE_TYPE_WXAPP)),
						'icon' => 'wi wi-wxapp',
						'type' => STORE_TYPE_WXAPP,
					),
					'store_goods_api' => array(
						'title' => '应用访问流量(API)',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1,  'type' => STORE_TYPE_API)),
						'icon' => 'wi wi-api',
						'type' => STORE_TYPE_API,
					),
					'store_goods_package' => array(
						'title' => '应用权限组',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1,  'type' => STORE_TYPE_PACKAGE)),
						'icon' => 'wi wi-appjurisdiction',
						'type' => STORE_TYPE_PACKAGE,
					),
					'store_goods_account_renew' => array(
						'title' => '公众号续费',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1,  'type' => STORE_TYPE_ACCOUNT_RENEW)),
						'icon' => 'wi wi-appjurisdiction',
						'type' => STORE_TYPE_ACCOUNT_RENEW,
					),
					'store_goods_wxapp_renew' => array(
						'title' => '小程序续费',
						'url' => $this->createWebUrl('goodsbuyer', array('direct' => 1,  'type' => STORE_TYPE_WXAPP_RENEW)),
						'icon' => 'wi wi-appjurisdiction',
						'type' => STORE_TYPE_WXAPP_RENEW,
					),
				),
			),
			'store_manage' => array(
				'title' => '商城管理',
				'founder' => true,
				'menu' => array(
					'store_manage_goods' => array(
						'title' => '添加商品',
						'url' => $this->createWebUrl('goodsSeller', array('direct' => 1)),
						'icon' => 'wi wi-goods-add',
						'type' => 'goodsSeller',
					),
					'store_manage_setting' => array(
						'title' => '商城设置',
						'url' => $this->createWebUrl('setting', array('direct' => 1)),
						'icon' => 'wi wi-store',
						'type' => 'setting',
					),
					'store_manage_payset' => array(
						'title' => '支付设置',
						'url' => $this->createWebUrl('paySetting', array('direct' => 1)),
						'icon' => 'wi wi-account',
						'type' => 'paySetting',
					),
				)
			),
			'store_orders' => array(
				'title' => '订单管理',
				'menu' => array(
					'store_orders_my' => array(
						'title' => '我的订单',
						'url' => $this->createWebUrl('orders', array('direct' => 1)),
						'icon' => 'wi wi-sale-record',
						'type' => 'orders',
					),
				),
			),
		);
		return $menu;
	}

	public function doWebPay() {
		$this->storeIsOpen();
		global $_GPC, $_W;
		$operate = $_GPC['operate'];
		$operates = array ('check_pay_result');
		$operate = in_array ($operate, $operates) ? $operate : 'check_pay_result';

		if ($operate == 'check_pay_result') {
			$orderid = intval($_GPC['orderid']);
			$pay_type = pdo_getcolumn('site_store_order', array('id' => $orderid), 'type');
			if ($pay_type == STORE_ORDER_FINISH) {
				iajax(1);
			} else {
				iajax(2);
			}
		}
	}

}