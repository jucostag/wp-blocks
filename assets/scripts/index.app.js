var wpBlocksModules = ["angularLazyImg", "lazy-scroll"].filter(function (module){
		try {
			return !!angular.module(module);    
		} catch (e) {}
	});

var wpBlocksApp = angular.module("wpBlocksApp", wpBlocksModules);