<?php

namespace FinalPHP\Tmpl\PHPTmpl;

abstract class Template
{
    private $vars;

    abstract protected function do_render($vars);

    function Render() {
        echo $this->do_render($vars);
    }
}
