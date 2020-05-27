<?php

use Gekko\Config\ConfigProvider;
use Gekko\Config\IConfigSection;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ConfigSectionTest extends TestCase
{
    /**
     * @var \Gekko\Config\ConfigProvider
     */
    private $configProvider;

    public function setUp() : void
    {
        $fs = vfsStream::setup();

        // Create the base config directory
        mkdir("{$fs->url()}/config");

        $console_config = '
        <?php
            return [
                "version" => "1.0.0",
                "bin" => [
                    "php-server"    => Gekko\Console\PHP\ServerCommand::class,
                    "php-cgi"       => Gekko\Console\PHP\FastCGICommand::class,
                ]
            ];
        ';

        file_put_contents("{$fs->url()}/config/console.php", $console_config);

        // Create the dev directory for the dev environment
        mkdir("{$fs->url()}/config/dev");

        $env_console_config = '
        <?php
            return [
                "bin" => [
                    "nginx"         => Gekko\Console\Nginx\ServerCommand::class,
                ]
            ];
        ';

        file_put_contents("{$fs->url()}/config/dev/console.php", $env_console_config);

        // Create the config provider
        $this->configProvider = new ConfigProvider("php", "dev", "{$fs->url()}/config");
    }

    public function test_hasKeyMethodShouldFindKeys()
    {
        $consolecfg = $this->configProvider->getConfig("console");
        
        $this->assertTrue($consolecfg->hasKey("bin"));

        $this->assertTrue($consolecfg->hasKey("version"));

        $this->assertFalse($consolecfg->hasKey("lib"));
    }

    public function test_magicGetIsAShorthandForGetValue()
    {
        $consolecfg = $this->configProvider->getConfig("console");
        
        $this->assertEquals($consolecfg->bin, $consolecfg->getValue("bin"));

        $this->assertEquals($consolecfg->version, $consolecfg->getValue("version"));
    }

    public function test_getValueMethodShouldReturnMixedObjects()
    {
        $consolecfg = $this->configProvider->getConfig("console");

        $bin = $consolecfg->getValue("bin");
        $this->assertInstanceOf(IConfigSection::class, $bin);
        
        $this->assertTrue($bin->hasKey('php-server'));
        $this->assertTrue($bin->hasKey('php-cgi'));
        $this->assertTrue($bin->hasKey('nginx'));

        $this->assertEquals('Gekko\Console\PHP\ServerCommand', $bin->getValue('php-server'));
        $this->assertEquals('Gekko\Console\PHP\FastCGICommand', $bin->getValue('php-cgi'));
        $this->assertEquals('Gekko\Console\Nginx\ServerCommand', $bin->getValue('nginx'));

        $version = $consolecfg->getValue("version");
        $this->assertIsString($version);
        $this->assertEquals("1.0.0", $version);

        $this->assertNull($consolecfg->getValue("missing"));
    }

    public function test_getKeysMethodShouldReturnAllTheKeys()
    {
        $consolecfg = $this->configProvider->getConfig("console");

        $this->assertEquals([ "version", "bin" ], $consolecfg->getKeys());
    }
}