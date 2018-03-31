define(['jquery.ui'], function(ui) {
	var diy = {
		id: 0,
		type: 1,
		initPart: [],
		data: {},
		selected: 'page',
		childid: null
	};

	diy.updateItem = function(itemid) {
		if(itemid) {
			var item = diy.items[itemid];
			if(item) {
				var $obj = $('.drag[data-itemid="'+itemid+'"]');
				item['style']['top'] = parseFloat($obj.css('top'));
				item['style']['left'] = parseFloat($obj.css('left'));
				item['style']['width'] = parseFloat($obj.css('width'));
				item['style']['height'] = parseFloat($obj.css('height'));
				item['style']['zindex'] = parseFloat($obj.css('z-index'));
				diy.items[itemid] = item;
			}
		} else {
			$.each(diy.items, function(itemid, item) {
				var $obj = $('.drag[data-itemid="'+itemid+'"]');
				item['style']['top'] = parseFloat($obj.css('top'));
				item['style']['left'] = parseFloat($obj.css('left'));
				item['style']['width'] = parseFloat($obj.css('width'));
				item['style']['height'] = parseFloat($obj.css('height'));
				item['style']['zindex'] = parseFloat($obj.css('z-index'));
				diy.items[itemid] = item;
			});

		}
		return true;
	};

	diy.deleteItems = function(itemid) {
		var item = diy.items[itemid];
		if(!item) {
			return;
		}
		delete(diy.items[itemid]);
		diy.initItems();
	};

	diy.dragEvent = function(obj) {
		var itemid = obj.data('itemid');
		var posterrs = new Resize(obj, {
			Max : true,
			mxContainer : "#app-preview",
			onStop: function() {
				diy.updateItem(itemid);
			}
		});
		posterrs.Set($(".rRightDown", obj), "right-down");
		posterrs.Set($(".rLeftDown", obj), "left-down");
		posterrs.Set($(".rRightUp", obj), "right-up");
		posterrs.Set($(".rLeftUp", obj), "left-up");
		posterrs.Set($(".rRight", obj), "right");
		posterrs.Set($(".rLeft", obj), "left");
		posterrs.Set($(".rUp", obj), "up");
		posterrs.Set($(".rDown", obj), "down");
		posterrs.Scale = true;
		var type = obj.attr('type');
		if (type == 'name' || type == 'img' || type == 'code') {
			posterrs.Scale = false;
		}
		new Drag(obj, {
			Limit : true,
			mxContainer : "#app-preview",
			onStop: function() {
				var itemid = obj.data('itemid');
				diy.updateItem(itemid);
			}
		});
		$('.drag .remove').unbind('click').click(function() {
			$(this).parent().remove();
		});
		irequire(['jquery.contextMenu'],function(){
			$.contextMenu({
				selector : '.drag[data-itemid="'+itemid+'"]',
				callback : function(key, options) {
					var zindex = parseInt($(this).attr('zindex'));
					if (key == 'prev') {
						var prevdiv = $(this).prev('.drag');
						if (prevdiv.length > 0) {
							$(this).insertBefore(prevdiv);
						}
					} else if (key == 'next') {
						var nextdiv = $(this).next('.drag');
						if (nextdiv.length > 0) {
							nextdiv.insertBefore($(this));
						}
					} else if (key == 'last') {
						var len = $('.drag').length;
						if (zindex >= len - 1) {
							return;
						}
						var last = $('#app-preview .drag:last');
						if (last.length > 0) {
							$(this).insertAfter(last);
						}
					} else if (key == 'first') {
						var zindex = $(this).index();
						if (zindex < 1) {
							return;
						}
						var first = $('#app-preview .drag:first');
						if (first.length > 0) {
							$(this).insertBefore(first);
						}
					} else if (key == 'delete') {
						$(this).remove();
						diy.deleteItems(itemid);
					}
					var n = 1;

					$('.drag:not(".drag-background")').each(function() {
						$(this).css("z-index", n);
						n++;
					});

					var newItems = {};
					$('.drag').each(function() {
						var itemid = $(this).data('itemid');
						newItems[itemid] = diy.items[itemid];;
					});
					diy.items = newItems;
					console.dir(diy.items);
					diy.updateItem();
				},
				items : {
					"next" : {
						name : "移动到上一层"
					},
					"prev" : {
						name : "移动到下一层"
					},
					"last" : {
						name : "移动到最顶层"
					},
					"first" : {
						name : "移动到最低层"
					},
					"delete" : {
						name : "删除元素"
					}
				}
			});
		});

		obj.unbind('click').click(function() {
			//jun_bind($(this));
		})
	};

	diy.init = function(params) {
		window.tmodtpl = params.tmodtpl;
		diy.attachurl = params.attachurl;
		diy.data = params.data;
		console.dir(params.data);
		diy.id = params.id;
		if(diy.data) {
			diy.page = diy.data.page;
			diy.items = diy.data.items;
		};
		diy.initTpl();
		diy.initPage();
		diy.initParts();
		diy.initItems();
		diy.initSave();
		$("#page").unbind('click').click(function(){
			if(diy.selected == 'page') {
				return;
			};
			diy.selected = 'page';
			diy.initPage();
		});
		$(document).on('mousedown', "#app-preview .drag", function(){
			if($(this).hasClass("selected")) {
				return;
			}
			$("#app-preview").find(".drag").removeClass("selected");
			$(this).addClass("selected");
			diy.selected = $(this).data('itemid');
			diy.initEditor();
		});
	};
	diy.initParts = function(){
		diy.getParts();
		var partGroup = {
			0: ['background', 'nickname', 'avatar', 'qrcode', 'image', 'text']
		};
		var partPage = partGroup[0];
		$.each(partPage, function(index, val) {
			var params = diy.parts[val];
			if(params) {
				params.id = val;
				diy.initPart.push(params);
			}
		});
		var html = tmodtpl("tpl-parts", diy);
		$("#parts").html(html).show();

		$("#parts nav").unbind('click').click(function(){
			var id = $(this).data('id');
			if(id === 'page') {
				$("#page").trigger("click");
				return;
			} else if(id === 'background') {
				$(".drag.drag-background").trigger('mousedown').trigger("click");
				return;
			}
			var inArray = $.inArray(id, partPage);
			if(inArray < 0) {
				Notify.error("此页面组建不存在！");
				return
			}
			var item = $.extend(true, {}, diy.parts[id]);
			delete item.name;
			if(!item) {
				Notify.error("未找到此元素！");
				return
			}
			var itemTplShow = $("#tpl-show-" + id).length;
			var itemTplEditor = $("#tpl-editor-" + id).length;
			if(itemTplShow == 0 || itemTplEditor == 0) {
				Notify.error("添加失败！模板错误，请刷新页面重试");
				return;
			}
			var itemid = diy.getId("M", 0);
			item['style']['zindex'] = diy.length(diy.items);
			diy.items[itemid] = item;
			diy.initItems();
			$(".drag[data-itemid='" + itemid + "']").trigger('mousedown').trigger('click');
			diy.selected = itemid;
		});
	};
	diy.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};
	diy.getParts = function(){
		diy.parts = {
			background: {
				name: '背景图',
				params: {
					name: '背景图',
					imgurl: '../addons/we7_wmall/plugin/spread/static/img/bg.jpg',
				},
				style: {'top': '0', 'left': '0', 'width': '100%', 'height': '100%', 'z-index': 0},
			},
			nickname: {
				name: '昵称',
				params: {
					name: '昵称',
				},
				style: {'top': '50', 'left': '95', 'width': '80', 'height': '40', 'fontsize': '16', 'color': '#000', 'z-index': 1},
			},
			avatar: {
				name: '头像',
				params: {
					avatar: '../addons/we7_wmall/plugin/spread/static/img/head.jpg',
				},
				style: {'top': '10', 'left': '10', 'width': '80', 'height': '80', 'z-index': 1},
			},
			image: {
				name: '图片',
				params: {
					imgurl: '../addons/we7_wmall/plugin/spread/static/img/image.jpg',
				},
				style: {'top': '10', 'left': '10', 'width': '80', 'height': '80', 'z-index': 1},
			},
			qrcode: {
				name: '二维码',
				params: {
					imgurl: '../addons/we7_wmall/plugin/spread/static/img/qrcode.jpg',
					type: "wechat",
				},
				style: {'top': '10', 'left': '10', 'width': '120', 'height': '120', 'z-index': 1},
			},
			text: {
				name: '文字',
				params: {'content' : '请输入文字内容'},
				style: {'color': '#000', 'fontsize' : '16', 'top': '10', 'left': '10', 'width': '120', 'height': '40', 'z-index': 1, 'content' : '请输入文字内容'}
			}
		}
	};
	diy.initItems = function(selected) {
		var preview = $("#app-preview");
		if(!diy.items) {
			diy.items = {};
			var item = $.extend(true, {}, diy.parts['background']);
			delete item.name;
			var itemid = diy.getId("M", 0);
			diy.items[itemid] = item;
		}
		preview.empty();
		$.each(diy.items, function(itemid, item) {
			if(typeof(item.id) !== 'undefined') {
				var newItem = $.extend(true, {}, item);
				newItem.itemid = itemid;
				var html = tmodtpl("tpl-show-" + item.id, newItem);
				preview.append(html);
				if( item.id != 'background') {
					var $obj = $('div[data-itemid="'+itemid+'"]');
					diy.dragEvent($obj);
				}
			}
		});
		if(selected) {
			diy.selectedItem(selected);
		}
	};

	diy.selectedItem = function(itemid){
		if(!itemid) {
			return;
		}
		diy.selected = itemid;
		if(itemid == 'page') {
			$("#page").trigger('click');
		} else {
			$(".drag[data-itemid='" + itemid + "']").addClass('selected');
		}
	};

	diy.initPage = function(initE) {
		if(typeof(initE) === 'undefined') {
			initE = true;
		}
		if(!diy.page) {
			diy.page = {
				type: diy.type,
				name: '海报名称',
				background: ''
			};
		}
		$("#page").text(diy.page.name);
		$("#app-preview").find(".drag").removeClass("selected");
		if(initE) {
			diy.initEditor();
		}
	};

	diy.initEditor = function() {
		var itemid = diy.selected;
		if(diy.selected) {
			if(diy.selected == 'page') {
				var html = tmodtpl("tpl-editor-page", diy);
				$("#app-editor .inner").html(html);
			} else {
				var item = $.extend(true, {}, diy.items[diy.selected]);
				item.itemid = diy.selected;
				var html = tmodtpl("tpl-editor-" + item.id, item);
				$("#app-editor .inner").html(html);
			}
			$("#app-editor").attr("data-editid", diy.selected).show();
		}

		var sliderlength = $("#app-editor .slider").length;
		if(sliderlength > 0) {
			$("#app-editor .slider").each(function(){
				var decimal = $(this).data('decimal');
				var multiply = $(this).data('multiply');
				var defaultValue = $(this).data("value");
				if(decimal) {
					defaultValue = defaultValue * decimal;
				}
				$(this).slider({
					slide: function(event, ui){
						var sliderValue = ui.value;
						if(decimal) {
							sliderValue = sliderValue / decimal;
						}
						$(this).siblings(".input").val(sliderValue).trigger("propertychange");
						$(this).siblings(".count").find("span").text(sliderValue);
					},
					value: defaultValue,
					min: $(this).data("min"),
					max: $(this).data("max")
				});
			});
		}
		$("#app-editor").find(".diy-bind").bind('input propertychange change', function(){
			var _this = $(this);
			var bind = _this.data("bind");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var initEditor = _this.data('bind-init');
			var value = '';
			var tag = this.tagName;
			if(!itemid) {
				diy.selectedItem('page');
			}
			if(tag == 'INPUT') {
				var type = _this.attr('type');
				var placeholder = _this.data('placeholder');
				value = _this.val();
				value = value == '' ? placeholder : value;
			}
			value = $.trim(value);
			if(itemid == 'page') {
				if(bindchild) {
					if(!diy.page[bindchild]) {
						diy.page[bindchild] = {};
					}
					diy.page[bindchild][bind] = value;
				} else {
					diy.page[bind] = value;
				}
				diy.initPage(false);
			} else {
				if(bindchild) {
					if(bindparent) {
						diy.items[itemid][bindparent][bindchild][bind] = value;
					} else {
						diy.items[itemid][bindchild][bind] = value;
					}
				} else {
					diy.items[itemid][bind] = value;
				}
				diy.initItems(itemid);
			}
			if(initEditor) {
				diy.initEditor(false);
			}
		})
	};
	diy.initTpl = function(){
		tmodtpl.helper("tomedia", function(src) {
			if(src.indexOf('images/') == 0) {
				return diy.attachurl + src;
			}
			if(typeof src != 'string') {
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0 || src.indexOf('../addons/we7_wmall/') == 0) {
				return src;
			} else if(src.indexOf('images/') == 0 || src.indexOf('audios/') == 0) {
				return diy.attachurl + src;
			}
		});
		tmodtpl.helper("count", function(data) {
			return diy.length(data);
		});
		tmodtpl.helper("toArray", function(data) {
			var oldArray = $.makeArray(data);
			var newArray = [];
			$.each(data, function(itemid, item) {
				newArray.push(item);
			});
			return newArray;
		});
		tmodtpl.helper("strexists", function(str, tag) {
			if(!str || !tag) {
				return false;
			}
			if(str.indexOf(tag) != -1){
				return true;
			}
			return false;
		});
		tmodtpl.helper("inArray", function(str, tag) {
			if(!str || !tag) {
				return false;
			}
			if(typeof(str) == 'string'){
				var arr = str.split(",");
				if($.inArray(tag, arr)>-1){
					return true;
				}
			}
			return false;
		});
		tmodtpl.helper("define", function(str) {
			var str;
		})
	};
	diy.initSave = function() {
		$(".btn-save").unbind('click').click(function() {
			var status = $(this).data('status');
			if (status) {
				Notify.error("正在保存，请稍候。。。");
				return;
			}
			diy.data = {page: diy.page, items: diy.items};
			if(!diy.page.name) {
				Notify.error("页面标题是必填项");
				$("#page").trigger("click");
				return;
			}
			$(".btn-save").data('status', 1).text("保存中...");
			irequire(['tiny'], function(tiny){
				$.post(tiny.getUrl('spread/postera/post'), {id: diy.id, data: diy.data}, function(ret) {
					var ret = ret.message;
					if(ret.errno != 0) {
						Notify.error(ret.message);
						$(".btn-save[data-type='save']").text("保存页面").data("status", 0);
						return;
					}
					Notify.success("保存成功！", ret.url);
				}, 'json');
			});
		});
	};
	diy.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0
		}
		var jsonlen = 0;
		for (var item in json) {
			jsonlen++
		}
		return jsonlen
	};
	return diy;
});