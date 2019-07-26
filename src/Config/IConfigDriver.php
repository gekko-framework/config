<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

interface IConfigDriver
{
    function has(string $name) : bool;
    function get(string $name);
    function getKeys() : array;
}
