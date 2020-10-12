<?php

namespace Hedii\LaravelOvhSwiftStorage;

use DateTimeInterface;
use Illuminate\Support\Str;
use Nimbusoft\Flysystem\OpenStack\SwiftAdapter;
use OpenStack\Common\Transport\Utils;
use OpenStack\ObjectStore\v1\Models\Container;
use RuntimeException;

class OvhSwiftStorageAdapter extends SwiftAdapter
{
    /**
     * The container url.
     *
     * @var string
     */
    protected string $containerUrl;

    /**
     * The container visibility.
     *
     * @var string
     */
    protected string $visibility;

    /**
     * The url key used for temporary urls.
     *
     * @var string|null
     */
    protected ?string $urlKey;

    /**
     * OvhSwiftStorageAdapter constructor.
     *
     * @param \OpenStack\ObjectStore\v1\Models\Container $container
     * @param string $containerUrl
     * @param string $visibility
     * @param string|null $prefix
     * @param string|null $urlKey
     */
    public function __construct(
        Container $container,
        string $containerUrl,
        string $visibility,
        ?string $prefix = null,
        ?string $urlKey = null
    ) {
        $this->containerUrl = $containerUrl;
        $this->visibility = $visibility;
        $this->urlKey = $urlKey;

        parent::__construct($container, $prefix);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     * @return string
     * @throws \RuntimeException
     */
    public function getUrl(string $path): string
    {
        if ($this->visibility !== self::VISIBILITY_PUBLIC) {
            throw new RuntimeException('This driver does not support retrieving URLs because the visibility is not public. Please use temporary URLs.');
        }

        return Utils::normalizeUrl($this->containerUrl) . $path;
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param string $path
     * @param \DateTimeInterface $expiration
     * @param array $options
     * @return string
     * @throws \RuntimeException
     */
    public function getTemporaryUrl(string $path, DateTimeInterface $expiration, array $options = []): string
    {
        if ($this->visibility !== self::VISIBILITY_PRIVATE) {
            throw new RuntimeException('This driver does not support creating temporary URLs because the visibility is not private. Please use regular URLs.');
        } elseif (! $this->urlKey) {
            throw new RuntimeException('This driver does not support creating temporary URLs because no temp url key has been configured.');
        }

        $expiration = $expiration->getTimestamp();
        $pathWithoutHost = Str::after($this->containerUrl, parse_url($this->containerUrl, PHP_URL_HOST)) . "/{$path}";
        $body = sprintf("%s\n%s\n%s", 'GET', $expiration, $pathWithoutHost);
        $signature = hash_hmac('sha1', $body, $this->urlKey);

        return Utils::normalizeUrl($this->containerUrl)
            . "{$path}?temp_url_sig={$signature}&temp_url_expires={$expiration}";
    }
}
