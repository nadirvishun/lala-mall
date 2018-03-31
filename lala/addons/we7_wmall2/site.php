<?php
//dezend by http://www.yunlu99.com/ QQ:270656184
defined('IN_IA') || exit('Access Denied');
include IA_ROOT . '/addons/we7_wmall/version.php';
include 'defines.php';
include 'model.php';
class We7_wmallModuleSite extends WeModuleSite
{
	private $cache = array();

	public function __construct()
	{
	}

	public function doWebWeb()
	{
		$this->router();
	}

	public function doMobileMobile()
	{
		$this->router();
	}

	public function router()
	{
		$bootstrap = WE7_WMALL_PATH . 'inc/__init.php';
		require $bootstrap;
		exit();
	}

	public function __call($name, $arguments)
	{
		global $_W;
		$isWeb = stripos($name, 'doWeb') === 0;
		$isMobile = stripos($name, 'doMobile') === 0;
		$isApi = stripos($name, 'doApi') === 0;
		if ($isWeb || $isMobile) {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';

			if ($isWeb) {
				require $dir . 'web/__init.php';
				$do = strtolower(substr($name, 5));
				$sys = substr($do, 0, 3);

				if ($sys == 'ptf') {
					$do = substr($do, 3);
					$dir .= 'web/plateform/';
				}
				else if ($sys == 'cmn') {
					$do = substr($do, 3);
					$dir .= 'web/common/';
				}
				else {
					$dir .= 'web/store/';
				}

				$fun = $do;
			}
			else {
				require $dir . 'mobile/__init.php';
				$do = strtolower(substr($name, 8));
				$sys = substr($do, 0, 3);

				if ($sys == 'cmn') {
					$do = substr($do, 3);
					$dir .= 'mobile/common/';
				}
				else {
					$sys = substr($do, 0, 2);

					if ($sys == 'mg') {
						$do = substr($do, 2);
						$dir .= 'mobile/manage/';
						require $dir . 'bootstrap.inc.php';
					}
					else if ($sys == 'dy') {
						$do = substr($do, 2);
						$dir .= 'mobile/delivery/';
						require $dir . 'bootstrap.inc.php';
					}
					else {
						$dir .= 'mobile/store/';
						$routers = array('goods' => imurl('wmall/store/goods', array('sid' => $_GET['sid'])), 'store' => imurl('wmall/store/index', array('sid' => $_GET['sid'])));

						if (in_array($do, array_keys($routers))) {
							header('location: ' . $routers[$do]);
							exit();
						}
					}
				}

				$fun = $do;
			}

			$file = $dir . $fun . '.inc.php';

			if (file_exists($file)) {
				require $file;
				exit();
			}
		}
		else {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';
			require $dir . 'api/__init.php';
			$do = strtolower(substr($name, 5));
			$sys = substr($do, 0, 2);

			if ($sys == 'mg') {
				$do = substr($do, 2);
				$dir .= 'api/manage/';
				require $dir . 'bootstrap.inc.php';
			}
			else if ($sys == 'dy') {
				$do = substr($do, 2);
				$dir .= 'api/delivery/';
				require $dir . 'bootstrap.inc.php';
			}
			else if ($sys == 'cm') {
				$do = substr($do, 3);
				$dir .= 'api/common/';
				require $dir . 'bootstrap.inc.php';
			}
			else {
				$dir .= 'api/store/';
				require $dir . 'bootstrap.inc.php';
			}

			$fun = $do;
			$file = $dir . $fun . '.inc.php';

			if (file_exists($file)) {
				require $file;
				exit();
			}
		}

		trigger_error('访问的方法 ' . $name . ' 不存在.', 512);
	}

	public function payResult($params)
	{
		global $_W;
		$_W['siteroot'] = str_replace(array('/addons/we7_wmall', '/payment/qianfan', '/payment/majia'), array('', '', ''), $_W['siteroot']);
		$_W['uniacid'] = $params['uniacid'];
		$record = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'order_sn' => $params['tid']));
		$_W['agentid'] = $record['agentid'];
		$config = get_system_config();
		$_W['we7_wmall']['config'] = $config;
		if ($params['result'] == 'success' && $params['from'] == 'notify' || $params['from'] == 'return' && in_array($params['type'], array('delivery'))) {
			mload()->model('order');
			mload()->model('store');

			if (!empty($record)) {
				$record['data'] = iunserializer($record['data']);
				$params['prepay_id'] = $record['data']['prepay_id'];
				pdo_update('tiny_wmall_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('id' => $record['id']));
			}

			if ($record['order_type'] == 'peerpay') {
				$order = pdo_get('tiny_wmall_order_peerpay_payinfo', array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));

				if (!empty($order)) {
					if (!$order['is_pay']) {
						pdo_update('tiny_wmall_order_peerpay_payinfo', array('is_pay' => 1, 'paytime' => TIMESTAMP), array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));
						$peerpay = pdo_get('tiny_wmall_order_peerpay', array('id' => $order['pid']));

						if (!empty($peerpay)) {
							$update = array('peerpay_realprice' => round($peerpay['peerpay_realprice'] - $order['final_fee'], 2));

							if ($update['peerpay_realprice'] <= 0) {
								$update['status'] = 1;
							}

							pdo_update('tiny_wmall_order_peerpay', $update, array('id' => $peerpay['id']));

							if ($update['status'] == 1) {
								$record = pdo_get('tiny_wmall_paylog', array('uniacid' => $_W['uniacid'], 'id' => $peerpay['plid']));
								$params = array('channel' => 'wap', 'type' => 'peerpay', 'card_fee' => $record['fee'], 'is_pay' => 1, 'paytime' => TIMESTAMP, 'out_trade_no' => '', 'transaction_id' => '');
							}
						}
					}
				}
			}

			if ($record['order_type'] == 'takeout') {
				order_system_status_update($record['order_id'], 'pay', $params);
			}
			else if ($record['order_type'] == 'deliveryCard') {
				include WE7_WMALL_PLUGIN_PATH . 'deliveryCard/model.php';
				card_setmeal_buy($record['order_id']);
			}
			else if ($record['order_type'] == 'paybill') {
				mload()->model('order');
				mload()->model('paybill');
				paybill_order_status_update($record['order_id'], 'pay', $params);
			}
			else if ($record['order_type'] == 'errander') {
				$_W['_plugin']['config'] = get_plugin_config('errander');
				include WE7_WMALL_PLUGIN_PATH . 'errander/model.php';
				$order = pdo_get('tiny_wmall_errander_order', array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));
				if (!empty($order) && !$order['is_pay']) {
					$data = array('order_channel' => $params['channel'], 'pay_type' => $params['type'], 'final_fee' => $params['card_fee'], 'is_pay' => 1, 'paytime' => TIMESTAMP, 'out_trade_no' => $params['uniontid'], 'transaction_id' => $params['transaction_id']);
					pdo_update('tiny_wmall_errander_order', $data, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
					errander_order_status_update($order['id'], 'pay');
					errander_order_status_update($order['id'], 'dispatch');
				}
			}
			else if ($record['order_type'] == 'recharge') {
				mload()->model('member');
				member_recharge_status_update($record['order_id'], 'pay', $params);
			}
			else if ($record['order_type'] == 'freelunch') {
				include WE7_WMALL_PLUGIN_PATH . 'freeLunch/model.php';
				freelunch_partaker_status_update($record['order_id'], 'pay');
			}
			else {
				if ($record['order_type'] == 'advertise') {
					include WE7_WMALL_PLUGIN_PATH . 'advertise/model.php';
					advertise_trade_update($record['order_id']);
				}
			}
		}

		if ($params['from'] == 'return') {
			if ($record['order_type'] == 'takeout') {
				$url = imurl('wmall/order/index/detail', array('id' => $record['order_id']), true);
			}
			else if ($record['order_type'] == 'deliveryCard') {
				$url = imurl('deliveryCard/index', array(), true);
			}
			else if ($record['order_type'] == 'recharge') {
				$url = imurl('wmall/member/mine', array(), true);
			}
			else if ($record['order_type'] == 'freelunch') {
				$url = imurl('freeLunch/freeLunch/partake_success', array(), true);
			}
			else if ($record['order_type'] == 'errander') {
				$url = imurl('errander/order/detail', array('id' => $record['order_id']), true);
			}
			else if ($record['order_type'] == 'peerpay') {
				$url = imurl('system/paycenter/peerpay/paylist', array('payinfo_id' => $record['order_id']), true);
			}
			else if ($record['order_type'] == 'paybill') {
				$url = imurl('wmall/member/mine', array(), true);
			}
			else {
				if ($record['order_type'] == 'advertise') {
					$url = imurl('manage/advertise/list', array(), true);
				}
			}

			if ($params['type'] == 'credit') {
				message('下单成功', $url, 'success');
				return NULL;
			}

			header('location:' . $url);
			exit();
		}
	}
}

?>
