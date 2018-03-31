define(['tiny'], function(tiny) {
	var index = {};
	index.init = function(params) {
		require(['map'], function(AMap){
			var map = new AMap.Map('com-map', {
				resizeEnable: true,
				center: [params['map'].location_y, params['map'].location_x],
				zoom: 11
			});
			map.plugin('AMap.Geolocation', function() {
				geolocation = new AMap.Geolocation({
					enableHighAccuracy: true,//是否使用高精度定位，默认:true
					showMarker: true,      //定位成功后在定位到的位置显示点标记，默认：true
					showButton: false
				});
				geolocation.getCurrentPosition();
				map.addControl(geolocation);
				AMap.event.addListener(geolocation, 'complete', function(){});//返回定位信息
				map.setFitView();
			});

			$.post(tiny.getUrl('errander/index/deliveryer'), function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != -1) {
					$.each(result.message.message, function(k, v){
						var deliveryer = v.deliveryer;
						if(deliveryer.location_x && deliveryer.location_y) {
							marker = new AMap.Marker({
								position: [deliveryer.location_y, deliveryer.location_x],
								offset: new AMap.Pixel(-26, -80),
								content: '<div class="marker-deliveyer-route"><img src="'+ v.deliveryer.avatar +'" alt=""/></div>'
							});
							marker.setMap(map);
						}
					});
				}
			});
		});
	};
	return index;
});