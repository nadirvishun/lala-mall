<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
function poster_trimPx($data) {
	$data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
	$data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
	$data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
	$data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
	$data['size'] = intval(str_replace('px', '', $data['size'])) * 2;
	$data['src'] = tomedia($data['src']);
	return $data;
}

function poster_mergeImage($target, $imgurl, $data) {
	$img = poster_createImage($imgurl);
	$w = imagesx($img);
	$h = imagesy($img);
	imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
	imagedestroy($img);
	return $target;
}

function poster_createImage($imgurl) {
	load()->func('communication');
	$resp = ihttp_request($imgurl);
	if(($resp['code'] == 200) && !empty($resp['content'])) {
		return imagecreatefromstring($resp['content']);
	}
	$i = 0;
	while($i < 3) {
		$resp = ihttp_request($imgurl);
		if(($resp['code'] == 200) && !empty($resp['content'])) {
			return imagecreatefromstring($resp['content']);
		}
		++$i;
	}
	return '';
}

function poster_mergeText($target, $text, $data) {
	$font = MODULE_ROOT . '/static/fonts/msyh.ttf';//字体文件
	$colors = poster_hex2rgb($data['color']);
	$color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
	imagettftext($target, $data['fontsize'], 0, $data['left'], $data['top'] + $data['size'], $color, $font, $text);
	return $target;
}

function poster_hex2rgb($colour) {
	if ($colour[0] == '#') {
		$colour = substr($colour, 1);
	}
	if (strlen($colour) == 6) {
		list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
	} elseif (strlen($colour) == 3) {
		list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
	} else {
		return false;
	}
	$r = hexdec($r);
	$g = hexdec($g);
	$b = hexdec($b);
	return array('red' => $r, 'green' => $g, 'blue' => $b);
}

function poster_create($poster){
	global $_W;
	$file = "/resource/poster/{$poster['plugin']}/{$_W['uniacid']}/iposter_{$poster['name']}.jpg";
	$qrcode = MODULE_ROOT . "{$file}";
	if(file_exists($qrcode)) {
		return "{$_W['siteroot']}addons/we7_wmall{$file}?t=" . time();
	}
	load()->func('file');
	mkdirs(dirname($qrcode));
	set_time_limit(0);
	@ini_set('memory_limit', '256M');
	$bg = tomedia($poster['config']['bg']);
	if(empty($bg)) {
		return error(-1, '背景图片不存在');
	}
	$size = getimagesize($bg);
	$target = imagecreatetruecolor($size[0], $size[1]);
	$bg = poster_createImage($bg);
	imagecopy($target, $bg, 0, 0, 0, 0,$size[0], $size[1]);
	imagedestroy($bg);

	$member = $poster['member'];
	$parts = $poster['config']['data']['items'];
	foreach($parts as $part) {
		$style = poster_trimPx($part['style']);
		if($part['id'] == 'qrcode'){
			$qrcode_url = $poster['config']['qrcode_url'];
			poster_mergeImage($target, $qrcode_url, $style);
		} elseif($part['id'] == 'image'){
			poster_mergeImage($target, tomedia($part['params']['imgurl']), $style);
		}  elseif($part['id'] == 'avatar'){
			poster_mergeImage($target, $member['avatar'], $style);
		} elseif($part['id'] == 'nickname'){
			poster_mergeText($target, $member['nickname'], $style);
		} elseif($part['id'] == 'text'){
			poster_mergeText($target, $part['params']['content'], $style);
		}
	}
	imagejpeg($target, $qrcode);
	imagedestroy($target);
	return "{$_W['siteroot']}addons/we7_wmall{$file}?t=" . time();
}

function poster_getQR($fans,$poster,$sid,$modulename){
	global $_W;
	$pid = $poster['id'];
	//看看是否已有记录
	$share = pdo_fetch('select * from '.tablename($modulename."_share")." where id='{$sid}'");
	if (!empty($share['url'])){
		$out = false;
		if ($poster['rtype']){//若是临时二维码 需要查看时间
			$qrcode = pdo_fetch('select * from '.tablename('qrcode')
				." where uniacid='{$_W['uniacid']}' and qrcid='{$share['sceneid']}' "
				." and ticket='{$share['ticketid']}' and url='{$share['url']}'");
			if($qrcode['createtime'] + $qrcode['expire'] < time()){//过期
				pdo_delete('qrcode',array('id'=>$qrcode['id']));
				$out = true;
			}
		}
		if (!$out){
			return $share['url'];
		}
	}
	$model = 2 - intval($poster['rtype']);
	//找出已经有的最大的场景id
	$sceneid = pdo_fetchcolumn('select qrcid from '.tablename("qrcode")." where uniacid='{$_W['uniacid']}' and model='{$model}' order by qrcid desc limit 1");
	if (empty($sceneid)) $sceneid = 20000;
	else $sceneid++;
	$barcode['action_info']['scene']['scene_id'] = $sceneid;

	load()->model('account');
	$acid = pdo_fetchcolumn('select acid from '.tablename('account')." where uniacid={$_W['uniacid']}");
	$uniacccount = WeAccount::create($acid);
	$time = 0;
	if ($poster['rtype']){//七天临时二维码
		$barcode['action_name'] = 'QR_SCENE';
		$barcode['expire_seconds'] = 30*24*3600;
		$res = $uniacccount->barCodeCreateDisposable($barcode);
		$time = $barcode['expire_seconds'];
	}else{
		$barcode['action_name'] = 'QR_LIMIT_SCENE';
		$res = $uniacccount->barCodeCreateFixed($barcode);
	}
	//将二维码存于微擎官方二维码表
	pdo_insert('qrcode',
		array('uniacid'=>$_W['uniacid'],'acid'=>$acid,'qrcid'=>$sceneid,'name'=>$poster['title'],'keyword'=>$poster['kword']
		,'model'=>$model,'ticket'=>$res['ticket'],'expire'=>$time,'createtime'=>time(),'status'=>1,'url'=>$res['url']
		)
	);

	pdo_update($modulename."_share",array('sceneid'=>$sceneid,'ticketid'=>$res['ticket'],'url'=>$res['url']),array('id'=>$sid));
	return $res['url'];
}


