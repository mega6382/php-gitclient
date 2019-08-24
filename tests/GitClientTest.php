<?php
declare(strict_types=1);

use GitClient\ConfigurationException;
use GitClient\Configurations;
use GitClient\File;
use PHPUnit\Framework\TestCase;
use GitClient\GitClient;

final class GitClientTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $config = new Configurations();
        $config->setRepository("https://github.com/mega6382/HabWpLogin");
        $config->setLogFile("README.md");
        $this->client = new GitClient($config);

        $this->assertEquals(true, $this->client->validate());
        $this->assertEquals(true, $this->client->connect());
    }

    public function testSuccessful(): void
    {
        $files = $this->client->getNextFile();
        $this->assertIsIterable($files);

        $this->assertContainsOnlyInstancesOf(File::class, $files);


    }

    public function testLogFiles(): void
    {
        $files = $this->client->getLogFile();
        $this->assertIsIterable($files);

        $this->assertContainsOnlyInstancesOf(File::class, $files);
    }


    public function testFailure(): void
    {
        $this->expectException(ConfigurationException::class);

        $config = new Configurations();
        $client = new GitClient($config);
        $client->checkout();

    }

    protected function tearDown(): void
    {
        $this->assertEquals(true, $this->client->disconnect());
    }
}