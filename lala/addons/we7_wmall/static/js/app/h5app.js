define(['tiny', '//static.ydbimg.com/API/YdbOnline.js'], function(tiny) {
	var h5app = {};
	h5app.init = function(params) {
		h5app.shareinfo = params.share ? params.share : null;
		h5app.relation = params.relation ? params.relation : null;
		h5app.backurl = params.backurl;
		h5app.payinfo = params.payinfo;
		h5app.initYDB();
		h5app.set();
		h5app.sns();
		h5app.initOther();
	};

	h5app.initYDB = function() {
		window.YDB = h5app.YDB = new YDBOBJ();
	};

	h5app.initOther = function() {
		$("#btn-share").unbind("click").click(function() {
			h5app.share();
		});
		if(h5app.relation) {
			h5app.YDB.SetUserRelationForPush(h5app.relation);
		}
	};

	h5app.set = function() {
		//h5app.YDB.SetStatusBarStyle(0);
	};

	h5app.share = function() {
		if(!h5app.share) {
			$.toast("分享参数错误");
			return;
		}
		h5app.YDB.Share(h5app.share.title, h5app.share.desc, h5app.share.imgUrl, h5app.share.link);
	};

	h5app.sns = function() {
		$(".btn-sns").click(function() {
			var sns = $(this).data("sns");
			if (sns == "wx") {
				$.toast("正在呼起微信");
				h5app.YDB.WXLogin(0, tiny.getUrl("wmall/auth/account/sns", {
					sns: "wx",
					backurl: h5app.backurl
				}, true));
			} else {
				if (sns == "qq") {
					$.toast("正在呼起手机QQ");
					h5app.YDB.QQLogin(tiny.getUrl("wmall/auth/account/sns", {
						sns: "qq",
						backurl: h5app.backurl
					}, true));
				} else {
					return;
				}
			}
		});
	};

	h5app.pay = function(app, ordersn, money, status, callback) {
		var ordersn = ordersn ? ordersn : h5app.payinfo.ordersn;
		var money = money ? money : h5app.payinfo.money;
		if(!h5app.payinfo || !h5app.payinfo.mallName) {
			$.toast("支付信息不完善");
			return;
		}
		if(!ordersn) {
			$.toast("订单号有误");
			return;
		}
		if(money <= 0) {
			$.toast("支付金额有误");
			return;
		}
		if(app == "wechat") {
			$.toast("正在呼起微信");
			h5app.YDB.SetWxpayInfo(h5app.payinfo.mallName, h5app.payinfo.desc, money, ordersn, h5app.payinfo.attach);
		}
		if(app == "alipay") {
			$.toast("正在呼起支付宝");
			h5app.YDB.SetAlipayInfo(h5app.payinfo.mallName, h5app.payinfo.attach, money, ordersn);
		}
		$(".btn-pay").removeAttr("submit");
		$(".pay-btn").removeAttr("stop");
		if(status) {
			h5app.payStatus();
		}
		if(callback) {
			callback();
		}
	};

	h5app.payStatus = function() {
		var paytype = h5app.payinfo.type;
		var url = tiny.getUrl("system/paycenter/check");
		var data = {
			id: h5app.payinfo.order_id
		};
		if(paytype == 'takeout') {
			var url_return = tiny.getUrl("wmall/order/index/detail", {
				id: h5app.payinfo.order_id
			});
		} else if(paytype == 'errander') {
			var url_return = tiny.getUrl("errander/order/detail", {
				id: h5app.payinfo.order_id
			});
		} else if(paytype == 'deliveryCard') {
			var url_return = tiny.getUrl("deliveryCard/index", {
				id: h5app.payinfo.order_id
			});
		}
		var settime = setInterval(function() {
			$.getJSON(url, data, function(ret) {
				if (ret.status >= 1) {
					clearInterval(settime);
					location.href = url_return;
				} else {}
			});
		}, 1e3);
	};
	window.h5app = h5app;
	return h5app;
});