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

\error_reporting(\E_STRICT | \E_ALL);

if (!defined('PHP_VERSION_ID') || \PHP_VERSION_ID < 80210) {
    if (\PHP_SAPI === 'cli') {
        echo 'Unsupported PHP Version, spark supports PHP 8.2.1 or later.';
    } else {
        echo "<p>Unsupported PHP Version, spark supports PHP 8.2.1 or later.</p>";
    }

    \http_response_code(503);
    exit(1);
}

if (\PHP_SAPI !== 'cli') {
    \ini_set('session.use_cookies', '1');
    \ini_set('session.use_only_cookies', '1');
    \ini_set('session.use_trans_sid', '0');
    \ini_set('session.cache_limiter', '');
    \ini_set('session.cookie_httponly', '1');
}

\setlocale(\LC_ALL, 'C');

\mb_internal_encoding('UTF-8');
\mb_language('uni');

\date_default_timezone_set('UTC');

$autoload = require __DIR__ . '/../vendor/autoload.php';

return \Spark\Framework\Foundation\Kernel::create('dev', \dirname(__DIR__), $autoload);
