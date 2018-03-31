<?php
defined('IN_IA') || exit('Access Denied');
class WxPay 
{
	protected $wxpay;
	protected $cert;
	public function __construct($pay_type = '') 
	{
		global $_W;
		$wechat = $_W['we7_wmall']['config']['payment']['wechat'];
		$wechat = $wechat[$wechat['type']];
		if ($pay_type == 'app') 
		{
			$wechat = $_W['we7_wmall']['config']['payment']['app_wechat'];
		}
		$this->wxpay = array('appid' => $wechat['appid'], 'mch_id' => $wechat['mchid'], 'sub_mch_id' => $wechat['sub_mch_id'], 'key' => $wechat['apikey']);
		$this->cert = array('apiclient_cert' => $wechat['apiclient_cert'], 'apiclient_key' => $wechat['apiclient_key'], 'rootca' => $wechat['rootca']);
	}
	public function array2url($params, $force = false) 
	{
		$str = '';
		foreach ($params as $key => $val ) 
		{
			if ($force && empty($val)) 
			{
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
		$string = $string . '&key=' . $this->wxpay['key'];
		$string = md5($string);
		$result = strtoupper($string);
		return $result;
	}
	public function parseResult($result, $is_check_sign = false) 
	{
		if (substr($result, 0, 5) != '<xml>') 
		{
			return $result;
		}
		$result = json_decode(json_encode(isimplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		if (!(is_array($result))) 
		{
			return error(-1, 'xml结构错误');
		}
		if (isset($result['return_code']) && ($result['return_code'] != 'SUCCESS')) 
		{
			$msg = ((empty($result['return_msg']) ? $result['err_code_des'] : $result['return_msg']));
			return error(-1, $msg);
		}
		if ($is_check_sign && ($this->bulidsign($result) != $result['sign'])) 
		{
			return error(-1, '验证签名出错');
		}
		return $result;
	}
	public function httpWxurl($url, $params, $extra = array()) 
	{
		load()->func('communication');
		$xml = array2xml($params);
		$response = ihttp_request($url, $xml, $extra);
		if (is_error($response)) 
		{
			return $response;
		}
		$result = $this->parseResult($response['content']);
		return $result;
	}
	public function shortUrl($url) 
	{
		$params = array('appid' => $this->wxpay['appid'], 'mch_id' => $this->wxpay['mch_id'], 'long_url' => $url, 'nonce_str' => random(32));
		$params['sign'] = $this->bulidSign($params);
		$result = $this->httpWxurl('https://api.mch.weixin.qq.com/tools/shorturl', $params);
		if (is_error($result)) 
		{
			return $result;
		}
		return $result['short_url'];
	}
	public function checkCert() 
	{
		global $_W;
		if (empty($this->cert['apiclient_key']) || empty($this->cert['apiclient_cert']) || empty($this->cert['rootca'])) 
		{
			return error(-1, '支付证书不完整');
		}
		return true;
	}
	public function mktTransfers($params, $check_type = 'FORCE_CHECK') 
	{
		global $_W;
		$status = $this->checkCert();
		if (is_error($status)) 
		{
			return $status;
		}
		$elements = array('openid', 'amount', 'partner_trade_no', 're_user_name', 'desc');
		$params = array_elements($elements, $params);
		if (empty($params['openid'])) 
		{
			return error(-1, '粉丝信息错误');
		}
		if ((($check_type == 'FORCE_CHECK') || ($check_type == 'OPTION_CHECK')) && empty($params['re_user_name'])) 
		{
			return error(-1, '收款人真实姓名不能为空');
		}
		if (empty($params['amount'])) 
		{
			return error(-1, '打款金额不能为空');
		}
		if (empty($params['partner_trade_no'])) 
		{
			return error(-1, '商户订单号不能为空');
		}
		if (empty($params['desc'])) 
		{
			return error(-1, '付款描述信息不能为空');
		}
		$params['check_name'] = $check_type;
		$params['mch_appid'] = $this->wxpay['appid'];
		$params['mchid'] = $this->wxpay['mch_id'];
		$params['nonce_str'] = random(32);
		$params['spbill_create_ip'] = CLIENT_IP;
		$params['sign'] = $this->bulidSign($params);
		$extra = array(CURLOPT_SSLCERT => MODULE_ROOT . '/cert/' . $this->cert['apiclient_cert'] . '/apiclient_cert.pem', CURLOPT_SSLKEY => MODULE_ROOT . '/cert/' . $this->cert['apiclient_key'] . '/apiclient_key.pem', CURLOPT_CAINFO => MODULE_ROOT . '/cert/' . $this->cert['rootca'] . '/rootca.pem');
		$result = $this->httpWxurl('https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers', $params, $extra);
		if (is_error($result)) 
		{
			return $result;
		}
		if ($result['result_code'] != 'SUCCESS') 
		{
			return error(-1, $result['err_code'] . '：' . $result['err_code_des']);
		}
		return true;
	}
	public function payRefund_build($params) 
	{
		global $_W;
		$status = $this->checkCert();
		if (is_error($status)) 
		{
			return $status;
		}
		$elements = array('total_fee', 'refund_fee', 'out_trade_no', 'out_refund_no');
		$params = array_elements($elements, $params);
		if (empty($params['total_fee'])) 
		{
			return error(-1, '订单总金额不能为空');
		}
		if (empty($params['refund_fee'])) 
		{
			return error(-1, '退款金额不能为空');
		}
		if (empty($params['out_trade_no'])) 
		{
			return error(-1, '商户订单号不能为空');
		}
		if (empty($params['out_refund_no'])) 
		{
			return error(-1, '商户退款单号不能为空');
		}
		$params['appid'] = $this->wxpay['appid'];
		$params['mch_id'] = $this->wxpay['mch_id'];
		$params['sub_mch_id'] = $this->wxpay['sub_mch_id'];
		$params['op_user_id'] = $this->wxpay['mch_id'];
		$params['nonce_str'] = random(32);
		$params['sign'] = $this->bulidSign($params);
		$extra = array(CURLOPT_SSLCERT => MODULE_ROOT . '/cert/' . $this->cert['apiclient_cert'] . '/apiclient_cert.pem', CURLOPT_SSLKEY => MODULE_ROOT . '/cert/' . $this->cert['apiclient_key'] . '/apiclient_key.pem', CURLOPT_CAINFO => MODULE_ROOT . '/cert/' . $this->cert['rootca'] . '/rootca.pem');
		$result = $this->httpWxurl('https://api.mch.weixin.qq.com/secapi/pay/refund', $params, $extra);
		if (is_error($result)) 
		{
			return error(-1, '发起退款申请失败.' . $result['message']);
		}
		if ($result['result_code'] != 'SUCCESS') 
		{
			return error(-10, '发起退款申请失败.' . $result['err_code'] . '：' . $result['err_code_des']);
		}
		return $result;
	}
	public function payRefund_query($params) 
	{
		$elements = array('out_refund_no');
		$params = array_elements($elements, $params);
		if (empty($params['out_refund_no'])) 
		{
			return error(-1, '商户退款单号不能为空');
		}
		$params['appid'] = $this->wxpay['appid'];
		$params['mch_id'] = $this->wxpay['mch_id'];
		$params['sub_mch_id'] = $this->wxpay['sub_mch_id'];
		$params['nonce_str'] = random(32);
		$params['sign'] = $this->bulidSign($params);
		$result = $this->httpWxurl('https://api.mch.weixin.qq.com/pay/refundquery', $params);
		if (is_error($result)) 
		{
			return error(-1, '查询微信退款进度失败.' . $result['message']);
		}
		if ($result['result_code'] != 'SUCCESS') 
		{
			return error(-1, '查询微信退款进度失败.' . $result['err_code'] . '：' . $result['err_code_des']);
		}
		return $result;
	}
	public function payRefund_status() 
	{
		$wechat_status = array( 'SUCCESS' => array('text' => '成功', 'value' => 3), 'FAIL' => array('text' => '失败', 'value' => 4), 'PROCESSING' => array('text' => '处理中', 'value' => 2), 'NOTSURE' => array('text' => '未确定，需要商户原退款单号重新发起', 'value' => 5) );
		return $wechat_status;
	}
}
?>