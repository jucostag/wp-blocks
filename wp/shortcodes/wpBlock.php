<?php
add_shortcode("wp-block", "wpBlock");
function wpBlock($userAttrs, $content = null)
{
    $attributes = wpBlocksExtractAttrs(wpBlockSetAttrs(), $userAttrs);

    $attributes["per_page"] = 1;
    $attributes["taxonomy"] = wpBlocksTaxCompatibility($attributes["taxonomy"]);
    $attributes["excerpt"] = wpBlocksIsTrue($attributes["excerpt"]);

    $shortcodeOptions = http_build_query($attributes);
    $svgLoader = wpBlocksGetSvgLoader();

    ob_start();
    do_action("include_assets_wp_block");
    $template = require(dirname(__FILE__) . "/wpBlock.template.phtml");
    return str_replace(["\r", "\n", "\t"], '', trim(ob_get_clean()));
}

function wpBlockSetAttrs()
{
    return [
        "type" => "post",
        "slug" => "",
        "offset" => "0",
        "taxonomy" => "",
        "order" => "DESC",
        "orderby" => "post_date",
        "image_size" => "medium",
        "layout" => "splash",
        "layout_height" => "medium",
        "excerpt" => false,
        "charlimit" => "",
    ];
}
