<?php

namespace FinalPHP\Frameworks\Nano;

/**
 * Route is a wrapper for a Route object returned by Aura.Router upon calling
 * .get() or .post() on an Aura.Router Map object.
 */
class Route
{
    function __construct($auraRoute) {
        $this->auraRoute = $auraRoute;

        $this->sandwichwares = array();

        $this->extras = array();
        $this->extras['tags'] = array();

        $this->_update();
    }

    function get_aura() {
        return $this->auraRoute;
    }

    function tags(...$tags) {
        foreach ($tags as $tag) {
            $this->extras['tags'][] = $tag;
        }
        $this->_update();
    }

    function _update() {
        $this->extras['sandwichwares'] = &$this->sandwichwares;
        $this->auraRoute->extras($this->extras);
    }

    function set($key, $val) {
        $this->extras[$key] = $val;
        $this->_update();
    }

    function add_sandwichware($ware) {
        $this->sandwichwares[] = $ware;
    }
}