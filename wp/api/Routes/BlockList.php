<?php
/**
 * Endpoints for wp_block_list.
 *
 * @package   WPBlocks\WPBlocksApi
 * @author    Juliana Gonçalves
 */

namespace WPBlocks\WPBlocksApi\Routes;

class BlockList extends CustomPostMeta
{
    
    public function getItems($request)
    {
        $params = $request->get_params();
        $args = $this->queryArgs($params);

        return $this->doQuery($request, $args, $params);
    }

    protected function setData($post, $data, $found_posts, $params)
    {
        $filters = $this->getFilters($params);
        $permalink = get_the_permalink($post->ID);
        $attachments = $this->getPostImageSizes($post->ID, ["thumbnail", "medium", "large"]);
        $excerpt = $this->stripTagsFromExcerpt($post->post_excerpt);
        $terms = $this->getTerms($post->ID);

        $data["found_posts"] = $found_posts;
        $data["filters"] = $filters;
        $data["items"][] = [
            "ID" => $post->ID,
            "type" => $post->post_type,
            "title" => $post->post_title,
            "slug" => $post->post_name,
            "permalink" => $permalink,
            "date" => $post->post_date,
            "attachments" => $attachments,
            "taxonomies" => $terms,
        ];

        return $data;
    }

    protected function getFilters($params)
    {
        $termIds = [];
        $args = ["taxonomy" => "category"];

        if (!empty($params["taxonomy"])) {
            $args["taxonomy"] = $params["taxonomy"];
        }

        if (!empty($params["terms"])) {
            $terms = explode(",", preg_replace("/\s+/", "", $params["terms"]));

            foreach ($terms as $term) {
                $term = get_term_by("slug", $term, $params["taxonomy"]);
                array_push($termIds, $term->term_id);
            }

            $args["include"] = $termIds;
        }

        if (!empty($params["parent_term"])) {
            unset($args["include"]);
            $term = get_term_by("slug", $params["parent_term"], $params["taxonomy"]);
            $args["parent"] = $term->term_id;
        }

        return get_terms($args);
    }
}
