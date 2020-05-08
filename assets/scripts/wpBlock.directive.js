wpBlocksApp.directive("ngBlock", ["wpBlocksWpApi", "$filter", function(ngBlocksWpApi, $filter){

    var diretiva = {};
    diretiva.restrict = "E";
    diretiva.templateUrl = "wpBlock.template.html";
    diretiva.scope = {};

    diretiva.link = function(scope, element, attrs) {

        var options = wpBlocksWpApi.decodeOptions(attrs.options);

        scope.options = options;
        scope.layout = options.layout;
        scope.layoutHeight = options.layout_height;
        scope.imgSize = options.image_size;
        scope.hasExcerpt = isTrue(options.excerpt);

        getPost(options, options.offset);

        function getPost (options, offset){
            var getPost = wpBlocksWpApi.getPosts("block", options, 1, offset);

            getPost.then(function(response){ 
                handleSuccess(response);
            }, function(response){ 
                handleError(reason);
            });
        }

        function handleError (reason){
            console.log(reason);
            scope.post({ data: [] });
        }
        
        function handleSuccess (response){
            scope.post = response.data.post[0];
            scope.excerpt = scope.post.excerpt;
        }


        function isTrue (option){
            return (option == 1);
        }

    };

    return diretiva;
}]);