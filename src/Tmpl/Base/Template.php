<?php

namespace FinalPHP\Tmpl\Base;

abstract class Template
{
    private $vars;

    function __construct() {
        //
    }

    abstract protected function do_render($vars);

    function Render() {
        echo $this->do_render($this->vars);
    }

    function __set($key, $val) {
        $this->vars[$key] = $val;
    }

    function __toString() {
        return $this->do_render($vars);
    }
}
