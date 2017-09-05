<?php

namespace FinalPHP;

class L {
    const END = "__fphp_end";

    const METAKEY = "__fphp_meta";

    public static function Struct(...$props) {
        // $data represents the default initialization of the struct defined
        // using this language tool.
        $data = array();
        $data[L::METAKEY] = array();

        // Loop over properties
        foreach ($props as $prop) {
            // Skip if ending constant
            if ($prop === L::END) {
                continue;
            }
            // Missing array key zero is a fatal error
            if (!array_key_exists(0, $prop)) {
                trigger_error("[FinalPHP] [Lang] Property must have name",
                    E_USER_ERROR);
            }
            // Set name, and default values
            $name = $prop[0];
            $type = "any";
            $tags = array();
            // Set type and tags if corresponding array keys exist
            if (array_key_exists(1, $prop)) {
                $type = $prop[1];
            }
            if (array_key_exists(2, $prop)) {
                $tags = $prop[2];
            }
            // Get value associated with specified type (NULL if any)
            $value = L::get_default_value_of_type($type);
            // Set corresponding key of default initialization to this value
            $data[$name] = $value;
            // Add type and tag to special L::METAKEY key of default initialization
            $data[L::METAKEY][$name] = array(
                "tags" => $tags,
                "type" => $type);
        }
        return $data;
    }

    public static function Prop($name, $type, ...$tags) {
        $prop = array($name, $type, $tags);
        return $prop;
    }

    /**
     * AssertStruct ensures that a structure matches a definition
     */
    public static function AssertStruct($input, $struct) {
        // Iterate over keys in structure definition
        foreach ($struct as $key => $default_value) {
            // Skip if meta key
            if (prop === L::METAKEY) {
                continue;
            }
            // Ensure key exists in input data
            if (!array_key_exists($key, $input)) {
                trigger_error("Input did not match struct definition",
                    E_USER_ERROR);
            }
        }
    }

    private static function get_default_value_of_type($type) {
        switch ($type) {
            case 'bool':
                return false;
            case 'float':
                return 0.0;
            case 'int':
                return 0;
            case 'string':
                return "";
            default:
                // Anything else, including arrays, callables, and classes,
                // should by initialized to NULL.
                return NULL;
        }
    }
}
