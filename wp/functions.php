<?php
function wpBlocksIsTrue($val)
{
    return filter_var($val, FILTER_VALIDATE_BOOLEAN);
}

function wpBlocksTaxCompatibility($tax)
{
    return ($tax == "category_name") ? "category" : $tax;
}

function wpBlocksExtractAttrs($attrs, $userAttrs)
{
    return shortcode_atts($attrs, $userAttrs);
}

function wpBlocksGetSvgLoader()
{
    return (object)[
        "path" => NGBLOCKSURL . "/assets/svg/loader.svg",
        "style" => "position: absolute; padding-bottom: 70px; max-width: 100px;",
    ];
}
