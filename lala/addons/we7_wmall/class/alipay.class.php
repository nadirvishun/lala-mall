<?php
//微擎应用 http://www.we7.cc   
class AliPay
{
	public $alipay;

	public function __construct($pay_type = 'wap')
	{
		global $_W;
		$alipay = $_W['we7_wmall']['config']['payment']['alipay'];

		if ($pay_type == 'app') {
			$alipay = $_W['we7_wmall']['config']['payment']['app_alipay'];
		}

		$this->alipay = array('app_id' => $alipay['appid']);
		$this->cert = array('private_key' => $alipay['private_key'], 'public_key' => $alipay['public_key']);
	}

	public function array2url($params, $force = false)
	{
		$str = '';

		foreach ($params as $key => $val) {
			if ($force && empty($val)) {
				continue;
			}

			$str .= $key . '=' . $val . '&';
		}

		$str = trim($str, '&');
		return $str;
	}

	public function bulidSign($params)
	{
		unset($params['sign']);
		ksort($params);
		$string = $this->array2url($params, true);
		$priKey = file_get_contents(MODULE_ROOT . '/cert/' . $this->cert['private_key'] . '/private_key.pem');
		$res = openssl_get_privatekey($priKey);
		openssl_sign($string, $sign, $res);
		openssl_free_key($res);
		$sign = base64_encode($sign);
		return $sign;
	}

	public function checkCert()
	{
		global $_W;
		if (empty($this->cert['private_key']) || empty($this->cert['public_key'])) {
			return error(-1, '支付宝支付证书不完整');
		}

		return true;
	}

	public function payRefund_build($params)
	{
		global $_W;
		$status = $this->checkCert();

		if (is_error($status)) {
			return $status;
		}

		$elements = array('refund_fee', 'out_trade_no', 'out_refund_no', 'refund_reason');
		$params = array_elements($elements, $params);

		if (empty($params['refund_fee'])) {
			return error(-1, '退款金额不能为空');
		}

		if (empty($params['out_trade_no'])) {
			return error(-1, '商户订单号不能为空');
		}

		$set = array();
		$set['app_id'] = $this->alipay['app_id'];
		$set['method'] = 'alipay.trade.refund';
		$set['charset'] = 'utf8';
		$set['sign_type'] = 'RSA';
		$set['timestamp'] = date('Y-m-d H:i:s');
		$set['version'] = '1.0';
		$other = array('out_trade_no' => $params['out_trade_no'], 'refund_amount' => $params['refund_fee'], 'refund_reason' => $params['refund_reason'] ? $params['refund_reason'] : '正常退款');
		$set['biz_content'] = json_encode($other);
		$set['sign'] = $this->bulidSign($set);
		load()->func('communication');
		$result = ihttp_post('https://openapi.alipay.com/gateway.do', $set);

		if (is_error($result)) {
			return $result;
		}

		$result['content'] = iconv('GBK', 'UTF-8//IGNORE', $result['content']);
		$result = json_decode($result['content'], true);

		if (!is_array($result)) {
			return error(-1, '返回数据错误');
		}

		if ($result['alipay_trade_refund_response']['code'] != 10000) {
			return error(-1, $result['alipay_trade_refund_response']['sub_msg']);
		}

		return $result['alipay_trade_refund_response'];
	}
}

defined('IN_IA') || exit('Access Denied');

?>
