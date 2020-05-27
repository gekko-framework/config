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

    const DIRECTORY_SEPARATOR = '/';

    /**
     * Creates a new instance of the configuration provider using the provided settings
     * 
     * @param string $driver Driver name to consume the configuration sections
     * @param string $env Environment identifier. It can be separated by dots and each part represents
     * a nested directory in the configuration directory.
     * @param string $configPath The base path to the configuration directory
     */
    public function __construct(string $driver, string $env, string $configPath)
    {
        $this->env = $env;

        // Build all the configuration paths using the base config path
        // and the environment setting
        $configPath = \str_replace("\\", self::DIRECTORY_SEPARATOR, $configPath);
        $configPath = \str_replace("/", self::DIRECTORY_SEPARATOR, $configPath);
        
        if ($configPath[\strlen($configPath)-1] != self::DIRECTORY_SEPARATOR)
            $configPath .= self::DIRECTORY_SEPARATOR;

        $paths = [ $configPath ];

        if (isset($this->env[0]))
        {
            $envPath = \explode(".", $this->env);
            
            foreach ($envPath as $path) {
                $configPath .= $path . self::DIRECTORY_SEPARATOR;
                $paths[] = $configPath;
            }
        }

        $this->paths = $paths;
        $this->driver = $this->buildDriver($driver, $paths);
    }

    /**
     * @see \Gekko\Config\IConfigProvider
     */
    function getDriver(): \Gekko\Config\IConfigDriver
    {
        return $this->driver;
    }

    /**
     * @see \Gekko\Config\IConfigProvider
     */
    function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @see \Gekko\Config\IConfigProvider
     */
    function getConfig(string $sectionName) : IConfigSection
    {
        return $this->driver->loadConfigurationSection($sectionName);
    }

    private function buildDriver(string $driverName, array $paths) : IConfigDriver
    {
        if ($driverName == "php")
            return new PHPConfigDriver($paths);

        throw new \Exception("Unknown IConfigDriver {$driverName}");
    }
}
