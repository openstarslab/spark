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

use Psr\Log\LoggerInterface;
use Spark\Framework\Container\ContainerAwareInterface;
use Spark\Framework\Container\ContainerAwareTrait;

/**
 * A logger factor.
 *
 * @see \Spark\Framework\Log\Logger
 */
class LoggerFactory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The registered loggers.
     *
     * @var LoggerInterface[] $loggers
     */
    protected array $loggers = [];

    /**
     * Gets a logger for requested channel.
     *
     * @param string $channel
     *  The channel name for this instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function get(string $channel): LoggerInterface
    {
        if (!isset($this->loggers[$channel])) {
            $logsDir = $this->container->getParameter('kernel.logs_path');

            if (!\is_string($logsDir) || !\file_exists($logsDir)) {
                throw new \RuntimeException("The directory to logs has invalid value, must be valid path.");
            }

            $this->loggers[$channel] = new Logger($channel, $logsDir);
        }

        return $this->loggers[$channel];
    }
}
