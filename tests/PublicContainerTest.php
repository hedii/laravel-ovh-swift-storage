<?php

namespace Hedii\LaravelOvhSwiftStorage\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PublicContainerTest extends TestCase
{
    public function testItStoreAFileAndRetrieveItsUrl(): void
    {
        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertSame($content, file_get_contents(Storage::url($fileName)));
    }

    public function testItSetAPrefix(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration(['prefix' => 'foo']));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertStringEndsWith("foo/{$fileName}", Storage::url($fileName));
    }

    public function testItDoesntUsePrefixIfPrefixIsNull(): void
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

    public function testItUseACustomPublicUrl(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'publicUrl' => 'http://foo.example.com',
            'prefix' => null
        ]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertSame(
            "http://foo.example.com/{$fileName}",
            Storage::url($fileName)
        );
    }

    public function testItPreservesPrefixWithACustomPublicUrl(): void
    {
        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'publicUrl' => 'http://foo.example.com',
            'prefix' => 'a-prefix'
        ]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertSame(
            "http://foo.example.com/a-prefix/{$fileName}",
            Storage::url($fileName)
        );
    }

    public function testItThrowsWhenUsingTemporaryUrlOnPublicContainer(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This driver does not support creating temporary URLs because the visibility is not private. Please use regular URLs.');

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        Storage::temporaryUrl($fileName, Carbon::now()->addMinute());
    }
}
