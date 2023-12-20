<?php

if (!function_exists('env')) {

    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return string|null
     */
    function env(string $key, mixed $default = null): string|null {
        return \Spark\Framework\Support\Env::get($key, $default);
    }
}