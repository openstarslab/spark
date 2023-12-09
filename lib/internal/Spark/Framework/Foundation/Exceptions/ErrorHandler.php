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

namespace Spark\Framework\Foundation\Exceptions;

class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * Registers the error and exception handlers.
     *
     * @return void
     */
    public static function register(): void
    {
        $self = new self();

        \set_error_handler([$self, 'handleError']);
        \set_exception_handler([$self, 'handleException']);
    }

    /**
     * {@inheritDoc}
     */
    public function handleError(int $level, string $message, string $file = null, int $line = null): bool {
        if (0 === (\error_reporting() & $level)) {
            return false;
        }

        throw new ErrorException($message, $level, $level, $file, $line);
    }

    /**
     * {@inheritDoc}
     */
    public function handleException(\Throwable $throwable): void {
        $traceline = "#%s %s(%s): %s";
        $format = "'%s' with message '%s' in %s:%s\nStack trace:\n%s\n throw in %s on line %s";

        $k = 0;
        $results = [];
        foreach ($throwable->getTrace() as $key => $stack) {
            $results[] = sprintf(
                $traceline,
                $key,
                $stack['file'] ?? '',
                $stack['line'] ?? '',
                $stack['function']
            );
            ++$k;
        }

        $results[] = '#' . ++$k . ' {main}';

        $message = \sprintf($format,
            \get_class($throwable),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine(),
            \implode("\n", $results),
            $throwable->getFile(),
            $throwable->getLine()
        );

        echo "<pre>";
        echo $message;
        echo "</pre>";
    }
}