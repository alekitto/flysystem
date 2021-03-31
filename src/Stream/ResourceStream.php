<?php declare(strict_types=1);

namespace League\Flysystem\Stream;

use League\Flysystem\InvalidStreamProvided;
use function fclose;
use function fread;
use function ftell;

class ResourceStream implements ReadableStream
{
    /** @var bool */
    private $eof;

    /** @var resource */
    private $resource;

    /** @var bool */
    private $seekable;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (is_resource($resource) === false) {
            throw new InvalidStreamProvided(
                "Invalid stream provided, expected stream resource, received " . gettype($resource)
            );
        } elseif ($type = get_resource_type($resource) !== 'stream') {
            throw new InvalidStreamProvided(
                "Invalid stream provided, expected stream resource, received resource of type " . $type
            );
        }

        if (($metadata = @stream_get_meta_data($resource)) === false) {
            throw new InvalidStreamProvided(
                "Invalid stream provided, expected stream resource, received " . gettype($resource)
            );
        }

        $this->eof = feof($resource);
        $this->seekable = $metadata['seekable'];
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        fclose($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return $this->eof;
    }

    /**
     * @inheritDoc
     */
    public function read(int $length): string
    {
        $content = fread($this->resource, $length);
        $this->eof = feof($this->resource);

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        if ($this->seekable === false) {
            return;
        }

        if (0 !== ftell($this->resource)) {
            fseek($this->resource, 0, SEEK_SET);
        }
    }
}
