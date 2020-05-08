<?php
/**
 * Boot the API and add the routes.
 *
 * @package   WPBlocks\WPBlocks_api
 * @author    Juliana Gonçalves
 */

namespace WPBlocks\WPBlocksApi;

use WPBlocks\WPBlocksApi\Routes\Block;
use WPBlocks\WPBlocksApi\Routes\BlockList;

class Boot
{

    public function __construct($apiName, $version)
    {
        $this->root = $apiName;
        $this->version = $version;
        add_action("rest_api_init", array($this, "setRoutes"));
    }

    public function setRoutes()
    {
        $wpBlockEndpoint = "block";
        $wpBlockClass = new Block("{$this->root}/{$this->version}/$wpBlockEndpoint");

        $wpBlockListEndpoint = "block_list";
        $wpBlockListClass = new BlockList("{$this->root}/{$this->version}/$wpBlockListEndpoint");

        $this->registerRoutes($wpBlockEndpoint, $wpBlockClass);
        $this->registerRoutes($wpBlockListEndpoint, $wpBlockListClass);
    }

    public function registerRoutes($endpoint, $class)
    {
        register_rest_route("{$this->root}/{$this->version}", "/{$endpoint}", [
                [
                    "methods" => \WP_REST_Server::READABLE,
                    "callback" => [$class, "getItems"],
                    "args" => [
                        "per_page" => [
                            "default" => 10,
                            "sanitize_callback" => "absint",
                        ],
                        "page" => [
                            "default" => 1,
                            "sanitize_callback" => "absint",
                        ]
                    ],
                    "permission_callback" => (function () { return true; })
                ],
            ]
        );
    }
}