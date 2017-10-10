<?php

namespace FinalPHP\Tmpl\IncludeTmpl;

class Template extends \FinalPHP\Tmpl\Base\Template
{
    function __construct($filename) {
        $this->filename = $filename;
    }
    function do_render($vars) {
        foreach ($vars as $key => $value) $$key = $value;
        include($filename);
    }
}
