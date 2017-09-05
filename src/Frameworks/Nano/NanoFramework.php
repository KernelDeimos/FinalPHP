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
    private function __construct($config)
    {
        L::AssertStruct($config, self::DEF_Config());

        $this->router = new Router($config['router']);
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

    public static function NewWithConfigFiles(...$files)
    {
        // Load contents of each configuration file
        $strings = array();
        foreach ($files as $file) {
            // TODO: error handling for file_get_contents
            $strings[] = file_get_contents($file);
        }

        // Initialize config with definition
        $config = self::DEF_Config();

        // Marshal file contents into config object,
        // allowing consequtive files to override parameters.
        foreach($strings as $string) {
            $parsed = Yaml::parse($string);
            $config = L::Marshal($parsed, $config);
        }

        return new NanoFramework($config);
    }

    public static function DEF_Config() {
        return L::Struct(
            L::Prop("router", Router::DEF_Config()),
            L::END
        );
    }

    function get_router() {
        return $this->router;
    }

    function go() {
        $this->router->route();
    }

}
