<?php

namespace FinalPHP\Frameworks\Nano;

use \FinalPHP\L;

use Symfony\Component\Yaml\Yaml;

class NanoFramework
{
    
    /**
     * NanoFramework implements a very minimal framework using the Aura
     * router.
     */
    function __construct($config)
    {
        echo "=====<pre>";
        var_dump($config);
        echo "</pre>";

        L::AssertStruct($config, self::DEF_Config());
    }

    public static function NewWithConfigString($yamlString)
    {
        $parsed = Yaml::parse($yamlString);
        $config = L::Marshal($parsed, self::DEF_Config());
        return new NanoFramework($config);
    }

    public static function NewWithConfigFile($yamlFile)
    {
        return self::NewWithConfigString(file_get_contents($yamlFile));
    }

    public static function DEF_Config() {
        return L::Struct(
            L::Prop("router", Router::DEF_Config()),
            L::END
        );
    }
}
