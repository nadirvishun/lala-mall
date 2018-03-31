<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';

if($ta == 'index') {
	$_W['page']['title'] = '热门商品';

	$condition = ' WHERE uniacid = :uniacid AND sid = :sid and order_plateform = :order_plateform';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':order_plateform' => 'we7_wmall',
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 5;
	if($status > 0) {
		$condition .= ' and status = :status';
		$params['status'] = $status;
	}
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}
	$orderby = trim($_GPC['orderby']) ? trim($_GPC['orderby']) : 'total_goods_price';
	$stat = array();
	$stat['total_goods_num'] = intval(pdo_fetchcolumn('select sum(goods_num) from ' . tablename('tiny_wmall_order_stat') . $condition, $params));
	$stat['total_goods_price'] = floatval(pdo_fetchcolumn('select round(sum(goods_price), 2) from ' . tablename('tiny_wmall_order_stat') . $condition, $params));
	$records = pdo_fetchall('select stat_day, goods_id, goods_title, sum(goods_price) as total_goods_price, sum(goods_num) as total_goods_num from ' . tablename('tiny_wmall_order_stat') . $condition . " group by goods_id order by {$orderby} desc", $params);
	$chart = array(
		'field' => array('goods_num', 'goods_price'),
		'title' => array(),
		'data' => array()
	);
	if(!empty($records)) {
		$oids = pdo_fetchall('select oid from ' . tablename('tiny_wmall_order_stat') . $condition, $params, 'oid');
		$oid_str = implode(',', array_keys($oids));
		foreach($records as &$row) {
			$row['pre_goods_price'] = round($row['total_goods_price'] / $stat['total_goods_price'], 2) * 100 . '%';
			$row['pre_goods_num'] = round($row['total_goods_num'] / $stat['total_goods_num'], 2) * 100 . '%';
			$row['goods'] = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $row['goods_id']), array('title', 'number', 'is_options', 'unitname'));
			$row['goods_unitname'] = $row['goods']['unitname'];
			$row['goods_title'] = $row['goods']['title'];
			$row['goods_number'] = $row['goods']['number'];
			$row['goods_is_options'] = $row['goods']['is_options'];
			if($row['goods_is_options'] == 1) {
				$row['options'] = pdo_fetchall('select sum(a.goods_num) as option_goods_num, a.option_id, b.name from ' . tablename('tiny_wmall_order_stat') . ' as a left join ' . tablename('tiny_wmall_goods_options') . " as b on a.option_id = b.id where a.uniacid = :uniacid and a.goods_id = :goods_id and a.oid in({$oid_str}) group by a.option_id", array(':uniacid' => $_W['uniacid'], ':goods_id' => $row['goods_id']), 'option_id');
			}
			$chart['title'][] = $row['goods_title'];
			$chart['data']['goods_price'][] = array(
				'name' => $row['goods_title'],
				'value' => $row['total_goods_price']
			);
			$chart['data']['goods_num'][] = array(
				'name' => $row['goods_title'],
				'value' => $row['total_goods_num']
			);
		}
	}
	if($_W['isajax']) {
		imessage(error(0, $chart), '', 'ajax');
	}
	if($_GPC['r'] == 'export') {
		$stat_fields = array(
			'goods_title' => array(
				'field' => 'goods_title',
				'title' => '商品名称',
				'width' => '30',
			),
			'goods_number' => array(
				'field' => 'goods_number',
				'title' => '商品编号',
				'width' => '20',
			),
			'option' => array(
				'field' => 'option',
				'title' => '规格',
				'width' => '20',
			),
			'total_goods_num' => array(
				'field' => 'total_goods_num',
				'title' => '销售量',
				'width' => '30',
			),
			'total_goods_price' => array(
				'field' => 'total_goods_price',
				'title' => '销售额',
				'width' => '20',
			),
			'pre_goods_price' => array(
				'field' => 'pre_goods_price',
				'title' => '销售额百分比',
				'width' => '20',
			),
			'pre_goods_num' => array(
				'field' => 'pre_goods_num',
				'title' => '销售量百分比',
				'width' => '20',
			),
		);
		$ABC = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
		$i = 0;
		foreach($stat_fields as $key => $val) {
			$all_fields[$ABC[$i]] = $val;
			$i++;
		}
		include_once(IA_ROOT . '/framework/library/phpexcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		foreach($all_fields as $key => $li) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($li['width']);
			$objPHPExcel->getActiveSheet()->getStyle($key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($key . '1', $li['title']);
		}
		$i = 2;
		if(!empty($records)) {
			foreach($records as $val) {
				$val['total_goods_num'] = "{$val['total_goods_num']}{$val['goods_unitname']}";
				foreach($all_fields as $key => $li) {
					$objPHPExcel->getActiveSheet(0)->setCellValue($key . $i, $val[$li['field']]);
				}
				$i++;
				if(!empty($val['options'])) {
					foreach($val['options'] as $option) {
						$temp = array(
							'goods_title' => '',
							'goods_number' => '',
							'option' => $option['name'],
							'total_goods_num' => "{$option['option_goods_num']}{$val['goods_unitname']}",
						);
						foreach($all_fields as $key => $li) {
							$objPHPExcel->getActiveSheet(0)->setCellValue($key . $i, $temp[$li['field']]);
						}
						$i++;
					}
				}
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle('商品数据统计');
		$objPHPExcel->setActiveSheetIndex(0);

		// 输出
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header('Content-Disposition: attachment;filename="商品数据统计.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit();
	}

}
include itemplate('store/statistic/goods');
