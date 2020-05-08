<?php
/**
 * Base class for custom post meta.
 *
 * @package   WPBlocks\WPBlocksApi
 * @author    Juliana Gonçalves
 */

namespace WPBlocks\WPBlocksApi\Routes;

class CustomPostMeta extends Endpoints
{

    public function __construct()
    {
        $this->taxonomies = get_taxonomies();
        $this->postTypes = get_post_types();
        $this->postMetaPrefix = "_ess";
    }

    protected function getPostImageSizes($postId, $sizes)
    {
        $images = [];

        foreach ($sizes as $size) {
            $images[$size] = $this->getImageSrc($postId, null, $size);
        }

        return $images;
    }

    private function getImageSrc($postId, $attachmentId = null, $size = "medium")
    {
        if (null === $attachmentId) {
            $attachmentId = get_post_thumbnail_id($postId);
            if (!$attachmentId) {
                return "";
            }
        }

        $_image = wp_get_attachment_image_src($attachmentId, $size);
        if ($_image) {
            return array_shift($_image);
        }
    }

    protected function stripTagsFromExcerpt($excerpt)
    {
        return strip_tags($excerpt);
    }

    protected function getTerms($postId)
    {
        $terms = [];

        foreach ($this->taxonomies as $tax) {
            $taxTerms = get_the_terms($postId, $tax);

            if (!empty($taxTerms)) {
                array_push($terms, ["name" => $tax, "terms" => $taxTerms]);
            }
        }

        return $terms;
    }
}