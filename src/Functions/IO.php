<?php

namespace FinalPHP\Functions;

use \FinalPHP\L;

use Symfony\Component\Yaml\Yaml;

class IO
{
    public static function MarshalFiles($struct, ...$files)
    {
        // TODO: Infer JSON or YAML from file extension
        return L::Reduce(
            // TODO: Map should be capable of variadic input instead of nesting
            // maps so that code like this is more readable.
            L::Map(
                L::Map($files, file_get_contents),
                array('Symfony\Component\Yaml\Yaml', 'parse')
            ),
            function ($structSoFar, $value) {
                return L::Marshal($value, $structSoFar);
            },
            $struct
        );

    }
}