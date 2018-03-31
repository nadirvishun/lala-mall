define(['jquery.ui'], function(ui) {
	var diyMenu = {itemid: ''};
	diyMenu.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		diyMenu.attachurl = params.attachurl;
		diyMenu.menu = params.menu;
		diyMenu.id = params.id;
		if(!diyMenu.menu) {
			diyMenu.menu = {
				name: '自定义菜单',
				params: {'navstyle': '0'},
				css: {
					'iconColor': '#163636',
					'iconColorActive': '#ff2d4b',
					'textColor': '#929292',
					'textColorActive': '#ff2d4b',
				},
				data: {
					M0123456789101: {
						pagePath: "pages/home/index",
						iconPath: "/static/img/tabbar/icon-1.png",
						selectedIconPath: "/static/img/tabbar/icon-1-active.png",
						text: '首页'
					},
					M0123456789102: {
						pagePath: "pages/order/index",
						iconPath: "/static/img/tabbar/icon-3.png",
						selectedIconPath: "/static/img/tabbar/icon-3-active.png",
						text: '订单'
					},
					M0123456789103: {
						pagePath: "pages/member/mine",
						iconPath: "/static/img/tabbar/icon-5.png",
						selectedIconPath: "/static/img/tabbar/icon-5-active.png",
						text: '我的'
					},
				}
			}
		}

		tmodtpl.helper("tomedia", function(src) {
			if (typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons') == 0) {
				return src;
			}
			if(src.indexOf('images/') == 0) {
				return diyMenu.attachurl + src;
			}
		});

		diyMenu.tplMenu();
		diyMenu.tplEditor();
		diyMenu.initGotop();
		diyMenu.save();
	};

	diyMenu.tplMenu = function () {
		var html = tmodtpl("tpl-show-menu", diyMenu.menu);
		$("#app-preview").html(html);
	};

	diyMenu.tplEditor = function () {
		var html = tmodtpl("tpl-edit-menu", diyMenu.menu);
		$("#app-editor .inner").html(html);

		$("#app-editor #addItem").unbind('click').click(function () {
			var itemid = diyMenu.getId('M', 0);
			var max = $(this).closest('.form-items').data('max');
			var num = diyMenu.length(diyMenu.menu.data);
			if (num >= max) {
				Notify.info("最大添加 " + max + " 个！");
				return;
			}
			diyMenu.menu.data[itemid] = {
				pagePath: "pages/home/index",
				iconPath: "/static/img/tabbar/icon-1.png",
				selectedIconPath: "/static/img/tabbar/icon-1-active.png",
				text: '菜单文字'
			};
			diyMenu.tplMenu();
			diyMenu.tplEditor();
		});

		$("#app-editor .del-item").unbind('click').click(function() {
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = diyMenu.length(diyMenu.menu.data);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete diyMenu.menu.data[itemid];
				diyMenu.tplMenu();
				diyMenu.tplEditor()
			});
		});

		diyMenu.tplSortable();

		$("#app-editor").find(".diy-bind").bind('input propertychange change', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
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
					diyMenu.menu[bindchild][bindparent][bind] = value;
				} else {
					diyMenu.menu[bindchild][bind] = value;
				}
			} else {
				diyMenu.menu[bind] = value;
			}
			diyMenu.tplMenu();
			if(tplEditor) {
				diyMenu.tplEditor();
			}
		});
	};

	diyMenu.tplSortable = function () {
		$("#app-editor .inner").sortable({
			opacity: 0.8,
			placeholder: "highlight",
			items: '.item',
			revert: 100,
			scroll: false,
			cancel: '.goods-selector,input,.btn',
			axis: 'y',
			start: function(event, ui) {
				var height = ui.item.height();
				$(".highlight").css({"height": height + 22 + "px", "margin-bottom" : "10px"});
				$(".highlight").html('<div><i class="icon icon-plus"></i> 放置此处</div>');
				$(".highlight div").css({"line-height": height + 16 + "px", "font-size" : "16px", "color" : "#999", "text-align" : "center", "border" : "2px dashed #eee"})
			},
			update: function(event, ui) {
				diyMenu.sortItems();
			}
		});
	};

	diyMenu.sortItems = function () {
		var newItems = {};
		$("#app-editor .inner .item").each(function () {
			var thisid = $(this).data('id');
			newItems[thisid] = diyMenu.menu.data[thisid]
		});
		diyMenu.menu.data = newItems;
		diyMenu.tplMenu();
	};

	diyMenu.initGotop = function () {
		$(window).bind('scroll resize', function () {
			var scrolltop = $(window).scrollTop();
			if (scrolltop > 100) {
				$("#gotop").show()
			} else {
				$("#gotop").hide()
			}
			$("#gotop").unbind('click').click(function () {
				$('body').animate({scrollTop: "0px"}, 1000)
			})
		})
	};

	diyMenu.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	diyMenu.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	diyMenu.save = function () {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if (status) {
				Notify.info("正在保存，请稍候。。。");
				return;
			}
			if(!diyMenu.menu.data) {
				Notify.info("菜单为空！");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			var posturl = "./index.php?c=site&a=entry&ctrl=wxapp&ac=menu&op=post&do=web&m=we7_wmall";
			$.post(posturl, {id: diyMenu.id, menu: diyMenu.menu}, function(result) {
				$(".btn-save").text("保存菜单").data("status", 0);
				if(result.message.errno != 0) {
					Notify.error(result.message.result);
					return;
				}
				Notify.success("保存成功！", result.message.url);
			}, 'json');
		});
	};
	return diyMenu;
});