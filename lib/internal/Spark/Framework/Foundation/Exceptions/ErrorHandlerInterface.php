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

interface ErrorHandlerInterface
{
    /**
     * Handle an exception.
     *
     * @param \Throwable $throwable
     *  The exception to handle.
     *
     * @return void
     */
    public function handleException(\Throwable $throwable): void;

    /**
     * Handle an error.
     *
     * @param int $level
     *   The level of the error that occurred.
     * @param string $message
     *   The error message.
     * @param string|null $file
     *   The file in which the error occurred (optional).
     * @param int|null $line
     *   The line number where the error occurred (optional).
     *
     * @return bool
     *   Indicates whether the error handling was successful or not.
     */
    public function handleError(int $level, string $message, string $file = null, int $line = null): bool;
}
