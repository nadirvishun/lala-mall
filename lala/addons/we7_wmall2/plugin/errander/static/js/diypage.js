define(['jquery.ui'], function (ui) {
	var diypage = {itemid: ''};
	diypage.init = function (params) {
		window.tmodtpl = params.tmodtpl;
		diypage.attachurl = params.attachurl;
		diypage.data = params.data;
		diypage.id = params.id;
		if(!diypage.data) {
			diypage.data = {
				params: {
					type: 'buy',
					container: '.head-banner'
				},
				slide: {
					M0123456789101: {
						img: '../addons/we7_wmall/plugin/diypage/static/img/1.png',
						link: ''
					}
				},
				categoryBuy: {
					M0123456789102: {
						title: '买小吃',
						subtitle: '美味在身边',
						img: '../addons/we7_wmall/plugin/diypage/static/img/1.png',
					}
				},
				categoryDelivery: {
					M0123456789103: {
						title: '鲜花',
						img: '../addons/we7_wmall/plugin/diypage/static/img/1.png',
					}
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
				return diypage.attachurl + src;
			}
		});
		diypage.tplPage();
		diypage.selectContainer();
		diypage.tplEditor();
		diypage.initGotop();
		diypage.save();
	};

	diypage.tplPage = function() {
		var html = tmodtpl("tpl-page", diypage.data);
		$("#app-preview").html(html);
	}

	diypage.selectContainer = function () {
		$(document).on('click', '.select-container', function() {
			var container = $(this).data('container');
			diypage.data.params.container = container;
			diypage.tplPage();
			diypage.tplEditor();
		});

		$(document).on('click', '#app-preview .head-tabs .tabs-head-item', function() {
			var type = $(this).data('type');
			diypage.data.params.type = type;
			diypage.tplPage();
			diypage.tplEditor();
		});

		$(document).on('click', '.select-container .btn-edit', function() {
			var container = $(this).parents('.select-container').data('container');
			diypage.data.params.container = container;
			diypage.tplPage();
		});

		$(document).on('click', '.select-container .btn-del', function() {
			var $this = $(this).parents('.select-container');
			var type = $this.data('type');
			diypage.data[type] = {};
			diypage.tplPage();
			diypage.tplEditor();
 		});
	};
	diypage.tplEditor = function() {
		var container = diypage.data.params.container;
		var top = $(container).offset().top;
		if(top > 50) {
			$("#app-editor").unbind('animate').animate({"margin-top": top - 80 + "px"});
			setTimeout(function () {
				$("body").unbind('animate').animate({scrollTop: top - 80 + "px"}, 1000)
			}, 1000);
		}
		var html = tmodtpl("tpl-editor", diypage.data);
		$("#app-editor .inner").html(html);

		$("#app-editor .addItem").unbind('click').click(function () {
			var itemid = diypage.getId('M', 0);
			var type = $(this).closest('.form-items').data('type');
			var max = $(this).closest('.form-items').data('max');
			if(max) {
				var length = diypage.length(diypage.data[type]);
				if(length >= max) {
					Notify.info("最多添加 " + max + " 个！");
					return;
				}
			}
			if(type == 'slide') {
				diypage.data.slide[itemid] = {
					img: '../addons/we7_wmall/plugin/diypage/static/img/1.png',
					link: ''
				};
			} else if(type == 'categoryBuy') {
				diypage.data.categoryBuy[itemid] = {
					title: '买小吃',
					subtitle: '美味在身边',
					img: '../addons/we7_wmall/plugin/diypage/static/img/1.png',
				};
			} else if(type == 'categoryDelivery') {
				diypage.data.categoryDelivery[itemid] = {
					title: '鲜花',
					img: '../addons/we7_wmall/plugin/diypage/static/img/1.png',
				};
			}
			diypage.tplPage();
			diypage.tplEditor();
		});

		$("#app-editor .del-item").unbind('click').click(function() {
			var type = $(this).closest('.form-items').data('type');
			var min = $(this).closest('.form-items').data('min');
			var itemid = $(this).closest('.item').data('id');
			if(min) {
				var length = diypage.length(diypage.data[type]);
				if(length <= min) {
					Notify.info("至少保留 " + min + " 个！");
					return;
				}
			}
			Notify.confirm("确定删除吗", function() {
				delete diypage.data[type][itemid];
				diypage.tplPage();
				diypage.tplEditor();
			});
		});

		diypage.tplSortable();

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
					diypage.data[bindchild][bindparent][bind] = value;
				} else {
					diypage.data[bindchild][bind] = value;
				}
			} else {
				diypage.data[bind] = value;
			}
			diypage.tplPage();
			if(tplEditor) {
				diypage.tplEditor();
			}
		});
	}

	diypage.tplSortable = function () {
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
				diypage.sortItems();
			}
		});
	};

	diypage.sortItems = function () {
		var newItems = {};
		var $this = $('#app-editor .form-items').not('.hide');
		var type = $this.data('type');
		$this.find('.item').each(function () {
			var thisid = $(this).data('id');
			newItems[thisid] = diypage.data[type][thisid];
		});
		diypage.data[type] = newItems;
		diypage.tplPage();
	};

	diypage.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};

	diypage.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	diypage.initGotop = function () {
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

	diypage.save = function () {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if (status) {
				Notify.info("正在保存，请稍候。。。");
				return;
			}
			if(!diypage.data) {
				Notify.info("菜单为空！");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			var posturl = "./index.php?c=site&a=entry&ctrl=errander&ac=diypage&op=post&do=web&m=we7_wmall";
			$.post(posturl, {id: diypage.id, data: diypage.data}, function(result) {
				$(".btn-save").text("保存菜单").data("status", 0);
				if(result.message.errno != 0) {
					Notify.error(result.message.result);
					return;
				}
				Notify.success("保存成功！", result.message.url);
			}, 'json');
		});
	};
	return diypage;
});