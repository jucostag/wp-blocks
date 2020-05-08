<?php
/**
 * Base class for the endpoints.
 *
 * @package   WPBlocks\WPBlocksApi
 * @author    Juliana Gonçalves
 */

namespace WPBlocks\WPBlocksApi\Routes;

abstract class Endpoints extends \WP_REST_Posts_Controller
{

    public function __construct($base)
    {
        $this->postType = "any";
        $this->base = $base;
    }

    protected function queryArgs($params)
    {
        $postType = $this->postType;
        if ($this->paramIsValid($params["post_type"])) {
            $postType = $this->paramToArray($params["post_type"]);
        }

        if ($this->paramIsValid($params["per_page"]) && $params["per_page"] == 1) {
            $args["no_found_rows"] = true;
        }

        $args = [
            "posts_per_page" => $params["per_page"],
            "post_type"      => $postType,
            "order"             => $params["order"],
            "orderby"        => $params["orderby"],
            "paged"             => $params["page"]
        ];

        if ($this->paramIsValid($params["taxonomy"])) {
            $terms = $this->paramToArray($params["terms"]);
            if ($this->paramIsValid($params["parent_term"])) {
                $terms = $this->paramToArray($params["parent_term"]);
            }

            if (!empty($terms)) {
                $args["tax_query"] = [
                    [
                        "taxonomy" => $params["taxonomy"],
                        "field" => "slug",
                        "terms" => $terms,
                    ],
                ];
            }
        }

        if ($this->paramIsValid($params["offset"])) {
            unset($args["paged"]);
            $args["offset"] = $params["offset"];
        }

        return $args;
    }

    protected function doQuery($request, $args, $params, $respond = true)
    {
        $postsQuery  = new \WP_Query();
        $queryResult = $postsQuery->query($args);
        $foundPosts  = $postsQuery->found_posts;
        $data = [];

        if (!empty($queryResult)) {
            foreach ($queryResult as $post) {
                $data = $this->setData($post, $data, $foundPosts, $params);
            }
        }

        if ($respond) {
            return $this->createResponse($request, $args, $data);
        }
        return $data;
    }

    protected function createResponse($request, $args, $data)
    {
        $response = rest_ensure_response($data);
        $countQuery = new \WP_Query();
        unset($args["paged"]);
        $queryResult = $countQuery->query($args);
        $totalPosts = $countQuery->found_posts;
        $response->header("X-WP-Total", (int) $totalPosts);
        
        if (0 == (int) $request["per_page"]) {
            $maxPages = 1;
        } else {
            $maxPages = ceil($totalPosts / $request["per_page"]);
        }

        $response->header("X-WP-TotalPages", (int) $maxPages);

        if ($request["page"] > 1) {
            $prevPage = $request["page"] - 1;
            if ($prevPage > $maxPages) {
                $prevPage = $maxPages;
            }
            $prevLink = add_query_arg("page", $prevPage, rest_url($this->base));
            $response->link_header("prev", $prevLink);
        }

        if ($maxPages > $request["page"]) {
            $nextPage = $request["page"] + 1;
            $nextLink = add_query_arg("page", $nextPage, rest_url($this->base));
            $response->link_header("next", $nextLink);
        }

        return $response;
    }

    protected function paramIsValid($param)
    {
        return (isset($param) && !empty($param));
    }

    protected function paramToArray($param)
    {
        return explode(",", $param);
    }
}
