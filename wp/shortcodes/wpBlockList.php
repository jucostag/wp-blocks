<?php
add_shortcode("wp-block-list", "wpBlockList");
function wpBlockList($userAttrs, $content = null)
{
    $attributes = wpBlocksExtractAttrs(wpBlockListSetAttrs(), $userAttrs);
    
    $attributes["terms"] = preg_replace('/\s+/', '', $attributes["terms"]);
    $attributes["hide_terms"] = explode(",", preg_replace('/\s+/', '', $attributes["hide_terms"]));
    $attributes["show_tags"] = wpBlocksIsTrue($attributes["show_tags"]);
    $attributes["show_logo"] = wpBlocksIsTrue($attributes["show_logo"]);
    $attributes["show_filter"] = wpBlocksIsTrue($attributes["show_filter"]);

    $shortcodeOptions = http_build_query($attributes);
    $svgLoader = wpBlocksGetSvgLoader();

    ob_start();
    do_action("include_assets_ng_block_list");
    $template = require(dirname(__FILE__) . "/wpBlockList.template.phtml");
    return str_replace(["\r", "\n", "\t"], '', trim(ob_get_clean()));
}

function wpBlockListSetAttrs()
{
    return [
        "type" => "any",
        "taxonomy" => "",
        "terms" => "",
        "parent_term" => "",
        "per_page" => "",
        "offset" => 0,
        "order" => "DESC",
        "orderby" => "date",
        "logo" => false,
        "filter" => false,
        "tags" => false,
        "hide_tags"=> "",
        "tags_from_tax" => "",
        "layout" => "splash",
        "layout_height" => "large",
        "image_size" => "large",
        "load_more" => false,
        "excerpt" => false,
    ];
}
