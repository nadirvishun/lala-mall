define(['tiny', 'map', 'laytpl', 'jquery.range'], function(tiny, AMap, laytpl) {
	var trade = {};
	trade.init = function(params) {
		var order = {
			start_address: {},
			end_address: {},
			delivery_fee: 0,
			start_address_num: 1,
			multiaddress: [],
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

		$('input[name="goods_weight"]').bind('input propertchange', function() {
			countOrder();
		});

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

		$(document).on('click', '.multiaddress .icon-add', function(){
			var config_multiaddress = params.rule.multiaddress;
			var size = $('.multiaddress .item-content.start-address').size();
			if(size >= config_multiaddress.max) {
				$.toast("最多只能添加"+config_multiaddress.max+"个地址");
				return false;
			}
			var html =  '<li class="item-content start-address border-1px-b">'+
				'	<div class="item-media"><i class="icon icon-gou"></i></div>'+
				'	<div class="item-input">'+
				'		<input type="text" name="address[]" placeholder="请输入购买地址">'+
				'	</div>'+
				'	<div class="item-media"><a href="javascript:;"><i class="icon icon-minus"></i></a></div>'+
				'</li>';
			$(this).parents('.multiaddress').append(html);
			countOrder();
		});

		$(document).on('click', '.multiaddress .icon-minus', function(){
			var size = $('.multiaddress .item-content.start-address').size();
			if(size <= 1) {
				$(this).removeClass('icon-minus').addClass('icon-add');
				$.toast("最少保留一个地址");
				return false;
			}
			$(this).parent().parent().parent().remove();
			countOrder();
		});

		$('.single-slider').jRange({
			theme: 'jrange-theme-custom theme-green',
			from: parseFloat(params.category.tip_min), //滑动范围的最小值，数字，如0
			to: parseFloat(params.category.tip_max), //滑动范围的最大值，数字，如100
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
				if(!order.discount_fee) {
					order.discount_fee = 0;
				}
				$('#final-fee').html((order.delivery_fee - order.discount_fee + tip_fee).toFixed(2));
			}
		});

		$(document).on('click', '.delivery-time-show', function(){
			$.iopenModal('.delivery-time-modal', function(){
				var init_show = $('#delivery-time-children li').not('.hide').size();
				if(!init_show) {
					var now_day = $('#delivery-time-parent li.active');
					now_day.next().trigger('click');
					now_day.addClass('hide');
				}
				$('.delivery-time-modal .children-category-wrapper').height(350);
				if($.device.iphone) {
					new IScroll('.delivery-time-modal .children-category-wrapper', {probeType: 3, mouseWheel: true, click: false, tap: true})
				} else {
					new IScroll('.delivery-time-modal .children-category-wrapper', {probeType: 3, mouseWheel: true, click: true})
				}
			});
		});

		$(document).on('click', '#delivery-time-children li:not(.delivery-tips)', function(){
			$(this).addClass('active').siblings().removeClass('active');
			var day = $('#delivery-time-parent li.active').data('value');
			var time = $(this).data('value');
			$('#delivery-time').val(time);
			$('#delivery-day').val(day);
			$('.delivery-time-show').html(day + ' ' + time);
			countOrder();
			$.icloseModal('.delivery-time-modal', true);
		});

		$(document).on('click', '#delivery-time-parent li', function(){
			$(this).addClass('active').siblings().removeClass('active');
			var myDate = new Date();
			var month = myDate.getMonth() + 1;
			if(month < 10) {
				month = '0' + month;
			}
			var day = myDate.getDate();
			if(day < 10) {
				day = '0' + day;
			}
			var today_date = month + '-' + day;
			var date = $(this).data('value');
			if(today_date == date) {
				if(params.time_flag == 1) {
					$('#delivery-time-children li.time-flag').removeClass('hide');
				}
				$('#delivery-time-children li.init-hide').addClass('hide');
			} else {
				$('#delivery-time-children li.time-flag').addClass('hide');
				$('#delivery-time-children li.init-hide').removeClass('hide');
			}
			if($.device.iphone) {
				new IScroll('.delivery-time-modal .children-category-wrapper', {probeType: 3, mouseWheel: true, click: false, tap: true})
			} else {
				new IScroll('.delivery-time-modal .children-category-wrapper', {probeType: 3, mouseWheel: true, click: true})
			}
			countOrder();
			return false;
		});

		$(document).on("click", '.js-open-delivery-fee-modal', function(e) {
			e.preventDefault();
			var modal = $(this).data('modal');
			if(!modal || !$(modal).size()) {
				return false;
			}
			$.iopenModal(modal, function(){});
		});

		function countOrder() {
			var datas = {
				id: params.errander_id,
				start_address_num: 1,
				start_address: order.start_address,
				end_address: order.end_address,
				goods_weight: $.trim($(':text[name="goods_weight"]').val()),
				predict_index: $("#delivery-time-children li.active").data('id'),
				delivery_tips: $.trim($('#tip_fee').val()),
			};
			if(params.rule.weight_fee_status == 1 && !datas.goods_weight) {
				$.toast('商品重量不能为空');
				return false;
			}
			datas.start_address_num = parseInt($('.multiaddress .item-content.start-address').size());
			$.showIndicator();
			$.post(tiny.getUrl('errander/order/delivery_fee'), datas, function(data){
				var result = $.parseJSON(data);
				if(!result.message.errno) {
					order.delivery_fee = parseFloat(result.message.message['delivery_fee']);
					order.discount_fee = parseFloat(result.message.message['discount_fee']);
					$('#delivery-fee').html(result.message.message['delivery_fee']);
					$('#discount-fee').html(result.message.message['discount_fee']);
					if(order.discount_fee > 0) {
						$('#discount-box').show();
					} else {
						$('#discount-box').hide();
					}
					$('#final-fee').html(result.message.message['final_fee']);
					$('.modal-delivery-fee .notice').html(result.message.message['message']);
					if(result.message.message['distance'] > 0) {
						$('#distance').html(result.message.message['distance']);
						$('#distance').parent().removeClass('hide');
					}
				} else {
					$.toast(result.message.message);
				}
				$.hideIndicator();
			});
		}

		function orderData() {
			order.multiaddress_status = 1;
			order.multiaddress = [];
			if(params.rule.type == 'multiaddress') {
				$('.multiaddress .start-address').each(function(){
					var address = $.trim($(this).find('input[type="text"]').val());
					if(address) {
						order.multiaddress.push(address);
					} else {
						order.multiaddress_status = 0;
					}
				});
			}
			var data = {
				id: params.errander_id,
				goods_name: $.trim($(':text[name="goods_name"]').val()),
				goods_price: $.trim($(':text[name="goods_price"]').val()),
				goods_price_cn: $.trim($(':hidden[name="goods_price_cn"]').val()),
				goods_weight: $.trim($(':text[name="goods_weight"]').val()),
				note: $.trim($('textarea[name="note"]').val()),
				delivery_tips: $.trim($('#tip_fee').val()),
				is_anonymous: $(':checkbox[name="is_anonymous"]').prop('checked') ? 1 : 0,
				multiaddress: order.multiaddress,
				start_address_id: order.start_address.id,
				end_address_id: order.end_address.id,
				predict_index: $("#delivery-time-children li.active").data('id'),
				delivery_day: $('#delivery-day').val(),
				delivery_time: $('#delivery-time').val(),
				pay_type: $(':radio[name="pay_type"]:checked').val(),
				thumbs: []
			};
			$('.tpl-image .image-item input[type!="file"]').each(function(){
				var value = $.trim($(this).val());
				if(value) {
					data.thumbs.push(value);
				}
			});
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
			if(params.rule.weight_fee_status == 1 && !data.goods_weight) {
				$.toast('商品重量不能为空');
				return false;
			}
			if(params.rule.type == 'multiaddress') {
				if(data.multiaddress.length <= 0) {
					$.toast('请填写购买地址');
					return false;
				}
				if(!order.multiaddress_status) {
					$.toast('存在没有输入地址的购买地址');
					return false;
				}
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