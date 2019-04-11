<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

class ConfigProvider implements IConfigProvider
{
    /**
     * Configuration driver
     *
     * @var \Gekko\Config\IConfigDriver
     */
    private $driver;

    /**
     * Configuration tree
     *
     * @var string[]
     */
    private $paths;

    /**
     * Current environment
     *
     * @var string
     */
    private $env;

    public function __construct(string $driver, string $env, string $configPath)
    {
        $this->driver = $driver;
        $this->env = $env;

        
        $configPath = \str_replace("\\", DIRECTORY_SEPARATOR, $configPath);
        $configPath = \str_replace("/", DIRECTORY_SEPARATOR, $configPath);
        
        if ($configPath[\strlen($configPath)-1] != DIRECTORY_SEPARATOR)
            $configPath .= DIRECTORY_SEPARATOR;

        $this->paths = [ $configPath ];

        $envPath = \explode(".", $this->env);
        
        foreach ($envPath as $path) {
            $configPath .= $path . DIRECTORY_SEPARATOR;
            $this->paths[] = $configPath;
        }
    }

    function getConfig(string $configName) : IConfigDriver
    {

        if ($this->driver == "php")
            return new PHPConfigDriver($configName, $this->paths);

        throw new \Exception("Unknown IConfigDriver {$this->driver}");
    }
}
