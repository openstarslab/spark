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
 * Response contract.
 *
 * @since       2023-11-19
 * @package     Spark\Contract\HttpFoundation
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
interface Response extends \Stringable
{
    /**
     * Sets the HTTP status for the response.
     *
     * @param int $statusCode
     *  HTTP status code.
     *
     * @return self
     */
    public function status(int $statusCode): self;

    /**
     * Sets the response content.
     *
     * @param string|array $content
     *  Message content.
     *
     * @return self
     */
    public function content(string|array $content): self;

    /**
     * Sets the response's HTTP header field to value.
     *
     * @param string $header
     *  HTTP header name.
     * @param mixed $value
     *  HTTP header value.
     *
     * @return self
     */
    public function set(string $header, mixed $value): self;

    /**
     * Returns the HTTP response header specified by field. The match is case-insensitive.
     *
     * @param string $header
     *  HTTP header name.
     *
     * @return array
     */
    public function get(string $header): array;

    /**
     * Sends HTTP headers and content.
     *
     * @return void
     */
    public function send(): void;

    /**
     * Sets the HTTP protocol version.
     *
     * @param string $version
     *  HTTP protocol version.
     *
     * @return self
     */
    public function setProtocolVersion(string $version): self;

    /**
     * Gets the HTTP protocol version.
     *
     * @return string
     *  HTTP protocol version.
     */
    public function getProtocolVersion(): string;
}
