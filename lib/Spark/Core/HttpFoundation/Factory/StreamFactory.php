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

use Spark\Contract\HttpFoundation\Stream as StreamContract;
use Spark\Core\HttpFoundation\Stream;

/**
 * Stream factory
 *
 * @since       2023-11-19
 * @package     Spark\Core\HttpFoundation\Factory
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
class StreamFactory implements \Spark\Contract\HttpFoundation\Factory\StreamFactory
{
    /**
     * {@inheritDoc}
     */
    public function createStream(string $content = ''): StreamContract
    {
        $resource = \fopen('php://temp', 'rw+');

        if (!\is_resource($resource)) {
            throw new \RuntimeException('StreamFactory::createStream() could not open temporary file stream.');
        }

        \fwrite($resource, $content);
        \rewind($resource);

        return new Stream($resource);
    }
}
