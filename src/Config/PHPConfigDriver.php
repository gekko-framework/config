<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

class PHPConfigDriver implements IConfigDriver
{
    private $paths;

    /**
     * @param array $paths Array with all the directories that may contain a configuration section
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @see \Gekko\Config\IConfigDriver
     */
    function loadConfigurationSection(string $sectionName) : IConfigSection
    {
        $config = [];
        foreach ($this->paths as $path) {
            $file = $path . $sectionName . ".php";

            if (!\file_exists($file))
                continue;

            $config = \array_replace_recursive($config, require $file);
        }

        return new ConfigSection($config);
    }
}
