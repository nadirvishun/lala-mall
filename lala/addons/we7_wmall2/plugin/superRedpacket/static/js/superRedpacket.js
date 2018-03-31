define(['jquery.ui', 'clockpicker'], function(ui, $) {
	var superRedpacket = {};
	superRedpacket.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		superRedpacket.attachurl = params.attachurl;
		superRedpacket.id = params.id;
		superRedpacket.data = params.data;
		if(!superRedpacket.data) {
			superRedpacket.data = {
				name: '超级红包',
				page: {
					image: '../addons/we7_wmall/plugin/superRedpacket/static/img/header.png',
					text: {
						color: '#fb584f',
						backgroundColor: '#b80404'
					},
					button: {
						color: '#333',
						backgroundColor: '#ffd161'
					}
				},
				redpackets: {
					M0123456789101: {
						name: '通用红包',
						discount: 5,
						condition: 20,
						grant_days_effect: 0,
						use_days_limit: 7,
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
				customer: {
					type: 0,
					uid : '',
					template_notice: 2
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

		$("#app-editor #addItem").unbind('click').click(function() {
			var itemid = superRedpacket.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = superRedpacket.length(superRedpacket.data.redpackets);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			superRedpacket.data.redpackets[itemid] = {
				name: '通用红包',
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

		$("#app-editor .del-item").unbind('click').click(function() {
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
				superRedpacket.tplEditor();
			});
		});

		$("#app-editor .hour-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var times = superRedpacket.data.redpackets[itemid]['times'];
			var length = superRedpacket.length(times);
			if (length >= 3) {
				Notify.info("最大添加3个！");
				return;
			}
			var timeid = superRedpacket.getId('T', 0);
		 	times[timeid] = {
				start_hour:  '20:00',
				end_hour: '23:00'
			};
			superRedpacket.tplEditor();
		});

		$("#app-editor .hour-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var timeid = $(this).data('id');
			var times = superRedpacket.data.redpackets[itemid]['times'];
			Notify.confirm("确定删除吗", function() {
				delete times[timeid];
				superRedpacket.tplEditor();
			});
		});

		$("#app-editor .category-add").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var categorys = superRedpacket.data.redpackets[itemid]['categorys'];
			var categoryid = superRedpacket.getId('C', 0);
			categorys[categoryid] = {
				id: 0,
				title: '选择分类',
				src: ''
			};
			superRedpacket.tplEditor();
		});

		$("#app-editor .category-del").unbind('click').click(function() {
			var itemid = $(this).closest('.item').data('id');
			var categorys = superRedpacket.data.redpackets[itemid]['categorys'];
			var categoryid = $(this).data('id');
			Notify.confirm("确定删除吗", function() {
				delete categorys[categoryid];
				superRedpacket.tplEditor();
			});
		});

		$("#app-editor").find(".diy-bind").bind('input propertychange change', function() {
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
			var $this = $(this);
			Notify.confirm('确定保存并开始发放吗?', function() {
				var uid = $.trim($('textarea[name="uid"]').val());
				if(superRedpacket.data.customer.type == 1 && !uid) {
					Notify.info("请指定要发放的顾客");
					return;
				}
				var status = $this.data('status');
				if(status) {
					Notify.info("正在保存，请稍候。。。");
					return;
				}
				$(".btn-save").data('status', 1).text("保存中...");
				var posturl = "./index.php?c=site&a=entry&ctrl=superRedpacket&ac=grant&op=post&do=web&m=we7_wmall";
				$.post(posturl, {id: superRedpacket.id, data: superRedpacket.data}, function(result) {
					$(".btn-save").text("保存并开始发放").data("status", 0);
					if(result.message.errno != 0) {
						Notify.error(result.message.message);
						return;
					}
					Notify.success("保存成功,准备发放！", result.message.url);
				}, 'json');
			});
			return false;
		});
	};
	return superRedpacket;
});