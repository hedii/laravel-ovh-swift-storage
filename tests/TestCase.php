<?php

namespace Hedii\LaravelOvhSwiftStorage\Tests;

use Hedii\LaravelOvhSwiftStorage\OvhSwiftStorageServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestCase extends \Orchestra\Testbench\TestCase
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
            'visibility' => env('OVH_SWIFT_VISIBILITY', 'public'),
            'publicUrl' => env('OVH_SWIFT_PUBLIC_URL'),
            'urlKey' => env('OVH_SWIFT_URL_KEY'),
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
