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

/**
 * Header bag
 *
 * @since       2023-11-19
 * @package     Spark\Core\Http
 * @author      Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license     https://opensource.org/license/lgpl-2-1/
 * @link        https://github.com/openstarslab/spark-core
 */
class HeaderBag implements \Stringable
{
    /**
     * @var array $headers
     */
    public array $headers;

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Sets the response's HTTP header field to value.
     *
     * @param string|array $field
     *  To set multiple fields at once, pass an array as the parameter.
     * @param mixed|null $value
     *  HTTP header values.
     *
     * @return $this
     */
    public function setHeader(string|array $field, mixed $value = null): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->headers[$key] = $val;
            }
        } else {
            $this->headers[$field] = $value;
        }

        return $this;
    }

    /**
     * Returns the HTTP response header specified by field.
     *
     * @param string $name
     *  HTTP header name.
     *
     * @param array $default
     *  Returns a default value if header not found.
     *
     * @return array
     *  Returns array of header values.
     */
    public function getHeader(string $name, array $default = []): array
    {
        $name = $this->normalizeHeaderName($name);

        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return $default;
    }

    /**
     * Normalizes header name.
     *
     * @param string $name
     *  Header name.
     *
     * @param bool $preserveCase
     *  Preserve Case sensitive header name.
     *
     * @return string
     *  Returns normalized header name.
     */
    protected function normalizeHeaderName(string $name, bool $preserveCase = false): string
    {
        $name = (string)\strstr($name, '_', true);

        if (!$preserveCase) {
            $name = \strtolower($name);
        }

        if (\str_starts_with($name, 'HTTP-')) {
            $name = \substr($name, 5);
        }

        return $name;
    }

    public function getHeaders(): array
    {
        $headers = [];

        foreach ($this->headers as $key => $header) {
            $headers[$key] = $header;
        }

        return $headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$this->normalizeHeaderName($name)]);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $headers = $this->headers;

        \ksort($headers);

        $max = \max(\array_map('strlen', \array_keys($headers))) + 1;
        $content = '';
        foreach ($headers as $name => $values) {
            $name = \ucwords($name, '-');
            foreach ($values as $value) {
                $content .= \sprintf("%-{$max}s %s\r\n", $name . ':', $value);
            }
        }

        return $content;
    }
}
