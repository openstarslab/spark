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

namespace Spark\Framework\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareTrait;

/**
 * Defines a simple logger.
 *
 * In the near future, the logger should be refactored, adding support for `handlers`, such as sending logs to
 * designated email addresses, or adding log entry processing.
 */
class Logger extends AbstractLogger
{
    use LoggerAwareTrait;

    public const MAX_CALL_DEPTH = 5;

    protected string $channel;
    protected string $path;

    protected int $logDepth = 0;

    public function __construct(string $channel, string $path)
    {
        $this->channel = $channel;
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if ($this->logDepth > self::MAX_CALL_DEPTH) {
            return;
        }

        $this->logDepth++;

        if (!\is_string($level)) {
            return;
        }

        $record = new LogRecord(
            new \DateTimeImmutable(),
            $this->channel,
            $level,
            $message,
        );

        $filepath = sprintf(
            '%s/%s.log',
            rtrim($this->path . '/'),
            $this->channel,
        );

        if (!\file_exists($filepath)) {
            \mkdir(\dirname($filepath), 0777, true);
        }

        $message = \strtr(
            '[%date%][%level%] %message%',
            [
                '%date%' => $record->datetime->format('Y-m-d'),
                '%level%' => $record->level,
                '%message%' => $record->message,
            ],
        );


        \file_put_contents($filepath, $message . PHP_EOL, \FILE_APPEND);

        $this->logDepth--;
    }
}
