wpBlocksApp.filter("imgByResolution", function(){
	return function(obj, layout, imgSize) {
		if(obj){
			var img = "",
				smallLayouts = ["card"],
				largeLayouts = ["splash", "cover"],
				isMobile = {
				Responsive: function() {
					var windowWidth = (window.screen.width < window.outerWidth) ? window.screen.width : window.outerWidth,
						maxMobile = 604;
					return windowWidth <= maxMobile;
				},
				Android: function() {
					return navigator.userAgent.match(/Android/i);
				},
				BlackBerry: function() {
					return navigator.userAgent.match(/BlackBerry/i);
				},
				iOS: function() {
					return navigator.userAgent.match(/iPhone|iPad|iPod/i);
				},
				Opera: function() {
					return navigator.userAgent.match(/Opera Mini/i);
				},
				Windows: function() {
					return navigator.userAgent.match(/IEMobile/i);
				},
				any: function() {
					return (isMobile.Responsive() || isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
				}
			};

			switch(true){
				case(smallLayouts.includes(layout) && isMobile.any() && imgSize !== "large"):
					img = obj.thumbnail;
					break;
				case(smallLayouts.includes(layout) && isMobile.any() && imgSize == "large"):
					img = obj.medium;
					break;
				case(largeLayouts.includes(layout) && isMobile.any()):
					img = obj.medium;
					break;
				default:
					img = obj[imgSize];
			}

			return img;
		}
	};
});