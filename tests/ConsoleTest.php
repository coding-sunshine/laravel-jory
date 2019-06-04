<?php

namespace JosKolenberg\LaravelJory\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class ConsoleTest extends TestCase
{
    /** @test */
    public function dummy()
    {
        // Disabled this tests because they fail on scrutinizer-ci.
        // Keep them for local testing.
        $this->assertTrue(true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanup();
    }

    /** @test */
    public function it_can_run_a_generate_for_command_1()
    {
        $this->artisan('jory:generate-for', ['model' => 'JosKolenberg\LaravelJory\Tests\Models\Band']);

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Original'));
        $expectedContents = $filesystem->read('BandJoryResource.php');

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Generated'));
        $realContents = $filesystem->read('BandJoryResource.php');

        $this->assertEquals($expectedContents, $realContents);
    }

    /** @test */
    public function it_can_run_a_generate_for_command_with_name_option()
    {
        $this->artisan('jory:generate-for', ['model' => 'JosKolenberg\LaravelJory\Tests\Models\Band', '--name' => 'AlternateBandJoryResource']);

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Original'));
        $expectedContents = $filesystem->read('AlternateBandJoryResource.php');

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Generated'));
        $realContents = $filesystem->read('AlternateBandJoryResource.php');

        $this->assertEquals($expectedContents, $realContents);
    }

    /** @test */
    public function it_can_run_a_make_jory_resource_command()
    {
        $this->artisan('make:jory-resource', ['name' => 'EmptyJoryResource']);

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Original'));
        $expectedContents = $filesystem->read('EmptyJoryResource.php');

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Generated'));
        $realContents = $filesystem->read('EmptyJoryResource.php');

        $this->assertEquals($expectedContents, $realContents);
    }

    /** @test */
    public function it_can_run_a_make_jory_resource_command_with_related_model()
    {
        $this->artisan('make:jory-resource', ['name' => 'AlternateBandJoryResource', '--model' => 'JosKolenberg\LaravelJory\Tests\Models\Band']);

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Original'));
        $expectedContents = $filesystem->read('AlternateBandJoryResource.php');

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Generated'));
        $realContents = $filesystem->read('AlternateBandJoryResource.php');

        $this->assertEquals($expectedContents, $realContents);
    }

    /** @test */
    public function it_can_run_a_generate_all_command()
    {
        $this->artisan('jory:generate-all');

        $filesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Original'));

        $generatedFilesystem = new Filesystem(new Local(__DIR__ . '/ConsoleOutput/Generated'));

        $this->assertEquals($filesystem->read('AlbumJoryResource.php'), $generatedFilesystem->read('AlbumJoryResource.php'));
        $this->assertEquals($filesystem->read('BandJoryResource.php'), $generatedFilesystem->read('BandJoryResource.php'));
        $this->assertEquals($filesystem->read('PersonJoryResource.php'), $generatedFilesystem->read('PersonJoryResource.php'));

        $this->assertTrue($generatedFilesystem->has('AlbumCoverJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('AlbumJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('BandJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('ErrorPersonJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('GroupieJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('InstrumentJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('ModelJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('PersonJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('SongJoryResource.php'));
        $this->assertTrue($generatedFilesystem->has('SongWithAfterFetchHookJoryResource.php'));
        $this->assertFalse($generatedFilesystem->has('NonExistingJoryResource.php'));
    }

    protected function cleanup()
    {
        // Remove all previously built JoryResources
        $adapter = new Local(__DIR__ . '/ConsoleOutput');
        $filesystem = new Filesystem($adapter);
        $filesystem->deleteDir('Generated');
    }
}