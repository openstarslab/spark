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

namespace Spark\Framework\Http\Response;

use Nyholm\Psr7\Stream;
use Spark\Framework\Http\Response;

class JsonResponse extends Response
{
    /**
     * @param mixed[]|string $body
     * @param int $statusCode
     * @param mixed[] $headers
     */
    public function __construct(array|string $body, int $statusCode = 200, array $headers = [])
    {
        parent::__construct(
            new \Nyholm\Psr7\Response(
                $statusCode,
                \array_merge(['Content-Type' => 'application/json'], $headers),
                Stream::create((string)(\is_array($body) ? \json_encode($body) : $body)),
                '2',
            ),
        );
    }
}
