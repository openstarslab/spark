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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Request
 *
 * @since   2023-11-19
 * @package Spark\Core\HttpFoundation
 * @author  Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license https://opensource.org/license/lgpl-2-1/
 * @link    https://github.com/openstarslab/spark-core
 */
class Request implements \Spark\Contract\HttpFoundation\Request
{
    protected RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function host(): string
    {
        return $this->request->getUri()->getHost();
    }

    /**
     * @inheritDoc
     */
    public function method(): string
    {
        return $this->request->getMethod();
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->request->getUri()->getPath();
    }

    /**
     * @inheritDoc
     */
    public function scheme(): string
    {
        return $this->request->getUri()->getScheme();
    }

    /**
     * @inheritDoc
     */
    public function query(): string
    {
        return $this->request->getUri()->getQuery();
    }

    /**
     * @inheritDoc
     */
    public function secure(): bool
    {
        return $this->scheme() === 'HTTPS';
    }

    /**
     * @inheritDoc
     */
    public function raw(): StreamInterface
    {
        return $this->request->getBody();
    }

    /**
     * @inheritDoc
     */
    public function json(bool $returnAsArray = true): object|array
    {
        $content = $this->raw()->getContents();

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
     * @inheritDoc
     */
    public function text(): string
    {
        return $this->raw()->getContents();
    }

    /**
     * @inheritDoc
     */
    public function getOriginalRequest(): RequestInterface
    {
        return $this->request;
    }
}
