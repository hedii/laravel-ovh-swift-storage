<?php

namespace Hedii\LaravelOvhSwiftStorage\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PrivateContainerTest extends TestCase
{
    public function testItStoreAFileAndRetrieveItsTemporaryUrl(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'visibility' => 'private',
            'containerName' => 'test-private',
        ]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);
        $temporaryUrl = Storage::temporaryUrl($fileName, Carbon::now()->addMinute());

        $this->assertSame($content, file_get_contents($temporaryUrl));
    }

    public function testItSetAPrefix(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'visibility' => 'private',
            'containerName' => 'test-private',
            'prefix' => 'foo',
        ]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);
        $temporaryUrl = Storage::temporaryUrl($fileName, Carbon::now()->addMinute());

        $this->assertStringEndsWith("foo/{$fileName}", Str::before($temporaryUrl, '?'));
    }

    public function testItDoesntUsePrefixIfPrefixIsNull(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'visibility' => 'private',
            'containerName' => 'test-private',
            'prefix' => null,
        ]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);
        $temporaryUrl = Storage::temporaryUrl($fileName, Carbon::now()->addMinute());

        $this->assertStringEndsWith(
            "test-private/{$fileName}",
            Str::before($temporaryUrl, '?')
        );
    }

    public function testItThrowsWhenUsingUrlOnPrivateContainer(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'visibility' => 'private',
            'containerName' => 'test-private',
        ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This driver does not support retrieving URLs because the visibility is not public. Please use temporary URLs.');

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        Storage::url($fileName);
    }

    public function testItThrowsWhenNoTempUrlKeyHasBeenSet(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'visibility' => 'private',
            'containerName' => 'test-private',
            'urlKey' => null
        ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This driver does not support creating temporary URLs because no temp url key has been configured.');

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        Storage::temporaryUrl($fileName, Carbon::now()->addMinute());
    }
}
