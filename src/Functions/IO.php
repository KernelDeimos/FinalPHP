<?php

namespace FinalPHP\Functions;

use \FinalPHP\L;

use Symfony\Component\Yaml\Yaml;

class IO
{
    public static function MarshalFiles($struct, ...$files)
    {
        // TODO: Infer JSON or YAML from file extension

        // Return unified struct with configuration from each file
        return L::Reduce(
            // To reduce: Parsed YAML from each file
            L::Map(
                // For each value in
                $files,
                // Map with the following functions
                "file_get_contents", array('\Symfony\Component\Yaml\Yaml', 'parse')
            ),
            // Reduce function: Apply parsed YAML to each struct state
            L::ReverseArgs(
                array('\FinalPHP\L', 'Marshal')
            ),
            // Reduce initialization: Empty struct
            $struct
        );

    }
}