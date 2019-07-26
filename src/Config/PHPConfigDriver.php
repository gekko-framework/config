<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

class PHPConfigDriver implements IConfigDriver
{
    private $config;

    public function __construct(string $configName, array $paths)
    {
        $this->config = [];
        foreach ($paths as $path) {
            $file = $path . $configName . ".php";

            if (!\file_exists($file))
                continue;

            $this->config = \array_replace_recursive($this->config, require $file);
        }
    }

    function has(string $name) : bool
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

    function get(string $name)
    {
        if (\array_key_exists($name, $this->config))
            return $this->config[$name];

        $tmp = $this->config;

        $paths = explode(".", $name);
        
        foreach ($paths as $path)
            $tmp = $tmp[$path];

        return $tmp;
    }

    function getKeys() : array
    {
        return \array_keys($this->config);
    }
}
