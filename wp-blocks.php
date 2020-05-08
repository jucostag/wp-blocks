<?php

/**
* Plugin Name: WP Block Style
* Plugin URI: https://github.com/jucostag/wp-block-style
* Description: This plugin adds a block-style to your content of any type, via shortcode.
* Version: 1.0.0
* Author: Juliana Gonçalves
* License: GPL
* Author URI: https://github.com/jucostag
*/

define("WPBLOCKSURL", WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__)));

include("wp/taxonomies/blocksTaxonomy.php");
include("wp/functions.php");
include("wp/shortcodes/wpBlock.php");
include("wp/shortcodes/wpBlockList.php");

add_action("plugins_loaded", "wpBlocksApiInit");
function wpBlocksApiInit()
{
    spl_autoload_register(function ($class) {
        $prefix = "WPBlocks\\WPBlocksApi\\";
        $base_dir = dirname(__FILE__) . "/wp/api/";
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";

        if (file_exists($file)) {
            require $file;
        }
    });

    $apiName = "wpblocks_api";
    $version = "v2";
    new \WPBlocks\WPBlocksApi\Boot($apiName, $version);
}

add_action("include_assets_wp_block", "wpBlockShortcodeAssets");

function wpBlockShortcodeAssets()
{
    $assets = ["animate", "angular-lazy-img"];
    wpBlocksAssets($assets);
}

add_action("include_assets_wp_block_list", "wpBlockListShortcodeAssets");
function wpBlockListShortcodeAssets()
{
    $assets = ["animate", "angular-lazy-img", "lazy-scroll"];
    wpBlocksAssets($assets);
}

function wpBlocksAssets($assets)
{
    if (in_array("animate", $assets)) {
        wpBlocksEnqueueDeps("animate.min.css", plugins_url("/node_modules/animate.css/animate.min.css", __FILE__));
    }
    if (in_array("angular-lazy-img", $assets)) {
        wpBlocksEnqueueDeps("angular-lazy-img.min.js", plugins_url("/assets/scripts/vendor/angular-lazy-img/release/angular-lazy-img.min.js", __FILE__));
    }
    if (in_array("lazy-scroll", $assets)) {
        wpBlocksEnqueueDeps("lazy-scroll.min.js", plugins_url("/assets/scripts/vendor/lazy-scroll.min.js", __FILE__));
    }
    wp_enqueue_style("wp_blocks_min_css", plugins_url("/assets/css/wpBlocks.min.css", __FILE__));
    wp_enqueue_script("wp_blocks_min_js", plugins_url("/assets/js/wpBlocks.min.js", __FILE__), ['angular.js'], false);
}

function wpBlocksEnqueueDeps($file, $path)
{
    $type = pathinfo($file, PATHINFO_EXTENSION);

    if (!wpBlocksIsFileEnqueued($type, $file) && $type === "css") {
        wp_enqueue_style($file, $path);
    } elseif (!wpBlocksIsFileEnqueued($type, $file) && $type === "js") {
        wp_enqueue_script($file, $path, ['angular.js'], false);
    }
}

function wpBlocksIsFileEnqueued($type, $file)
{
    global $wp_scripts;
    global $wp_styles;

    $enqueues = $wp_scripts->registered;

    if ($type === "css") {
        $enqueues = $wp_styles->registered;
    }

    foreach ($enqueues as $enqueue) {
        if (strpos($enqueue->src, $file) !== false) {
            return true;
        }
    }
    return false;
}
