define(['tiny', 'map', 'laytpl', 'jquery.range'], function(tiny, AMap, laytpl) {
	var trade = {};
	trade.init = function(params) {
		var order = {
			start_address: {},
			end_address: {},
			delivery_fee: 0
		};
		var lng = parseFloat(params.config.map.location_y);
		var lat = parseFloat(params.config.map.location_x);

		$(document).on('click', '.goods-label span', function(){
			var title = $('#goods-title').val();
			var $this = $(this);
			$this.addClass('active');
			$('#goods-title').val(title + ' ' + $this.html());
			setTimeout(function(){
				$this.removeClass('active');
			}, 250);
		});

		if(params.end_address) {
			order.end_address = params.end_address;
			countOrder();
		}

		if(params.start_address) {
			order.start_address = params.start_address;
			countOrder();
		}

		var address = {};
		$('#serach-key').on('keyup', function(){
			var key = $.trim($(this).val());
			if(!key) {
				$('#search-result').addClass('hide');
				$('#history-address').removeClass('hide');
				return false;
			}
			$.post(tiny.getUrl('errander/address/suggestion'), {key: key}, function(data){
				var result = $.parseJSON(data);
				if(result.message.error != -1) {
					var data = result.message.message;
					var gettpl = $('#tpl-address').html();
					laytpl(gettpl).render(data, function(html){
						$('#search-result ul').html(html);
						$('#history-address').addClass('hide');
						$('#search-result').removeClass('hide');
					});
				}
			});
		});

		$(document).on('click', '.popup-select-buy-address .address-buy-item', function(){
			var $this = $(this);
			if($this.data('available') != 1) {
				alert('该地址不在跑腿服务范围内');
				return false;
			}
			address = {
				address: $this.data('address'),
				name: $this.data('name'),
				location_x: $this.data('lat'),
				location_y: $this.data('lng')
			};
			$('.popup-save-address :text[name="name"]').val(address.name);
			$.popup('.popup-save-address');
		});

		$(document).on('click', '#save-address', function(){
			if(!address.name || !address.location_x) {
				alert('地址信息错误');
				return false;
			}
			var number = $.trim($('.popup-save-address :text[name="number"]').val());
			if(!number) {
				alert('门牌号不能为空');
				return false;
			}
			address.number = number;
			$.post(tiny.getUrl('errander/address/serve_address'), address, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != -1) {
					address.id = result.message.message;
					$('#start-address .item-title').removeClass('color-gray').html(address.name + '~' + address.number);
					order.start_address = address;
					countOrder();
					$.icloseModal('.popup-save-address');
				}
			});
		});

		$(document).on('click', '.popup-select-buy-address .available-address-item', function(){
			var $this = $(this);
			var start_address_id = $this.data('id');
			if(!start_address_id) {
				alert('购买地址有误');
				return false;
			}
			$('#start-address .item-title').removeClass('color-gray').html($this.data('address') + '~' + $this.data('number'));
			order.start_address = {
				id: start_address_id,
				name: $this.data('name'),
				address: $this.data('address'),
				location_x: $this.data('location_x'),
				location_y: $this.data('location_y'),
				number: $this.data('number')
			};
			countOrder();
			$.icloseModal('.popup-select-buy-address');
		});

		$(document).on('click', '.popup-select-start-address .available-address-item', function(){
			var $this = $(this);
			var start_address_id = $this.data('id');
			if(!start_address_id) {
				alert('取货地址有误');
				return false;
			}
			var html = 	'<div>'+$this.data('address')+'</div>'+
				'	<div class="fontsm"><span>'+$this.data('realname')+'</span>'+$this.data('sex')+' <span>'+$this.data('mobile')+'</span></div>';
			$('#start-address .item-title').removeClass('color-gray').html(html);
			order.start_address = {
				id: $this.data('id'),
				name: $this.data('name'),
				address: $this.data('address'),
				location_x: $this.data('location_x'),
				location_y: $this.data('location_y'),
				number: $this.data('number')
			};
			countOrder();
			$.icloseModal('.popup-select-start-address');
		});

		$(document).on('click', '.popup-select-end-address .available-address-item', function(){
			var $this = $(this);
			var end_address_id = $this.data('id');
			if(!end_address_id) {
				alert('收货地址有误');
				return false;
			}
			var html = 	'<div>'+$this.data('address')+'</div>'+
				'	<div class="fontsm"><span>'+$this.data('realname')+'</span>'+$this.data('sex')+' <span>'+$this.data('mobile')+'</span></div>';
			$('#end-address .item-title').removeClass('color-gray').html(html);
			order.end_address = {
				id: $this.data('id'),
				name: $this.data('name'),
				address: $this.data('address'),
				location_x: $this.data('location_x'),
				location_y: $this.data('location_y'),
				number: $this.data('number')
			};
			countOrder();
			$.icloseModal('.popup-select-end-address');
		});

		$(document).on('click', '.edit-address', function(){
			var data = orderData();
			var input = $(this).data('input');
			var id = $(this).data('id');
			$.post(tiny.getUrl('errander/category/cart'), data, function(){
				location.href = tiny.getUrl('errander/address/post', {id: id, errander_id: params.errander_id, redirect_input: input});
			});
		});

		$('.single-slider').jRange({
			theme: 'jrange-theme-custom theme-green',
			from: params.category.tip_min, //滑动范围的最小值，数字，如0
			to: params.category.tip_max, //滑动范围的最大值，数字，如100
			step: 1,//步长值，每次滑动大小,
			width: 300, //滑动条宽度
			showLabels: false,// 是否显示滑动条下方的尺寸标签
			showScale: false, //是否显示滑块上方的数值标签
			onstatechange: function(value){
				$('#tip').html(value);
				var tip_fee = parseFloat(value);
				if(value > 0) {
					$('.tip').removeClass('hide');
				} else {
					$('.tip').addClass('hide');
				}
				$('#total-fee').html((order.delivery_fee + tip_fee).toFixed(2));
			}
		});

		function countOrder() {
			console.dir(order.end_address);
			if(order.end_address.id && order.end_address.location_x) {
				var fee = parseFloat(params.rule.start_fee);
				var start_km = parseFloat(params.rule.start_km);
				var pre_km_fee = parseFloat(params.rule.pre_km_fee);
				if(order.start_address.id && order.start_address.location_x) {
					var lnglat = new AMap.LngLat(order.end_address.location_y, order.end_address.location_x);
					var distance = lnglat.distance([order.start_address.location_y, order.start_address.location_x]);
					distance = parseFloat((distance / 1000)).toFixed(2);
					if(distance > start_km) {
						distance_temp = distance - start_km;
						fee = parseFloat(params.rule.start_fee) + parseFloat((distance_temp * pre_km_fee - 0).toFixed(2));
					}
				}
				order.distance = distance;
				fee = parseFloat(fee).toFixed(2);
				order.delivery_fee = parseFloat(fee);
				$('#delivery-fee').html(order.delivery_fee);
				$('#distance').html(order.distance);
				if(order.distance > 0) {
					$('#distance').parent().removeClass('hide');
				}
				var tip = parseFloat($('#tip_fee').val());
				$('#total-fee').html((order.delivery_fee + tip).toFixed(2));
			}
		}

		function orderData() {
			var data = {
				id: params.errander_id,
				goods_name: $.trim($(':text[name="goods_name"]').val()),
				goods_price: $.trim($(':text[name="goods_price"]').val()),
				note: $.trim($(':text[name="note"]').val()),
				delivery_tips: $.trim($('#tip_fee').val()),
				is_anonymous: $(':checkbox[name="is_anonymous"]').prop('checked') ? 1 : 0,
				end_address_id: order.end_address.id,
				start_address_id: order.start_address.id,
				pay_type: $(':radio[name="pay_type"]:checked').val()
			};
			return data;
		}

		$(document).on('click', '#order-submit', function(){
			var $this = $(this);
			if($this.hasClass('disabled')) {
				return false;
			}
			var data = orderData();
			if(!data.goods_name) {
				$.toast('商品名称不能为空');
				return false;
			}
			if(!data.end_address_id) {
				$.toast('请选择收货地址');
				return false;
			}
			if(!data.pay_type) {
				$.toast('请选择支付方式');
				return false;
			}
			if(!$(':checkbox[name="agree-rule"]').prop('checked')) {
				$.toast('请先同意并接受随意购用户协议');
				return false;
			}
			$this.addClass('disabled');
			$.post(tiny.getUrl('errander/order/create'), data, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno == -1) {
					$this.removeClass('disabled');
					$.toast(result.message.message);
					return false;
				} else {
					$.toast('下单成功');
					location.href = tiny.getUrl('system/paycenter/pay', {order_type: 'errander', id: result.message.message});
				}
			});
		});
	}
	return trade;
});