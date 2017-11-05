<?php

namespace FinalPHP\Tmpl\PHPTmpl;

abstract class Template extends \FinalPHP\Tmpl\Base\Template
{
    abstract protected function generate($nodes, $vars);

    function do_render($vars) {
        $doc = Gen::Document();
        $children = $doc->GetChildren();
        $nodes = array();
        $nodes['head'] = $children[0];
        $nodes['body'] = $children[1];
        $nodes['html'] = $doc;
        $this->generate($nodes, $vars);
        return $doc;
    }
}
