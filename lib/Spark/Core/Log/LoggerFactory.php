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

namespace Spark\Core\Log;

use Psr\Log\LoggerInterface;
use Spark\Core\DependencyInjection\ContainerAwareInterface;
use Spark\Core\DependencyInjection\ContainerAwareTrait;

/**
 * A logger factor.
 *
 * @see \Spark\Core\Log\Logger
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
            $this->loggers[$channel] = new Logger(
                $channel,
                $this->container->get('kernel.log.path')
            );
        }

        return $this->loggers[$channel];
    }
}