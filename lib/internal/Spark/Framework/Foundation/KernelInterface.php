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

namespace Spark\Framework\Foundation;

use Spark\Framework\Foundation\Application\ApplicationInterface;

interface KernelInterface
{
    /**
     * Creates a new application of the specified type.
     *
     * @param string $type
     *  The type of application to create.
     *
     * @return ApplicationInterface
     *  The created application.
     */
    public function createApplication(string $type): ApplicationInterface;

    /**
     * Starts the application.
     *
     * @param ApplicationInterface $application
     *  The application instance to start.
     *
     * @return void
     *  Returns nothing.
     */
    public function start(ApplicationInterface $application): void;
}
