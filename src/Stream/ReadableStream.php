<?php declare(strict_types=1);

namespace League\Flysystem\Stream;

interface ReadableStream
{
    /**
     * Closes the stream.
     */
    public function close(): void;

    /**
     * Whether the stream has been completely read.
     */
    public function eof(): bool;

    /**
     * Reads bytes from the stream.
     */
    public function read(int $length): string;

    /**
     * Rewinds the stream.
     */
    public function rewind(): void;
}
