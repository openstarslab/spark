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

namespace Spark\Core\HttpFoundation\Factory;

use Spark\Contract\HttpFoundation\Uri;
use Spark\Core\HttpFoundation\HeaderBag;
use Spark\Core\HttpFoundation\Request;

/**
 * Request factory.
 *
 * @since       2023-11-19
 * @package     Spark\Core\HttpFoundation\Factory
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
class RequestFactory implements \Spark\Contract\HttpFoundation\Factory\RequestFactory
{
    public function __construct(
        protected UriFactory $uriFactory = new UriFactory(),
        protected StreamFactory $streamFactory = new StreamFactory()
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function createRequest(string $method, string|Uri $uri): Request
    {
        if (\is_string($uri)) {
            $uri = $this->uriFactory->createUri($uri);
        }

        $stream = $this->streamFactory->createStream();

        return new Request($method, $uri, new HeaderBag([]), $stream);
    }
}
