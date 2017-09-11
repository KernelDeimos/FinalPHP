<?php

namespace FinalPHP\Frameworks\Nano;

use \Aura\Router\RouterContainer;
use \Zend\Diactoros\ServerRequestFactory;

use \FinalPHP\L;

class Router
{
    /**
     * Router provides a basic router
     */
    function __construct($config)
    {
        // Ensure valid config
        L::AssertStruct($config, self::DEF_config());

        // Set defaults
        if ($config["controller_namespace"] == "") {
            $config["controller_namespace"] = "Controllers\\";
        }

        // Set config property
        $this->config = $config;

        // Initialize array of "sandwichwares"
        $this->sandwichwares = array();

        // Instantiate router container
        $this->routerContainer = new RouterContainer(
            $this->config['base_path']
        );
        $this->routerMap = $this->routerContainer->getMap();
    }

    function add_sandwichware($ware) {
        $this->sandwichwares[] = $ware;
    }

    function GET(...$args) {
        return $this->routerMap->get(...$args);
    }

    function POST(...$args) {
        return $this->routerMap->post(...$args);
    }

    function route($controllerAPI) {
        // Generate request object using zend-diactoros
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );

        // Process request with Aura.Router to get route
        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);

        if (! $route) {
            die("TODO: Implement this error based on case in ddev-framework");
        }

        // Add route attributes to the request
        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        // Create context object for controller
        $globals = ControllerContext::DEF_Globals();
        $context = new ControllerContext(
            $request,
            $route->attributes,
            $globals);

        // Determine controller class from route
        $class = NULL;
        {
            $ns = $this->config['controller_namespace'];
            $class = $ns.$route->handler;

            // Check if class exists
            if (class_exists($class) === false) {
                // This is a fatal error
                trigger_error("Handler class '".$route->handler."' not found",
                    E_USER_ERROR);
            }
        }

        // Execute "underware" on context
        foreach ($this->sandwichwares as $ware) {
            if (method_exists($ware, "before_handler")) {
                $ware->before_handler($context, $controllerAPI);
            }
        }

        // Instantiate and run controller
        $controller = new $class();
        $controller->handler($context, $controllerAPI);

        // Execute "overware" on context
        foreach ($this->sandwichwares as $ware) {
            if (method_exists($ware, "after_handler")) {
                $ware->after_handler($context, $controllerAPI);
            }
        }
    }

    public static function DEF_config() {
        return L::Struct(
            L::Prop("controller_namespace", "string"),
            L::Prop("base_path", "string"),
            L::END
        );
    }

}