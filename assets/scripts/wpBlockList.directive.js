wpBlocksApp.directive("wpBlockList", ["wpBlocksWpApi", function(wpBlocksWpApi){

	var diretiva = {};
	diretiva.restrict = "E";
	diretiva.templateUrl = "wpBlockList.template.html";
	diretiva.scope = {};

	diretiva.link = function(scope, element, attrs) {

		var options = wpBlocksWpApi.decodeOptions(attrs.options);

		scope.allPosts = [];
		scope.filterPosts = filterPosts;
		scope.getNextPage = getNextPage;
		scope.gridCalc = gridCalc;
		scope.hasExcerpt = options.excerpt;
		scope.hasFilter = options.filter;
		scope.hasLogo = options.logo;
		scope.hasTags = options.tags;
		scope.hideTag = hideTag;
		scope.hideTags = options.hide_tags;
		scope.imgSize = options.image_size;
		scope.layout = options.layout;
		scope.layoutHeight = options.layout_height;
		scope.loadMore = (options.load_more && options.orderby !== "rand");
		scope.nextPage = 2;
		scope.postsList = [];
		scope.tagsFromTax = options.tags_from_tax;
		scope.terms = options.terms.split(",");

		getPosts(options, 1, options.offset);

		function calculatePages(postsCount, perPage){
			var pages = postsCount / parseInt(perPage),
				pagesCount = Math.round(pages);

			if(pages % 1 !== 0){
				pagesCount += 1;
			}

			return pagesCount;
		}

		function filterPosts(slug){

			jQuery(".wp-block-list__filter").removeClass("wp-block-list__filter--active");
			jQuery(".wp-block-list__filter--" + slug).addClass("wp-block-list__filter--active");

			scope.postsList = scope.allPosts;

			if(slug !== "all"){
				var filteredPosts = scope.postsList.filter(function(post){
					var hasTerm = false;
					angular.forEach(post.taxonomies, function(tax, i){
						if(tax.name == options.taxonomy){
							hasTerm = hasSlug(slug, tax.terms);
						}
					});
					return hasTerm;
				});

				scope.postsList = filteredPosts;
				$rootScope.$emit('lazyImg:refresh');
			}

		}

		function getNextPage(){
			scope.pages = calculatePages(scope.postsCount, options.per_page);

			if(Object.keys(scope.allPosts).length < scope.postsCount && scope.nextPage <= scope.pages){
				getPosts(options, scope.nextPage, options.offset);
				scope.nextPage = scope.nextPage + 1;
			}

		}

		function getSlugs(terms){
			var slugs = [];
			
			angular.forEach(terms, function(term, i){
				slugs.push(term.slug);
			});

			return slugs;
		}

		function getPosts(options, page, offset){
			var getPosts = wpBlocksWpApi.getPosts("block_list", options, page, offset);

			getPosts.then(function(response){ 
				handleSuccess(response);
			}, function(response){ 
				handleError(response);
			});
		}

		function gridCalc(index){
			index += 1;
			var sizeClass = "wp-block--",
				halfClass = [1, 2],
				oneThirdClass = [3, 5, 6, 7, 9],
				twoThirdsClass = [4, 8],
				reducedDigits = sumDigits(index);

				while(reducedDigits > 9){
					reducedDigits = sumDigits(reducedDigits);
				}

			if(halfClass.contains(reducedDigits)){

				return sizeClass + "half";

			} else if(oneThirdClass.contains(reducedDigits)){

				return sizeClass + "one-third";

			} else if(twoThirdsClass.contains(reducedDigits)){

				return sizeClass + "two-thirds";

			}
		}

		function handleError(reason){
			console.log(reason);
			scope.postsList( { data: [] });
		}

		function handleSuccess(response){
			var postsList = response.data.items;
			scope.postsCount = response.data.found_posts;
			scope.filters = response.data.filters;

			if(options.order === "DESC" && options.orderby === "date"){
				postsList = sortByDate(postsList);
			}

			scope.postsList = scope.postsList.concat(postsList);
			scope.allPosts = scope.postsList;
		}

		function hasSlug(slug, terms){
			var slugs = getSlugs(terms);
			
			return (slugs.contains(slug));
		}

		function hideTag(slug){
			return (scope.hideTags.indexOf(slug) > -1);
		}

		function sortByDate(obj){
			var postsArr = [];
			jQuery.each(obj, function(key, value){
				postsArr.push(value);
			});
			postsArr.sort(function(a, b) {
				return new Date(b.date).getTime() - new Date(a.date).getTime();
			});
			return postsArr;
		}

		function sumDigits(number){
			number = number.toString().split("");

			return number.reduce(function (a, b) {
				return parseInt(a) + parseInt(b);
			}, 0);
		}
		
	};

	return diretiva;
}]);