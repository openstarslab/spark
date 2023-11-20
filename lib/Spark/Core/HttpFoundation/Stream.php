<?php

/**
 * Copyright (C) 2023 OpenStars Lab Development Team
 *
 * This file is part of spark/spark
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Spark\Core\HttpFoundation;

/**
 * Stream
 *
 * @since       2023-11-19
 * @package     Spark\Core\Http
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
class Stream implements \Spark\Contract\HttpFoundation\Stream
{
    /**
     * The underlying stream resource.
     *
     * @var resource|null $stream ;
     */
    protected mixed $stream;
    protected ?array $meta;
    protected ?int $size = null;
    protected ?bool $readable = null;
    protected ?bool $writable = null;
    protected ?bool $seakable = null;

    protected bool $finished = false;

    /**
     * @param string|resource $stream
     */
    public function __construct($stream)
    {
        if (\is_string($stream)) {
            $resource = \fopen('php://temp', 'r+');

            if ($resource === false) {
                throw new \RuntimeException("Could not open temporary file stream.");
            }

            \fwrite($resource, $stream);
            \rewind($resource);

            $stream = $resource;
        }

        $this->stream = $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        if (\is_resource($this->stream) === true) {
            \fclose($this->stream);
        }

        $this->detach();
    }

    /**
     * {@inheritDoc}
     */
    public function detach(): mixed
    {
        $resource = $this->stream;

        $this->stream = null;
        $this->meta = null;
        $this->readable = null;
        $this->writable = null;
        $this->seakable = null;
        $this->size = null;
        $this->finished = false;

        return $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function size(): ?int
    {
        if ($this->stream !== null && !$this->size) {
            $stats = \fstat($this->stream);

            if ($stats) {
                $this->size = $stats['size'];
            }
        }

        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function pos(): int
    {
        $pos = false;

        if ($this->stream !== null) {
            $pos = \ftell($this->stream);
        }

        if ($pos === false) {
            throw new \RuntimeException('Could not get the position of the pointer in stream.');
        }

        return $pos;
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        if ($this->isSeekable() || $this->stream !== null && \fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Could not seek in stream.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSeekable(): bool
    {
        if ($this->seakable === null) {
            $this->seakable = false;

            if ($this->stream) {
                $this->seakable = $this->getMetadata('seekable');
            }
        }

        return $this->seakable;
    }

    public function getMetadata(string $key = null): mixed
    {
        if (!$this->stream) {
            return null;
        }

        $this->meta = \stream_get_meta_data($this->stream);

        if (!$key) {
            return $this->meta;
        }

        return $this->meta[$key] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $data): int
    {
        $written = false;

        if ($this->isWritable() && $this->stream !== null) {
            $written = \fwrite($this->stream, $data);
        }

        if ($written !== false) {
            $this->size = null;
            return $written;
        }

        throw new \RuntimeException('Could not write to stream.');
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        if ($this->writable === null) {
            $this->writable = false;

            if ($this->stream !== null) {
                $mode = $this->getMetadata('mode');

                if (\is_string($mode) && (\str_contains($mode, 'w') | \str_contains($mode, '+') !== false)) {
                    $this->writable = true;
                }
            }
        }

        return $this->writable;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length): string
    {
        $data = false;

        if ($this->isReadable() && $this->stream !== null && $length >= 0) {
            $data = \fread($this->stream, $length);
        }

        if (\is_string($data)) {
            var_dump($data);
            if ($this->eof()) {
                $this->finished = true;
            }

            return $data;
        }

        throw new \RuntimeException('Could not read from stream.');
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        if ($this->readable === null) {
            $this->readable = false;

            if ($this->stream !== null) {
                $mode = $this->getMetadata('mode');

                if (\is_string($mode) && (\str_contains($mode, 'r') | \str_contains($mode, '+') !== false)) {
                    $this->readable = true;
                }
            }
        }

        return $this->readable;
    }

    /**
     * {@inheritDoc}
     */
    public function eof(): bool
    {
        return $this->stream === null || \feof($this->stream);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        if (!$this->stream) {
            return '';
        }

        if ($this->isSeekable()) {
            $this->rewind();
        }

        return $this->getContents();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        if ($this->isSeekable() || $this->stream && \rewind($this->stream) === false) {
            throw new \RuntimeException('Could not rewind stream.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
    {
        $contents = false;

        if ($this->stream !== null) {
            $contents = \stream_get_contents($this->stream, -1, 0);
        }

        if (\is_string($contents)) {
            var_dump($contents);
            if ($this->eof()) {
                $this->finished = true;
            }

            return $contents;
        }

        throw new \RuntimeException('Could not get contents of stream.');
    }
}
