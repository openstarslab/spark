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

namespace Spark\Contract\HttpFoundation;

/**
 * Stream contract.
 *
 * @since       2023-11-19
 * @package     Spark\Contract\HttpFoundation
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
interface Stream extends \Stringable
{
    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close(): void;

    /**
     * Gets the size of the stream.
     *
     * @return int|null
     *  Returns the size in bytes or `NULL` if unknown.
     */
    public function size(): ?int;

    /**
     * Returns the current position of pointer.
     *
     * @return int
     *  Position of file pointer.
     */
    public function pos(): int;


    /**
     * Checks if the stream is at the end of the stream.
     *
     * @return bool
     *  Returns `TRUE` if the stream is at the end of the stream, otherwise `FALSE`.
     */
    public function eof(): bool;

    /**
     * Seek to a position in the stream.
     *
     * @see http://www.php.net/manual/en/function.fseek.php
     *
     * @param int $offset
     *  The offset.
     * @param int $whence
     *  Specifies how the cursor position will be calculated based on the seek offset.
     *
     * @return void
     */
    public function seek(int $offset, int $whence = \SEEK_SET): void;

    /**
     * Returns whether the stream is seekable.
     *
     * @return bool
     *  Returns `TRUE` if is seekable, otherwise `FALSE`.
     */
    public function isSeekable(): bool;

    /**
     * Seek to the beginning of the stream.
     *
     * @see seek()
     * @see http://www.php.net/manual/en/function.fseek.php
     *
     * @return void
     */
    public function rewind(): void;

    /**
     * Write data to the stream.
     *
     * @param string $data
     *  The string that is to be written.
     *
     * @return int
     *  Returns the number of bytes written to the stream.
     */
    public function write(string $data): int;

    /**
     * Returns whether the stream is writable.
     *
     * @return bool
     *  Returns `TRUE` if stream is writable, otherwise `FALSE`.
     */
    public function isWritable(): bool;

    /**
     * Write data to the stream.
     *
     * @param int $length
     *  Read up to $length bytes from the object and return them.
     *
     * @return string
     *  Returns the data read from the stream.
     */
    public function read(int $length): string;

    /**
     * Returns whether the stream is readable.
     *
     * @return bool
     *  Returns `TRUE` if stream is readable, otherwise `FALSE`.
     */
    public function isReadable(): bool;

    /**
     * Returns the remaining contents in a string.
     *
     * @return string
     */
    public function getContents(): string;

    /**
     * Separates any underlying resources from the stream.
     *
     * @return resource|null
     *  Returns underlying PHP stream, if any
     */
    public function detach(): mixed;
}
