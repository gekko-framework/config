<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

interface IConfigProvider
{
    /**
     * Returns the configuration driver
     * 
     * @return \Gekko\Config\IConfigDriver Configuration driver
     */
    function getDriver() : \Gekko\Config\IConfigDriver;

    /**
     * Returns an array with all the paths that may contain 
     * configuration files
     * 
     * @return string[] Paths to configuration directories
     */
    function getPaths() : array;

    /**
     * Returns a configuration section
     * 
     * @param string $sectionName Configuration section name
     * @return \Gekko\Config\IConfigSection Configuration section
     */
    function getConfig(string $sectionName) : IConfigSection;
}
