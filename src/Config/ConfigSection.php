<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

class ConfigSection implements IConfigSection
{
    /**
     * @var array An associative array containing the configuration
     */
    private $config;

    /**
     * The constructor receives an array that contains the whole configuration
     * section
     * 
     * @param array $config Associative array containing the configuration values
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Shorthand for the {@see \Gekko\Config\IConfigSection::getValue} method
     */
    function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * @see \Gekko\Config\IConfigSection
     */
    function hasKey(string $name) : bool
    {
        if (\array_key_exists($name, $this->config))
            return true;

        $tmp = $this->config;

        $paths = explode(".", $name);
        
        foreach ($paths as $path)
        {
            if (!isset($tmp[$path]))
                return false;

            $tmp = $tmp[$path];
        }

        return true;
    }

    /**
     * @see \Gekko\Config\IConfigSection
     */
    function getValue(string $name)
    {
        if (\array_key_exists($name, $this->config))
            return is_array($this->config[$name]) ? new ConfigSection($this->config[$name]) : $this->config[$name];

        $tmp = $this->config;

        $paths = explode(".", $name);
        
        foreach ($paths as $path)
        {
            if (!isset($tmp[$path]))
                return null;

            $tmp = $tmp[$path];
        }

        if ($tmp === null)
            return null;

        return is_array($tmp) ? new ConfigSection($tmp) : $tmp;
    }

    /**
     * @see \Gekko\Config\IConfigSection
     */
    function getKeys() : array
    {
        return \array_keys($this->config);
    }
}
