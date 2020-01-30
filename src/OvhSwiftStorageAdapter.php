<?php

namespace Hedii\LaravelOvhSwiftStorage;

use Nimbusoft\Flysystem\OpenStack\SwiftAdapter;
use OpenStack\Common\Transport\Utils;
use OpenStack\ObjectStore\v1\Models\Container;
use RuntimeException;

class OvhSwiftStorageAdapter extends SwiftAdapter
{
    /**
     * The container public url.
     *
     * @var null|string
     */
    protected ?string $publicUrl;

    /**
     * OvhSwiftStorageAdapter constructor.
     *
     * @param \OpenStack\ObjectStore\v1\Models\Container $container
     * @param string|null $prefix
     * @param string|null $publicUrl
     */
    public function __construct(Container $container, ?string $prefix = null, ?string $publicUrl = null)
    {
        $this->publicUrl = $publicUrl;

        parent::__construct($container, $prefix);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     * @return string
     */
    public function getUrl(string $path): string
    {
        if (! $this->publicUrl) {
            throw new RuntimeException('This disk does not support retrieving URLs.');
        }

        return Utils::normalizeUrl($this->publicUrl) . $path;
    }
}
