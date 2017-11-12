<?php

namespace FinalPHP\Tmpl\Base;

abstract class Template
{
    private $vars;
    public $data;

    function __construct() {
        $this->vars = array();
        $this->data = array();
    }

    abstract protected function do_render($vars);

    function Render() {
        echo $this->do_render($this->vars);
    }

    /*
     * @deprecated
     */
    function __set($key, $val) {
        $this->vars[$key] = $val;
    }

    function set($key, $val) {
        $this->vars[$key] = $val;
    }

    function aggregate($vars) {
        foreach ($vars as $key => $value) $this->vars[$key] = $value;
    }

    function __toString() {
        return $this->do_render($this->vars);
    }
}
