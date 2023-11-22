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

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;

/**
 * Response
 *
 * @since   2023-11-19
 * @package Spark\Core\HttpFoundation
 * @author  Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license https://opensource.org/license/lgpl-2-1/
 * @link    https://github.com/openstarslab/spark-core
 */
class Response implements \Spark\Contract\HttpFoundation\Response
{
    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response = null)
    {
        $this->response = $response ?: new \Nyholm\Psr7\Response();
        $this->response->withHeader('Content-Type', 'text/html');
    }

    /**
     * @inheritDoc
     */
    public function status(int $statusCode): \Spark\Contract\HttpFoundation\Response
    {
        $this->response->withStatus($statusCode);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function content(array|string $content): \Spark\Contract\HttpFoundation\Response
    {
        if (\is_array($content)) {
            $content = (string) json_encode($content);
        }

        $body = $this->response->getBody();
        $body->write($content);

        $this->response->withBody($body);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set(string $header, mixed $value): \Spark\Contract\HttpFoundation\Response
    {
        $this->response->withHeader($header, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $header): array
    {
        return $this->response->getHeader($header);
    }

    /**
     * @inheritDoc
     */
    public function send(): ResponseInterface
    {
        $sapiEmitter = new SapiEmitter();
        $sapiEmitter->emit($this->response);

        return $this->response;
    }
}
