<?php
/*
 * (c) Leonardo Brugnara
 *
 * Full copyright and license information in LICENSE file.
 */

namespace Gekko\Config;

interface IConfigProvider
{
    function getConfig(string $name) : IConfigDriver;
}
