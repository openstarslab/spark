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

/**
 * @phpstan-type HTTPMessage MessageInterface|ServerRequestInterface|ResponseInterface
 */
trait MessageTrait
{
    /** @var HTTPMessage $message */
    private MessageInterface|ServerRequestInterface|ResponseInterface $message;

    /**
     * Returns the decorated message
     *
     * @return HTTPMessage
     */
    public function getMessage(): MessageInterface|ServerRequestInterface|ResponseInterface
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
    public function withProtocolVersion($version): self
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
    public function hasHeader($header): bool
    {
        return $this->message->hasHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $header): array
    {
        return $this->message->getHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($header): string
    {
        return $this->message->getHeaderLine($header);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader(string $header, $value): self
    {
        $new = clone $this;
        $new->message = $this->message->withHeader($header, $value);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader(string $header, $value): self
    {
        $new = clone $this;
        $new->message = $this->message->withAddedHeader($header, $value);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader(string $header): self
    {
        $new = clone $this;
        $new->message = $this->message->withoutHeader($header);

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
    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        $new->message = $this->message->withBody($body);

        return $new;
    }
}
