<?php

namespace FinalPHP\Frameworks\Nano;

use FinalPHP\L;

class ControllerContext
{
    /**
     * ControllerContext provides the API which controllers will use.
     */
    function __construct($request, $params, $globals)
    {
        L::AssertStruct($globals, self::DEF_Globals());
        if ($globals['web_path'] == "")
        {
            $pattern = '/^'.preg_quote($_SERVER['DOCUMENT_ROOT'],'/').'/';
            $webpath = "http://".$_SERVER['HTTP_HOST'].preg_replace($pattern,'',getcwd());
            $globals['web_path'] = $webpath;
        }
        if ($globals['src_path'] == "")
        {
            $globals['src_path'] = getcwd();
        }

        // Add globals to instance
        $this->globals = $globals;

        // Add params to instance
        $this->params = $params;

        // Add request to instance
        $this->request = $request;

        // Initialize controller and vars
        $this->vars  = array(); // Variables set by sandwichwares
    }

    public static function DEF_Globals() {
        return L::Struct(
            L::Prop("web_path", "string"),
            L::Prop("src_path", "string"),
            L::END
        );
    }

    /**
     * global is a getter for variables provided to all controllers.
     */
    function global($key) {
        return $this->globals[$key];
    }

    /**
     * param is a getter for URL parameters.
     */
    function param($key)
    {
        return $this->$params[$key];
    }

    /**
     * get is a getter for variables set by sandwichwares.
     */
    function get($key)
    {
        return $this->vars[$key];
    }

    /**
     * set is a method for sandwichwares to expose variables.
     */
    function set($key, $val)
    {
        $this->vars[$key] = $val;
    }
}
