wpBlocksApp.provider("wpBlocksWpApi", function(){
    var provider = {};

    provider.$get = ["$http", "$q", function($http, $q){
        var wpBlocksWpApi = {};
        
        wpBlocksWpApi.getPosts = getPosts;
        wpBlocksWpApi.decodeOptions = decodeOptions;

        function buildParam(option, param, validate){
            if(validate){
                return (option !== "") ? param + "=" + option : "";
            }
            
            return param + "=" + option;
        }

        function buildUrl(params, endpoint){
            url = "/wp-json/wpblocks_api/v2/" + endpoint;
            angular.forEach(params, function(p, i){
                var prefix = (p !== "") ? "&" : "";
                
                if(i === 0){
                    prefix = "?";
                }

                url += prefix + p;
            });

            return url;
        }

        function decodeOptions(params){
            
            if(params){
                params = params.split("&");
                params = params.reduce(function(prev, curr, i, arr) {
                    var p = curr.split("=");
                    prev[decodeURIComponent(p[0])] = decodeURIComponent(p[1]);
                    return prev;
                }, {});

                return replaceWhiteSpaces(params);
            }

            return null;
        }

        function doHttpRequest(url){
            var promise = $http.get(url),    
                deferObject = deferObject || $q.defer();

            promise.then(
                function (response){
                    deferObject.resolve(response);
                },
                function (reason){
                    deferObject.reject(reason);
                }
            );

            return deferObject.promise;
        }

        function getPosts(endpoint, options, page, offset){
            page = buildParam(page, "page", false);
            options.offset = parseInt(options.offset);
            offset = (options.offset >= 1 && offset >= 1) ? buildParam(offset, "offset", false) : "offset=0";

            var loadMore = options.load_more,
                postType = buildParam(options.type, "post_type", false),
                taxonomy = buildParam(options.taxonomy, "taxonomy", true),
                slug = buildParam(options.slug, "terms", true),
                terms = buildParam(options.terms, "terms", true),
                parentTerm = buildParam(options.parent_term, "parent_term", true),
                perPage = buildParam(options.per_page, "per_page", false),
                order = buildParam(options.order, "order", false),
                orderby = buildParam(options.orderby, "orderby", false),
                params = [postType, taxonomy, slug, perPage, offset, order, orderby],
                apiUrl = "";

            if(endpoint === "block_list"){
                params = [postType, taxonomy, terms, parentTerm, order, orderby, perPage];

                if(loadMore){
                    params.push(page);
                }

            }
            apiUrl = buildUrl(params, endpoint);

            return doHttpRequest(apiUrl);
        }

        function replaceWhiteSpaces(obj){
            return angular.forEach(obj, function(p, i){
                if(p.indexOf(",") !== -1){
                    obj[i] = obj[i].replace(/\+/g, "");
                }
            });
        }

        return wpBlocksWpApi;
    }];

    return provider;
});