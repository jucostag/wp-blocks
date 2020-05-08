wpBlocksApp.config(["$httpProvider", "lazyImgConfigProvider", function ($httpProvider, lazyImgConfigProvider){
	$httpProvider.interceptors.push("httpInterceptor");

	lazyImgConfigProvider.setOptions({
		successClass: "wp-block__image--loaded fadeIn",
		onSuccess: handleSuccess
	});

	function handleSuccess(image){
		if(image){
			jQuery(image.$elem.context).siblings().addClass("wp-block--image-loaded");
		}
	}
}]);