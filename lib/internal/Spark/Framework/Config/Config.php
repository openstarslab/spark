<?php

namespace Spark\Framework\Config;

class Config
{
    protected array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function set(string $key, mixed $value = null): void
    {
        $this->items[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->items[$key];
    }
}
