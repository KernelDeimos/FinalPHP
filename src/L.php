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
     * CheckStruct checks that a structure matches a definition,
     * returning an error if it does not.
     */
    public static function CheckStruct($input, $struct) {
        // Ensure inputs are valid
        self::assert(self::is_struct_def($input),
            "L::CheckStruct: Input is not a L::Struct array");
        self::assert(self::is_struct_def($struct),
            "L::CheckStruct: Struct is not a L::Struct definition");

        // Iterate over keys in structure definition
        foreach ($struct as $key => $default_value) {
            // Skip if meta key
            if ($key === L::METAKEY) {
                continue;
            }
            // Ensure key exists in input data
            if (!array_key_exists($key, $input)) {
                return "Input did not match struct definition";
            }
            // Get expected type of value
            $expectedType = $struct[L::METAKEY][$key]['type'];
            $actualType = gettype($input[$key]);
            // Perform type assertion of expected type is trivial to test
            if (in_array(
                $expectedType,
                array('bool', 'float', 'int', 'string')
            )) {
                if ($actualType !== $expectedType) {
                    return "Type of $key was $actualType; " .
                        "expected $expectedType.";
                }
            }
            // Perform recursive call if expected type is another
            // L::Struct definition.
            if (gettype($expectedType) === "array") {
                if (array_key_exists(L::METAKEY, $expectedType)) {
                    $result = self::CheckStruct($input[$key], $expectedType);
                    if ($result !== NULL) {
                        return "In key $key: " . $result;
                    }
                }
            }
        }
        return NULL;
    }
    /**
     * AssertStruct ensures that a structure matches a definition
     */
    public static function AssertStruct($input, $struct) {
        $result = self::CheckStruct($input, $struct);
        if ($result !== NULL) {
            trigger_error($result, E_USER_ERROR);
        }
    }

    public static function Marshal($input, $struct) {
        list ($result, $err) = self::_marshal($input, $struct);
        if ($err !== NULL) {
            trigger_error('L::Marshal: '.$err, E_USER_ERROR);
        }
        return $result;
    }

    private static function _marshal($input, $struct) {
        // Ensure inputs are valid
        if (!is_array($input)) {
            return array(NULL, "Input is not an array");
        }
        if (!self::is_struct_def($struct)) {
            return array(NULL,
                "Struct is not a L::Struct definition");
        }

        // Ensure meta key exists
        foreach ($struct as $key => $default_value) {
            // Skip meta key
            if ($key === L::METAKEY) {
                continue;
            }
            if (array_key_exists($key, $input)) {
                // Check if this key is another L::Struct definition
                $expectedType = $struct[L::METAKEY][$key]['type'];
                if (self::is_struct_def($expectedType)) {
                    // Get the input value to compare to
                    $inVal = $input[$key];
                    // For this case, NULL is considered the same
                    // as an ampty array
                    if ($inVal === NULL) {
                        $inVal = array();
                    }
                    // List 
                    list ($result, $err) =
                        self::_marshal($inVal, $expectedType);
                    if ($err !== NULL) {
                        return array(NULL,"In $key: ".$err);
                    }
                    $struct[$key] = $result;
                } else {
                    $struct[$key] = $input[$key];
                }
            }
        }
        $err = self::CheckStruct($struct, $struct);
        return array($struct, $err);
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

    private static function is_struct_def($value) {
        if (gettype($value) === "array") {
            if (array_key_exists(L::METAKEY, $value)) {
                return true;
            }
        }
        return false;
    }

    private static function assert($check, $message) {
        if (!$check) {
            trigger_error($message, E_USER_ERROR);
        }
    }
}
