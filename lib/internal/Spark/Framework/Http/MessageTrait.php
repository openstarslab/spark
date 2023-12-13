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

namespace Spark\Framework\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * Returns the decorated message
     *
     * @return ServerRequestInterface|ResponseInterface
     */
    public function getMessage(): ServerRequestInterface|ResponseInterface
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(): string
    {
        return $this->message->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        $new = clone $this;
        $new->message = $this->message->withProtocolVersion($version);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->message->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader(string $name): bool
    {
        return $this->message->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $name): array
    {
        return $this->message->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine(string $name): string
    {
        return $this->message->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader(string $name, $value): MessageInterface
    {
        $new = clone $this;
        $new->message = $this->message->withHeader($name, $value);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $new = clone $this;
        $new->message = $this->message->withAddedHeader($name, $value);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader(string $name): MessageInterface
    {
        $new = clone $this;
        $new->message = $this->message->withoutHeader($name);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(): StreamInterface
    {
        return $this->message->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $new = clone $this;
        $new->message = $this->message->withBody($body);

        return $new;
    }
}
