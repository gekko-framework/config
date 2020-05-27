<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

interface IConfigSection
{
    /**
     * Check if a key exists in the configuration section.
     * 
     * @param string $name Key name
     * @return bool True if the key exists in the configuration section, otherwise false.
     */
    function hasKey(string $name) : bool;

    /**
     * Return an object associated to a key in the configuration section. If the key
     * is an associative array, the returned object is a {@see \Gekko\Config\IConfigSection}
     * to. If the key doesn't exist, this method must return null.
     * 
     * @param string $name Key name
     * @return mixed An object associated to the key or null if the key doesn't exist
     */
    function getValue(string $name);

    /**
     * Return all the keys in the configuration section.
     * 
     * @return string[] All the keys in the configuration section
     */
    function getKeys() : array;
}