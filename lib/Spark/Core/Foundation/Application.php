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

namespace Spark\Core\Foundation;

use Spark\Contract\Foundation\ApplicationContract;

/**
 * Application
 *
 * The main class that orchestrates of the Framework functionality of the library.
 *
 * @package Spark\Core\Foundation
 * @version 0.1.0-alpha
 * @since 2023-11-17
 * @author Dominik Szamburski <dominikszamburski99@gmail.com>
 * @link https://github.com/openstarslab/spark-core
 * @license https://opensource.org/license/lgpl-2-1/
 */
final class Application implements ApplicationContract
{
    public const VERSION = '0.1.0-alpha';
}
