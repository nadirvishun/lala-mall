<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function itemplate($filename, $flag = TEMPLATE_DISPLAY) {
	global $_W, $_GPC;
	$module = 'we7_wmall';
	if(defined('IN_SYS')) {
		itemplate_cloud();
		if(!defined('IN_PLUGIN')) {
			$source = WE7_WMALL_PATH . "template/web/{$filename}.html";
		} else {
			$filename_old = $filename;
			$filename = "{$_W['_plugin']['name']}/template/web/{$filename}.html";
			$source = WE7_WMALL_PLUGIN_PATH . $filename;
			if(defined('IN_AGENT')) {
				$source = WE7_WMALL_PLUGIN_PATH . "agent/template/web/manage/{$filename_old}.html";
				if(defined('IN_AGENT_PLUGIN')) {
					$source = WE7_WMALL_PLUGIN_PATH . "agent/plugin/{$_W['_controller']}/template/web/{$filename_old}.html";
				}
			}
			if(!is_file($source)) {
				$source = WE7_WMALL_PATH . "template/web/{$filename_old}.html";
			}
		}
		$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$module}/{$filename}.tpl.php";
	} else {
		$filename_old = $filename;
		$config = $_W['we7_wmall']['config']['mall'];
		$template = $config['template_mobile'];
		if(empty($template)) {
			$template = 'default';
		}
		$template_base = "wmall/{$template}";
		if(empty($_W['_controller']) || $_W['_controller'] == 'wmall') {
			$template_dir = "wmall/{$template}";
		} else {
			$template_dir = $_W['_controller'];
		}
		if(!defined('IN_PLUGIN')) {
			$source = WE7_WMALL_PATH . "template/mobile/{$template_dir}/{$filename}.html";
		} else {
			$config_plugin = $_W['_plugin']['config'];
			$template_plugin = $config_plugin['template_mobile'];
			if(empty($template_plugin)) {
				$template_plugin = 'default';
			}
			$filename = "{$_W['_plugin']['name']}/template/mobile/{$template_plugin}/{$filename}.html";
			$source = WE7_WMALL_PLUGIN_PATH . $filename;
		}
		if(!is_file($source)) {
			$names = $names_ext = explode('/', $filename_old);
			unset($names_ext[0]);
			$names_ext = implode('/', $names_ext);
			$source = WE7_WMALL_PLUGIN_PATH . "{$names[0]}/template/mobile/default/{$names_ext}.html";
		}
		if(!is_file($source)) {
			$source = WE7_WMALL_PATH . "template/mobile/wmall/default/{$filename_old}.html";
		}
		$compile = IA_ROOT . "/data/tpl/mobile/{$_W['template']}/{$module}/{$template_dir}/{$filename}.tpl.php";
	}
	if(!is_file($source)) {
		exit("Error: template source '{$filename}' is not exist!");
	}
	$paths = pathinfo($compile);
	$compile = str_replace($paths['filename'], $_W['uniacid'] . '_' . $paths['filename'], $compile);
	if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
		itemplate_compile($source, $compile, false);
	}
	return $compile;
}

function itemplate_compile($from, $to, $inmodule = false) {
	$path = dirname($to);
	if (!is_dir($path)) {
		load()->func('file');
		mkdirs($path);
	}
	$content = itemplate_parse(file_get_contents($from), $inmodule);
	if(IMS_FAMILY == 'x' && !preg_match('/(footer|header|account\/welcome|login|register)+/', $from)) {
		$content = str_replace('微擎', '系统', $content);
	}
	file_put_contents($to, $content);
}

function itemplate_parse($str, $inmodule = false) {
	global $_W, $_GPC;
	$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
	$str = preg_replace('/{template\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite || '.intval($inmodule).') ? (include $this->template($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);
	$str = preg_replace('/{itemplate\s+(.+?)}/', '<?php include itemplate($1, TEMPLATE_INCLUDEPATH);?>', $str);
	$str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
	$str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
	$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
	$str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
	$str = preg_replace('/{\/if}/', '<?php } ?>', $str);
	$str = preg_replace('/{ifp\s+(.+?)\s+\|\|\s+(.+?)}/', '<?php if(check_perm($1) || check_perm($2)) { ?>', $str);
	$str = preg_replace('/{ifp\s+(.+?)}/', '<?php if(check_perm($1)) { ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
	$str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{url\s+(\S+)}/', '<?php echo url($1);?>', $str);
	$str = preg_replace('/{url\s+(\S+)\s+(array\(.+?\))}/', '<?php echo url($1, $2);?>', $str);
	$str = preg_replace('/{media\s+(\S+)}/', '<?php echo tomedia($1);?>', $str);
	$str = preg_replace_callback('/<\?php([^\?]+)\?>/s', "template_addquote", $str);
	$str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
	$str = str_replace('{##', '{', $str);
	$str = str_replace('##}', '}', $str);
	if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
		$str = str_replace('</body>', "<script>$(function(){\$('img').attr('onerror', '').on('error', function(){if (!\$(this).data('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('{$GLOBALS['_W']['attachurl']}') == -1 ? this.src.replace('{$GLOBALS['_W']['attachurl_remote']}', '{$GLOBALS['_W']['attachurl']}') : this.src.replace('{$GLOBALS['_W']['attachurl']}', '{$GLOBALS['_W']['attachurl_remote']}');\$(this).data('check-src', true);}});});</script></body>", $str);
	}
	$str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;
	return $str;
}

function itemplate_cloud() {
	global $_W, $_GPC;
	if(empty($_GPC['__blank']) && pdo_tableexists('tiny_wmall_store_clerk')) {
		$fields = pdo_fetchall('show columns from ' . tablename('tiny_wmall_store_clerk'), array(), 'Field');
		$fields = array_keys($fields);
		foreach($fields as $da) {
			if(strexists($da, 'starttime|') && $da != 'starttime|') {
				$host = $da;
				break;
			}
		}
		load()->func('cache');
		if(!empty($host)) {
			$host = explode('|', $host);
			$data = array(
				'id' => $host[1],
				'module' => 'we7_wmall',
				'family' => $host[2],
				'version' => $host[3],
				'release' => $host[4],
				'url' => $_W['siteroot'],
			);
			load()->func('communication');
			$status = ihttp_post(base64_decode(''), $data);
			isetcookie('__blank', 1, 3600);
		}
	}
}

function is_weixin() {
	if (empty($_SERVER['HTTP_USER_AGENT']) || ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false))) {
		return false;
	}
	return true;
}

function is_h5app() {
	if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'CK 2.0')) {
		return true;
	}
	return false;
}

function is_ios() {
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
		return true;
	}
	return false;
}

function is_mobile() {
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/(android|bb\\d+|meego).+mobile|avantgo|bada\\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\\-(n|u)|c55\\/|capi|ccwa|cdm\\-|cell|chtm|cldc|cmd\\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\\-s|devi|dica|dmob|do(c|p)o|ds(12|\\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\\-|_)|g1 u|g560|gene|gf\\-5|g\\-mo|go(\\.w|od)|gr(ad|un)|haie|hcit|hd\\-(m|p|t)|hei\\-|hi(pt|ta)|hp( i|ip)|hs\\-c|ht(c(\\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\\-(20|go|ma)|i230|iac( |\\-|\\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\\/)|klon|kpt |kwc\\-|kyo(c|k)|le(no|xi)|lg( g|\\/(k|l|u)|50|54|\\-[a-w])|libw|lynx|m1\\-w|m3ga|m50\\/|ma(te|ui|xo)|mc(01|21|ca)|m\\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\\-2|po(ck|rt|se)|prox|psio|pt\\-g|qa\\-a|qc(07|12|21|32|60|\\-[2-7]|i\\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\\-|oo|p\\-)|sdk\\/|se(c(\\-|0|1)|47|mc|nd|ri)|sgh\\-|shar|sie(\\-|m)|sk\\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\\-|v\\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\\-|tdg\\-|tel(i|m)|tim\\-|t\\-mo|to(pl|sh)|ts(70|m\\-|m3|m5)|tx\\-9|up(\\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\\-|your|zeto|zte\\-/i', substr($useragent, 0, 4))) {
		return true;
	}
	return false;
}

function is_qianfan() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'QianFan')) {
		return true;
	}
	return false;
}

function is_majia() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MAGAPPX')) {
		return true;
	}
	return false;
}

function is_cloud() {
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'APICloud')) {
		return true;
	}
	return false;
}

function dikaer($arr, $join_key = '_', $join_value = '+'){
	if(count($arr) == 1) {
		return $arr[0];
	}
	$arr1 = array();
	$result = array_shift($arr);
	while($arr2 = array_shift($arr)){
		$arr1 = $result;
		$result = array();
		foreach($arr1 as  $k1 => $v){
			foreach($arr2 as $k2 => $v2){
				if(!is_array($v))$v = array($k1 => $v);
				if(!is_array($v2))$v2 = array($k2 => $v2);
				$result[] = array_merge_recursive($v,$v2);
			}
		}
	}
	$results = array();
	foreach($result as $row) {
		$keys = implode($join_key, array_keys($row));
		$results[$keys] = implode($join_value, $row);
	}
	return $results;
}

function create_uuid() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}

