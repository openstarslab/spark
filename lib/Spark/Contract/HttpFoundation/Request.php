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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Request contract.
 *
 * @since       2023-11-19
 * @package     Spark\Contract\HttpFoundation
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
interface Request
{
    /**
     * Retrieve the host component of the URI.
     *
     * @return string
     *  The URI host.
     */
    public function host(): string;

    /**
     * Gets an HTTP method.
     *
     * @return string
     *   HTTP method.
     */
    public function method(): string;

    /**
     * Retrieve the path component of the URI.
     *
     * @return string
     *  The URI path.
     */
    public function path(): string;

    /**
     * Retrieve the scheme component of the URI.
     *
     * @return string
     *  The URI scheme.
     */
    public function scheme(): string;

    /**
     * Retrieve the query string of the URI.
     *
     * @return string
     *  The URI query string
     */
    public function query(): string;

    /**
     * Checks whether the request is secure or not.
     *
     * @return bool
     *  Returns `TRUE` if request is secure, otherwise `FALSE`.
     */
    public function secure(): bool;

    /**
     * Returns a raw request body as a stream.
     *
     * @return StreamInterface
     *  Raw request body.
     */
    public function raw(): StreamInterface;

    /**
     * Returns a request body as JSON.
     *
     * @param bool $returnAsArray
     *  If $returnAsArray is set to `TRUE` returns `array` otherwise `object`.
     *
     * @return object|array
     *  Decoded request body.
     */
    public function json(bool $returnAsArray = true): object|array;

    /**
     * Returns a request body as text.
     *
     * @return string
     *  Decoded request body.
     */
    public function text(): string;

    /**
     * Returns a original psr7 request.
     *
     * @return RequestInterface
     */
    public function getOriginalRequest(): RequestInterface;
}
