<?php

use Gekko\Config\ConfigProvider;
use Gekko\Config\IConfigSection;
use Gekko\Config\PHPConfigDriver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function test_getDriverReturnsTheDriverObject()
    {
        $cp1 = new ConfigProvider("php", "dev", "config/");
        $this->assertInstanceOf(PHPConfigDriver::class, $cp1->getDriver());
    }

    public function test_getPathsReturnsAllTheEnvironmentConfigurationDirectories()
    {
        $cp1 = new ConfigProvider("php", "dev", "config/");
        $this->assertEquals([ "config/", "config/dev/" ], $cp1->getPaths());

        $cp2 = new ConfigProvider("php", "dev.gekko", "config/");
        $this->assertEquals([ "config/", "config/dev/", "config/dev/gekko/" ], $cp2->getPaths());

        $cp3 = new ConfigProvider("php", "dev.gekko.s1", "config/");
        $this->assertEquals(
            [ "config/", "config/dev/", "config/dev/gekko/", "config/dev/gekko/s1/" ], 
            $cp3->getPaths());

        $cp4 = new ConfigProvider("php", "dev.gekko.s1", "path/to/config/");
            $this->assertEquals(
                [ "path/to/config/", "path/to/config/dev/", 
                  "path/to/config/dev/gekko/", 
                  "path/to/config/dev/gekko/s1/" ], 
                $cp4->getPaths());

        $cp5 = new ConfigProvider("php", "", "config/");
        $this->assertEquals([ "config/" ], $cp5->getPaths());
    }

    public function test_getConfigReturnsAConfigSection()
    {
        $cp1 = new ConfigProvider("php", "dev", "config/");
        
        $consoleConfig = $cp1->getConfig("console");
        $this->assertInstanceOf(IConfigSection::class, $consoleConfig);

        $dbConfig = $cp1->getConfig("database");
        $this->assertInstanceOf(IConfigSection::class, $dbConfig);
    }

    public function test_getConfigThrowsExceptionOnUnknownDriver()
    {
        $this->expectException(\Exception::class);
        $cp = new ConfigProvider("unknown", "dev", "config/");
    }

    public function test_envParameterMustChangePaths()
    {
        $fs = vfsStream::setup();

        // Create the base config directory
        mkdir("{$fs->url()}/config");
        mkdir("{$fs->url()}/config/dev");
        mkdir("{$fs->url()}/config/test");
        mkdir("{$fs->url()}/config/live");

        $basecfg = '<?php return [ "version" => 1.0 ];';
        $devcfg = '<?php return [ "name" => "Development" ];';
        $testcfg = '<?php return [ "name" => "Test" ];';
        $livecfg = '<?php return [ "name" => "Live" ];';

        file_put_contents("{$fs->url()}/config/cfg.php", $basecfg);
        file_put_contents("{$fs->url()}/config/dev/cfg.php", $devcfg);
        file_put_contents("{$fs->url()}/config/test/cfg.php", $testcfg);
        file_put_contents("{$fs->url()}/config/live/cfg.php", $livecfg);

        // Create the config provider
        $bcp = (new ConfigProvider("php", "base", "{$fs->url()}/config"))->getConfig("cfg");
        $this->assertTrue($bcp->hasKey("version"));
        $this->assertEquals(1.0, $bcp->getValue("version"));

        $dcp = (new ConfigProvider("php", "dev", "{$fs->url()}/config"))->getConfig("cfg");
        $this->assertTrue($dcp->hasKey("version"));
        $this->assertTrue($dcp->hasKey("name"));
        $this->assertEquals("Development", $dcp->getValue("name"));

        $tcp = (new ConfigProvider("php", "test", "{$fs->url()}/config"))->getConfig("cfg");
        $this->assertTrue($tcp->hasKey("version"));
        $this->assertTrue($tcp->hasKey("name"));
        $this->assertEquals("Test", $tcp->getValue("name"));

        $lcp = (new ConfigProvider("php", "live", "{$fs->url()}/config"))->getConfig("cfg");
        $this->assertTrue($lcp->hasKey("version"));
        $this->assertTrue($lcp->hasKey("name"));
        $this->assertEquals("Live", $lcp->getValue("name"));
    }
}