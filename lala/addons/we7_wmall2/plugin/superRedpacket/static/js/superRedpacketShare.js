define(['jquery.ui', 'clockpicker', 'datetimepicker'], function(ui, $, datetimepicker) {
	var superRedpacket = {};
	superRedpacket.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		superRedpacket.attachurl = params.attachurl;
		superRedpacket.id = params.id;
		superRedpacket.data = params.data;
		if(!superRedpacket.data) {
			superRedpacket.data = {
				activity: {
					status: 1,
					name: '分享超级红包',
					condition: 10,
					packet_min_num: 1,
					packet_max_num: 5,
					redpacket_min_num: 1,
					redpacket_max_num: 3,
					starttime: params.timestamp,
					endtime: params.timestamp,
					image: '../addons/we7_wmall/plugin/superRedpacket/static/img/superredpacket-share-bg.jpg',
					color: '#ef361e',
					backgroundColor: '#e36e07',
					button: {
						color: '#ffffff',
						backgroundColor: '#ff3f26'
					},
					agreement: "1.红包新老用户同享\n2.红包可与其他优惠叠加使用，首单支付红包不可叠加\n3.其他未尽事宜，请咨询客服"
				},
				redpackets: {
					M0123456789101: {
						name: '通用红包',
						nums: 100,
						discount: 4,
						condition: 35,
						grant_days_effect: 1,
						use_days_limit: 10,
						times: {},
						categorys: {}
					},
					M0123456789102: {
						name: '下午茶频道红包',
						nums: 100,
						discount: 5,
						condition: 40,
						grant_days_effect: 1,
						use_days_limit: 10,
						times: {
							T0123456789101: {
								start_hour: '13:00',
								end_hour: '17:30'
							}
						},
						categorys: {}
					},
					M0123456789103: {
						name: '夜宵频道红包',
						nums: 100,
						discount: 6,
						condition: 50,
						grant_days_effect: 1,
						use_days_limit: 10,
						times: {
							T0123456789101: {
								start_hour: '00:00',
								end_hour: '05:59'
							},
							T0123456789102: {
								start_hour: '20:00',
								end_hour: '23:59'
							}
						},
						categorys: {}
					}
				},
				share: {
					title: '暑伏美食,优惠等你拿',
					desc : '太阳辣眼睛,点份外卖不出门',
					imgUrl: '../addons/we7_wmall/plugin/superRedpacket/static/img/header.png'
				}
			}
		}

		tmodtpl.helper("tomedia", function(src){
			if (typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons') == 0) {
				return src;
			}
			if(src.indexOf('images/') == 0) {
				return superRedpacket.attachurl + src;
			}
		});

		tmodtpl.helper("date", function(time) {
			return new Date(parseInt(time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
		});
		superRedpacket.tplSuperRedpacket();
		superRedpacket.tplEditor();
		superRedpacket.initGotop();
		superRedpacket.save();
	};

	superRedpacket.tplSuperRedpacket = function() {
		var html = tmodtpl("tpl-show-superRedpacket", superRedpacket.data);
		$("#app-preview").html(html);
	};

	superRedpacket.tplEditor = function() {
		var html = tmodtpl("tpl-edit-superRedpacket", superRedpacket.data);
		$("#app-editor .inner").html(html);
		$('.clockpicker :text').clockpicker({autoclose: true});

		$(".datetimepicker").each(function(){
			var option = {
				lang : "zh",
				step : "10",
				timepicker :  true,
				closeOnDateSelect : true,
				format : "Y-m-d H:i:s"
			};
			$(this).datetimepicker(option);
		});

		$(".app-editor #addItem").unbind('click').click(function() {
			var itemid = superRedpacket.getId('M', 0);
			superRedpacket.data.redpackets[itemid] = {
				name: '通用红包',
				nums: 100,
				discount: 5,
				condition: 10,
				grant_days_effect: 1,
				use_days_limit: 10,
				times: {},
				categorys: {}
			};
			superRedpacket.tplSuperRedpacket();
			superRedpacket.tplEditor();
		});

		$(".app-editor .del-item").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = superRedpacket.length(superRedpacket.data.redpackets);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete superRedpacket.data.redpackets[itemid];
				superRedpacket.tplSuperRedpacket();
				superRedpacket.tplEditor();
			});
		});

		$(".app-editor .hour-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var item = superRedpacket.data.redpackets[itemid];
			if (!item.times) {
				item.times = {};
			}
			var length = superRedpacket.length(item.times);
			if (length >= 2) {
				Notify.info("最大添加2个！");
				return;
			}
			var timeid = superRedpacket.getId('T', 0);
		 	item.times[timeid] = {
				start_hour:  '20:00',
				end_hour: '23:00'
			};
			superRedpacket.tplEditor();
		});

		$(".app-editor .hour-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var timeid = $(this).data('id');
			var times = superRedpacket.data.redpackets[itemid]['times'];
			Notify.confirm("确定删除吗", function() {
				delete times[timeid];
				superRedpacket.tplEditor();
			});
		});

		$(".app-editor .category-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var item = superRedpacket.data.redpackets[itemid];
			var categoryid = superRedpacket.getId('C', 0);
			if(!item.categorys) {
				item.categorys = {};
			}
			item.categorys[categoryid] = {
				id: 0,
				title: '选择分类',
				src: ''
			};
			superRedpacket.tplEditor();
		});

		$(".app-editor .category-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var categorys = superRedpacket.data.redpackets[itemid]['categorys'];
			var categoryid = $(this).data('id');
			Notify.confirm("确定删除吗", function() {
				delete categorys[categoryid];
				superRedpacket.tplEditor();
			});
		});

		$(".app-editor").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var bindcategory = _this.data('bind-category');
			var bindtype = _this.data('bind-type');
			var tplEditor = _this.data('bind-init');
			var value = '';
			var tag = this.tagName;
			if (tag == 'INPUT') {
				var placeholder = _this.data('placeholder');
				value = _this.val();
				value = value == '' ? placeholder : value;
			} else if (tag == 'SELECT') {
				value = _this.find('option:selected').val();
			} else if (tag == 'TEXTAREA') {
				value = _this.val();
			}
			value = $.trim(value);
			if(bindchild) {
				if(bindparent) {
					if(bindcategory) {
						if(bindtype) {
							superRedpacket.data[bindchild][bindparent][bind][bindcategory][bindtype] = value;
						} else {
							superRedpacket.data[bindchild][bindparent][bind][bindcategory] = value;
						}
					} else {
						superRedpacket.data[bindchild][bindparent][bind] = value;
					}
				} else {
					superRedpacket.data[bindchild][bind] = value;
				}
			} else {
				superRedpacket.data[bind] = value;
			}
			superRedpacket.tplSuperRedpacket();
			if(tplEditor) {
				superRedpacket.tplEditor();
			}
		});
	};

	superRedpacket.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	superRedpacket.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	superRedpacket.initGotop = function() {
		$(window).bind('scroll resize', function() {
			var scrolltop = $(window).scrollTop();
			if (scrolltop > 100) {
				$("#gotop").show()
			} else {
				$("#gotop").hide()
			}
			$("#gotop").unbind('click').click(function() {
				$('body').animate({scrollTop: "0px"}, 1000)
			})
		})
	};

	superRedpacket.save = function() {
		$(".btn-save").unbind('click').click(function() {
			var name = superRedpacket.data.activity.name;
			if(!name) {
				Notify.info("活动名称不能为空");
				return;
			}
			var packet_min_num = parseInt(superRedpacket.data.activity.packet_min_num);
			var packet_max_num = parseInt(superRedpacket.data.activity.packet_max_num);
			var redpacket_min_num = parseInt(superRedpacket.data.activity.redpacket_min_num);
			var redpacket_max_num = parseInt(superRedpacket.data.activity.redpacket_max_num);
			if(!packet_min_num || !packet_max_num) {
				Notify.info("卡包数量的最大值和最小值不能为空");
				return;
			}
			if(packet_min_num >= packet_max_num) {
				Notify.info("卡包数量的最大值不能小于最小值");
				return;
			}
			if(!redpacket_min_num || !redpacket_max_num) {
				Notify.info("红包数量的最大值和最小值不能为空");
				return;
			}
			if(redpacket_min_num >= redpacket_max_num) {
				Notify.info("红包数量的最大值不能小于最小值");
				return;
			}
			var status = $(this).data('status');
			if(status) {
				Notify.info("正在保存，请稍候。。。");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
		    var posturl = "./index.php?c=site&a=entry&ctrl=superRedpacket&ac=share&op=post&do=web&m=we7_wmall";
			$.post(posturl, {id: superRedpacket.id, data: superRedpacket.data}, function(result) {
				$(".btn-save").text("保存").data("status", 0);
				if(result.message.errno != 0) {
					Notify.error(result.message.message);
					return;
				}
				Notify.success("保存成功！", result.message.url);
			}, 'json');
		});
	};
	return superRedpacket;
});