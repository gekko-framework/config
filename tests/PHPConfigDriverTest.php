<?php

use Gekko\Config\PHPConfigDriver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class PHPConfigDriverTest extends TestCase
{
    /**
     * @var \Gekko\Config\PHPConfigDriver
     */
    private $driver;

    public function setUp() : void
    {
        $fs = vfsStream::setup();

        // Create the base config directory
        mkdir("{$fs->url()}/config");
        mkdir("{$fs->url()}/config/dev");
        mkdir("{$fs->url()}/config/test");
        mkdir("{$fs->url()}/config/live");

        $section = '<?php return [ "version" => 1.0 ];';
        file_put_contents("{$fs->url()}/config/cfg.php", $section);

        $section = '<?php return [ "name" => "Development" ];';
        file_put_contents("{$fs->url()}/config/dev/cfg.php", $section);

        $section = '<?php return [ "name" => "Test" ];';
        file_put_contents("{$fs->url()}/config/test/cfg.php", $section);

        $section = '<?php return [ "name" => "Live" ];';
        file_put_contents("{$fs->url()}/config/live/cfg.php", $section);

        $this->driver = new PHPConfigDriver([
            "{$fs->url()}/config/", 
            "{$fs->url()}/config/dev/",
            "{$fs->url()}/config/test/",
            "{$fs->url()}/config/live/",
        ]);
    }

    public function test_loadConfigurationFileShouldReturnAValidConfigSection()
    {
        $cfg = $this->driver->loadConfigurationSection("cfg");
        
        $this->assertNotNull($cfg);
        $this->assertNotNull($cfg->version);
        $this->assertIsFloat($cfg->version);
        $this->assertNotNull($cfg->name);
        $this->assertIsString($cfg->name);
    }
}