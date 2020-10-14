<?php

namespace Hedii\LaravelOvhSwiftStorage\Tests;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequestOptionsTest extends TestCase
{
    protected string $tempDir = __DIR__ . '/temp';

    public function setUp(): void
    {
        parent::setUp();

        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir);
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();

        exec("rm -rf {$this->tempDir}");
    }

    public function testItShouldUseRequestOptions(): void
    {
        $path = $this->tempDir . '/' . Str::random() . '.txt';

        $this->app['config']->set('filesystems.disks.ovh-swift', $this->getOvhSwiftConfiguration([
            'requestOptions' => [
                'on_headers' => fn(): bool => touch($path)
            ]
        ]));

        $fileName = $this->randomFileName();
        $content = $this->randomContent();

        Storage::put($fileName, $content);

        $this->assertFileExists($path);
    }
}
