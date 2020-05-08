<?php
/**
 * Endpoints for wp_block.
 *
 * @package   WPBlocks\WPBlocksApi
 * @author    Juliana Gonçalves
 */

namespace WPBlocks\WPBlocksApi\Routes;

class Block extends CustomPostMeta
{

    public function getItems($request)
    {
        $params = $request->get_params();
        $args = $this->queryArgs($params);
        return $this->doQuery($request, $args, $params);
    }

    protected function setData($post, $data, $found_posts, $params)
    {
        $permalink = get_the_permalink($post->ID);
        $attachments = $this->getPostImageSizes($post->ID, ["thumbnail", "medium", "large"]);
        $excerpt = $this->stripTagsFromExcerpt($post->post_excerpt);
        
        $data["post"][] = [
            "ID" => $post->ID,
            "type" => $post->post_type,
            "title" => $post->post_title,
            "slug" => $post->post_name,
            "permalink" => $permalink,
            "date" => $post->post_date,
            "attachments" => $attachments,
            "excerpt" => $excerpt,
        ];
        
        return $data;
    }
}
