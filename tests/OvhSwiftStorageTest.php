<?php

namespace Hedii\LaravelOvhSwiftStorage\Tests;

use Hedii\LaravelOvhSwiftStorage\OvhSwiftStorageServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use RuntimeException;

class OvhSwiftStorageTest extends TestCase
{
    public function tearDown(): void
    {
        foreach (Storage::allDirectories() as $directory) {
            Storage::deleteDirectory($directory);
        }

        foreach (Storage::allFiles() as $file) {
            Storage::delete($file);
        }

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [OvhSwiftStorageServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration());
        $app['config']->set('filesystems.default', 'ovh-swift');
    }

    /** @test */
    public function it_should_put_a_file_in_the_swift_container(): void
    {
        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertSame($content, file_get_contents(Storage::url($fileName)));
    }

    /** @test */
    public function it_should_set_a_prefix_if_needed(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration(['prefix' => 'foo']));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertStringEndsWith("foo/{$fileName}", Storage::url($fileName));
    }

    /** @test */
    public function it_should_not_set_a_prefix_if_prefix_is_null(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration(['prefix' => null]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertStringEndsWith(
            "{$this->getOvhSwiftConfiguration()['containerName']}/{$fileName}",
            Storage::url($fileName)
        );
    }

    /** @test */
    public function it_should_not_create_a_public_file_if_visibility_is_not_public(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This disk does not support retrieving URLs');

        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration(['visibility' => 'private']));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        Storage::url($fileName);
    }

    protected function getOvhSwiftConfiguration(array $config = []): array
    {
        $baseConfig = [
            'driver' => 'ovh-swift',
            'authUrl' => env('OVH_SWIFT_OPENSTACK_AUTH_URL'),
            'region' => env('OVH_SWIFT_OPENSTACK_REGION'),
            'projectId' => env('OVH_SWIFT_OPENSTACK_PROJECT_ID'),
            'containerName' => env('OVH_SWIFT_CONTAINER_NAME'),
            'prefix' => env('OVH_SWIFT_PREFIX'),
            'username' => env('OVH_SWIFT_OPENSTACK_USERNAME'),
            'password' => env('OVH_SWIFT_OPENSTACK_PASSWORD'),
            'visibility' => env('OVH_SWIFT_VISIBILITY'),
        ];

        return array_merge($baseConfig, $config);
    }

    protected function randomFileName(): string
    {
        return Str::random() . '.txt';
    }

    protected function randomContent(): string
    {
        return Str::random();
    }
}
