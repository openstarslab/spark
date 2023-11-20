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

use Spark\Contract\HttpFoundation\Stream as StreamContract;

/**
 * Response
 *
 * @since       2023-11-19
 * @package     Spark\Core\Http
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
class Response implements \Spark\Contract\HttpFoundation\Response
{
    protected string $version;
    protected StreamContract $content;
    protected HeaderBag $headers;
    protected int $statusCode;
    protected string $statusText;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->headers = new HeaderBag($headers);

        $this->content($content);
        $this->status($statusCode);
        $this->setProtocolVersion('2.0');
    }

    /**
     * {@inheritDoc}
     */
    public function content(array|string $content): self
    {
        $this->content = new Stream((string)(\is_array($content) ? json_encode($content) : $content));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function status(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        $this->statusText = StatusCodes::statusText($statusCode);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setProtocolVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return
            sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText) . "\r\n" .
            $this->headers . "\r\n" .
            $this->content->getContents();
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $header, mixed $value): self
    {
        $this->headers->setHeader($header, $value);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $header): array
    {
        return $this->headers->getHeader($header);
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    /**
     * {@inheritDoc}
     */
    public function send(): void
    {
        // TODO: Implement send() method.
    }
}
