<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

interface IConfigDriver
{
    /**
     * Load the {@see \Gekko\Config\IConfigSection} object for the provided section
     */
    function loadConfigurationSection(string $sectionName) : IConfigSection;
}
