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

use Spark\Contract\HttpFoundation\Stream;
use Spark\Contract\HttpFoundation\Uri;

/**
 * Request
 *
 * @since       2023-11-19
 * @package     Spark\Core\Http
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
class Request implements \Spark\Contract\HttpFoundation\Request
{
    protected Uri $uri;
    protected string $method;
    protected HeaderBag $headers;
    protected Stream $body;

    public function __construct(string $method, Uri $uri, HeaderBag $headers, Stream $body)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * {@inheritDoc}
     */
    public function host(): string
    {
        return $this->uri->getHost();
    }

    /**
     * {@inheritDoc}
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function path(): string
    {
        return $this->uri->getPath();
    }

    /**
     * {@inheritDoc}
     */
    public function scheme(): string
    {
        return $this->uri->getScheme();
    }

    /**
     * {@inheritDoc}
     */
    public function query(): string
    {
        return $this->uri->getQuery();
    }

    /**
     * {@inheritDoc}
     */
    public function secure(): bool
    {
        return $this->uri->getScheme() === 'HTTPS';
    }

    /**
     * {@inheritDoc}
     */
    public function raw(): Stream
    {
        return $this->body;
    }

    /**
     * @{@inheritDoc}
     */
    public function json(bool $returnAsArray = true): array
    {
        $content = $this->body->getContents();

        try {
            $content = json_decode(
                $content,
                $returnAsArray,
                512,
                \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new \JsonException('Could not decode request body.', $e->getCode(), $e);
        }


        if (!\is_array($content)) {
            throw new \JsonException(
                sprintf(
                    'JSON content was expected to decode to an array, "%s" returned.',
                    get_debug_type($content)
                )
            );
        }

        return $content;
    }

    /**
     * @{@inheritDoc}
     */
    public function text(): string
    {
        return $this->body->getContents();
    }
}
