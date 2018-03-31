$.fn.iappend = function(html, callback) {
	var len = $("body").html().length;
	this.append(html);
	var e = 1, interval = setInterval(function() {
		e++;
		var clear = function() {
			clearInterval(interval);
			callback && callback();
		};
		if (len != $("body").html().length || e > 1e3) {
			clear();
		}
	}, 1);
};
$.modal.prototype.defaults.closePrevious = true;

+function($) {
	"use strict";
	var timeout;
	$.toptip = function(text, duration, type) {
		if(!text) return;
		if(typeof duration === typeof "a") {
			type = duration;
			duration = undefined;
		}
		duration = duration || 3000;
		var className = type ? 'bg-' + type : 'bg-danger';
		var $t = $('.wui-toptips').remove();
		var isHasHeader = $('header.bar').size() > 0 ? 1 : 0;
		$t = $('<div class="wui-toptips"></div>').appendTo(document.body);
		$t.html(text);
		$t[0].className = 'wui-toptips ' + className
		if(isHasHeader) {
			$t.css({top: '49px'});
		}

		clearTimeout(timeout);

		if(!$t.hasClass('wui-toptips-visible')) {
			$t.show().width();
			$t.addClass('wui-toptips-visible');
		}
		timeout = setTimeout(function() {
			$t.removeClass('wui-toptips-visible').transitionEnd(function() {
				$t.remove();
			});
		}, duration);
	}
}($);

+function ($) {
	"use strict";
	$.toast = function(msg, url, time) {
		var $toast = $("<div class='modal toast'>"+msg+"</div>").appendTo(document.body);
		$.openModal($toast);
		setTimeout(function() {
			$.closeModal($toast);
			if(url) {
				location.href = url;
				return false;
			}
		}, time || 2000);
	};

	$.iopenModal = function (modal, cb) {
		var defaults = $.modal.prototype.defaults;
		if(defaults.closePrevious) $.closeModal();
		modal = $(modal);
		var isModal = modal.hasClass('modal');
		if ($('.modal.modal-in:not(.modal-out)').length && defaults.modalStack && isModal) {
			$.modalStack.push(function () {
				$.iopenModal(modal, cb);
			});
			return;
		}
		var isPopover = modal.hasClass('popover');
		var isPopup = modal.hasClass('popup');
		var isLoginScreen = modal.hasClass('login-screen');
		var isPickerModal = modal.hasClass('picker-modal');
		var isToast = modal.hasClass('toast');
		if (isModal) {
			modal.show();
			modal.css({
				marginTop: - Math.round(modal.outerHeight() / 2) + 'px'
			});
		}
		if (isToast) {
			modal.show();
			modal.css({
				marginLeft: - Math.round(parseInt(window.getComputedStyle(modal[0]).width) / 2)  + 'px' //
			});
		}

		var overlay;
		if (!isLoginScreen && !isPickerModal && !isToast) {
			if ($('.modal-overlay').length === 0 && !isPopup) {
				$(defaults.modalContainer).append('<div class="modal-overlay"></div>');
			}
			if ($('.popup-overlay').length === 0 && isPopup) {
				$(defaults.modalContainer).append('<div class="popup-overlay"></div>');
			}
			overlay = isPopup ? $('.popup-overlay') : $('.modal-overlay');
		}

		//Make sure that styles are applied, trigger relayout;
		var clientLeft = modal[0].clientLeft;

		// Trugger open event
		modal.trigger('open');

		// Picker modal body class
		if (isPickerModal) {
			$(defaults.modalContainer).addClass('with-picker-modal');
		}

		// Classes for transition in
		if (!isLoginScreen && !isPickerModal && !isToast) overlay.addClass('modal-overlay-visible');
		modal.removeClass('modal-out').addClass('modal-in').transitionEnd(function (e) {
			if (modal.hasClass('modal-out')) modal.trigger('closed');
			else modal.trigger('opened');
		});
		if (typeof cb === 'function') {
			cb.call(this);
		}
		return true;
	};

	$.icloseModal = function(modal, notRemove){
		if(notRemove) {
			$('.modal-overlay').removeClass('modal-overlay-visible');
			$(modal).removeClass('modal-in').addClass('modal-out').transitionEnd(function(e) {
				$(this).removeClass('modal-out');
				$(this).hide();
			});
			return true;
		}
		$.closeModal(modal);
		return true;
	};

	function handleClicks(e) {
		/*jshint validthis:true */
		var defaults = $.modal.prototype.defaults;
		var clicked = $(this);
		var url = clicked.attr('href');

		//Collect Clicked data- attributes
		var clickedData = clicked.dataset();

		// Popover
		if (clicked.hasClass('open-popover')) {
			var popover;
			if (clickedData.popover) {
				popover = clickedData.popover;
			}
			else popover = '.popover';
			$.popover(popover, clicked);
		}
		if (clicked.hasClass('close-popover')) {
			$.closeModal('.popover.modal-in');
		}

		// Popup
		var popup;
		if (clicked.hasClass('open-popup')) {
			if (clickedData.popup) {
				popup = clickedData.popup;
			}
			else popup = '.popup';
			$.popup(popup);
		}
		if (clicked.hasClass('close-popup')) {
			if (clickedData.popup) {
				popup = clickedData.popup;
			}
			else popup = '.popup.modal-in';
			$.closeModal(popup);
		}
		// Close Modal
		if (clicked.hasClass('modal-overlay')) {
			if ($('.modal.modal-in').length > 0 && defaults.modalCloseByOutside) {
				$.icloseModal('.modal.modal-in', true);
			}
			if ($('.actions-modal.modal-in').length > 0 && defaults.actionsCloseByOutside)
				$.closeModal('.actions-modal.modal-in');

			if ($('.popover.modal-in').length > 0) $.closeModal('.popover.modal-in');
		}

		if (clicked.hasClass('popup-overlay')) {
			if ($('.popup.modal-in').length > 0 && defaults.popupCloseByOutside)
				$.closeModal('.popup.modal-in');
		}
	}

	$(function() {
		$(document).off('click', ' .modal-overlay, .popup-overlay, .close-popup, .open-popup, .open-popover, .close-popover, .close-picker');
		$(document).on('click', ' .modal-overlay, .popup-overlay, .close-popup, .open-popup, .open-popover, .close-popover, .close-picker', handleClicks);
		$.modal.prototype.defaults.modalContainer = $.modal.prototype.defaults.modalContainer || document.body;  //incase some one include js in head
	});
}($);

+ function($) {
	"use strict";
	var defaults;
	var Select = function(container, config) {
		var self = this;
		this.config = config;
		this.$container = $(container);
		this.$input = $(container).find('input:hidden.select-value');
		this.$input.prop("readOnly", true);
		this.initConfig();
		config = this.config;
		this.$container.click($.proxy(this.open, this));
	}

	Select.prototype.initConfig = function() {
		this.config = $.extend({}, defaults, this.config);
		var config = this.config;
		if(!config.items || !config.items.length) return;
		config.items = config.items.map(function(d, i) {
			if(typeof d == typeof "a") {
				return {
					title: d,
					value: d
				};
			}
			return d;
		});
		this.tpl = $.t7.compile("<div class='picker-modal picker-columns select-modal'>" + config.toolbarTemplate + (config.multi ? config.checkboxTemplate : config.radioTemplate) + "</div>");
		if(config.input !== undefined) this.$input.val(config.input);
		this.parseInitValue();
	}

	Select.prototype.updateInputValue = function(values, titles) {
		var v, t;
		if(this.config.multi) {
			v = values.join(this.config.split);
			t = titles.join(this.config.split);
		} else {
			v = values[0];
			t = titles[0];
		}
		//caculate origin data
		var origins = [];
		this.config.items.forEach(function(d) {
			values.each(function(i, dd) {
				if(d.value == dd) origins.push(d);
			});
		});
		this.$input.val(v);
		this.$input.attr("value", v);
		this.$input.prev('span').html(t);
		this.$input.next('input').html(t);
		this.$input.next('input').val(t);
		var data = {
			values: v,
			titles: t,
			origins: origins
		};
		this.$input.trigger("change", data);
		this.config.onChange && this.config.onChange.call(this, data);
	}

	Select.prototype.parseInitValue = function() {
		var value = this.$input.val();
		var items = this.config.items;
		if(value === undefined || value == null || value === "") return;

		var titles = this.config.multi ? value.split(this.config.split) : [value];
		for(var i=0;i<items.length;i++) {
			items[i].checked = false;
			for(var j=0;j<titles.length;j++) {
				if(items[i].value == titles[j]) {
					items[i].checked = true;
				}
			}
		}
	}

	//更新数据
	Select.prototype.update = function(config) {
		this.config = $.extend({}, this.config, config);
		this.initConfig();
		if(this._open) {
			$.updatePicker(this.getHTML());
		}
	}

	Select.prototype.open = function(values, titles) {
		if(this._open) return;
		this.parseInitValue();
		var config = this.config;
		var dialog = this.dialog = $.pickerModal(this.getHTML(), $.proxy(this.onClose, this));
		var self = this;
		dialog = $(dialog);
		dialog.on("change", function(e) {
			var checked = dialog.find("input:checked");
			var values = checked.map(function() {
				return $(this).val();
			});
			var titles = checked.map(function() {
				return $(this).data("title");
			});
			self.updateInputValue(values, titles);
			if(config.autoClose && !config.multi) self.close();
		});

		dialog.on("close", function(e) {
			self._open = false;
		});

		this._open = true;
		if(config.onOpen) config.onOpen(this);
	}

	Select.prototype.close = function(callback) {
		var self = this;
		$.closeModal();
		self.onClose();
		callback && callback();
	}

	Select.prototype.onClose = function() {
		this._open = false;
		if(this.config.onClose) this.config.onClose(this);
	}

	Select.prototype.getHTML = function(callback) {
		var config = this.config;
		return this.tpl({
			items: config.items,
			title: config.title,
			closeText: config.closeText
		})
	}

	$.fn.select = function(params, args) {
		return this.each(function() {
			var $this = $(this);
			if(!$this.data("light7-select")) $this.data("light7-select", new Select(this, params));

			var select = $this.data("light7-select");

			if(typeof params === typeof "a") select[params].call(select, args);

			return select;
		});
	}

	defaults = $.fn.select.prototype.defaults = {
		items: [],
		input: undefined, //输入框的初始值
		title: "请选择",
		multi: false,
		closeText: "关闭",
		autoClose: true, //是否选择完成后自动关闭，只有单选模式下才有效
		onChange: undefined, //function
		onClose: undefined, //function
		onOpen: undefined, //function
		split: ",",  //多选模式下的分隔符
		toolbarTemplate: '<header class="bar bar-nav">\
          <button class="button button-link pull-right close-picker">{{closeText}}</button>\
          <h1 class="title">{{title}}</h1>\
          </header>',
		radioTemplate:
			'<div class="list-block media-list">\
				<ul>\
					{{#items}}\
					<li>\
						<label class="label-checkbox item-content">\
						<div class="item-inner">\
							<div class="item-title">{{this.title}}</div>\
						</div>\
						<input type="radio" name="light7-select" id="wui-select-id-{{this.title}}" value="{{this.value}}" {{#if this.checked}}checked="checked"{{/if}} data-title="{{this.title}}">\
							<div class="item-media"><i class="icon icon-form-checkbox"></i></div>\
						</label>\
					</li>\
					{{/items}}\
				</ul>\
			</div>',

		checkboxTemplate:
			'<div class="list-block media-list">\
					<ul>\
						{{#items}}\
						<li>\
							<label class="label-checkbox item-content">\
							<div class="item-inner">\
								<div class="item-title">{{this.title}}</div>\
							</div>\
							<input type="checkbox" name="light7-select" id="wui-select-id-{{this.title}}" value="{{this.value}}" {{#if this.checked}}checked="checked"{{/if}} data-title="{{this.title}}">\
								<div class="item-media"><i class="icon icon-form-checkbox"></i></div>\
							</label>\
						</li>\
						{{/items}}\
					</ul>\
				</div>'
		}

}($);
var timeStamp = new Date().getTime();

$(function(){
	$(document).on("click", "a", function(e) {
		var $target = $(e.currentTarget);
		if($target[0].hasAttribute("external") ||
			$target.hasClass("tab-link") ||
			$target.hasClass("open-popup") ||
			$target.hasClass("open-panel") ||
			$target.hasClass("js-post")
			) return;
		if($target.hasClass("back")) {
			sessionStorage.setItem(
				window.pageId,
				$pageId.find('.content').scrollTop()
			);
			window.history.go(-1);
			return;
		}
		if($target.hasClass("refresh")) {
			location.reload();
			return;
		}
		var url = $target.attr("href");
		if(!url || url === "#" || /javascript:.*;/.test(url) || /tel:.*/.test(url) || tiny.ish5app()) return;
		if(pageId) {
			sessionStorage.setItem(
				window.pageId,
				$pageId.find('.content').scrollTop()
			);
		}
		if($.device.android == true) {
			$.showIndicator();
		}
	})
});