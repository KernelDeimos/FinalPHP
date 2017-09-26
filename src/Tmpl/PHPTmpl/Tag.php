<?php

namespace FinalPHP\Tmpl\PHPTmpl;

class Tag {

    const TAG_TYPE_DOUBLE = "normal";
    const TAG_TYPE_SINGLE = "single";
    const TAG_TYPE_RETRO = "retro";

    private $name;
    private $attributes;
    private $children;
    private $tagType;
    
    function __construct($name, $attrs = NULL, $children = NULL, $type = NULL)
    {
        if ($attrs    === NULL) $attrs    = array();
        if ($children === NULL) $children = array();

        if (!is_array($children)) {
            $children = array($children);
        }

        $this->name = $name;
        $this->attributes = $attrs;
        $this->children = $children;

        if ($type === NULL) {
            $this->autosetType();
        } else {
            $this->tagType = $type;
        }
    }

    function AppendChild($child)
    {
        $this->children[] = $child;
    }

    function InsertChild($child, $pos=0)
    {
        array_splice($this->children, $pos, 0, $child);
    }

    function GetChildren()
    {
        return $this->children;
    }

    function autosetType()
    {
        $name = $this->name;

        switch (true) {
            case in_array($name, array('img','link','meta')):
                $this->tagType = self::TAG_TYPE_RETRO;
                break;
            
            default:
                $this->tagType = self::TAG_TYPE_DOUBLE;
                break;
        }
    }

    function __toString()
    {
        ob_start();

        // Beginning of tag
        echo "<".$this->name;

        // Add a space if there are attributes
        if (count($this->attributes) > 0) echo " ";

        // Generate key="value" strings
        $attrStrings = array();
        foreach ($this->attributes as $key => $value) {
            $attrStrings[] = $key.'="'.$value.'"';
        }

        // Output attributes section
        echo implode(" ", $attrStrings);

        // For regular double-type tag, display children
        if ($this->tagType === self::TAG_TYPE_DOUBLE) {
            // Close open tag
            echo ">";
            // Render child elements
            $this->renderChildren();
            // Display end tag
            echo "</".$this->name.">";
        }
        // Otherwise, render single tag ending
        else {
            echo ($this->tagType == self::TAG_TYPE_SINGLE) ? ">" : " />";
        }

        return ob_get_clean();
    }

    function renderChildren() {
        foreach ($this->children as $child) {
            // Child will be string, or implementor of __toString magic method
            echo $child;
        }
    }
}