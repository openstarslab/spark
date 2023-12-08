<?php

namespace Spark\Framework\DependencyInjection\Builder;

final class Definition
{
    private string $class;
    private array $arguments;

    private bool $shared;

    public function __construct(string $class, array $arguments = [])
    {
        $this->setClass($class);
        $this->setArguments($arguments);
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Sets the arguments for the instance.
     *
     * @param array $arguments The new arguments to set for the instance. Must be an array.
     *
     * @return self The updated instance with the new arguments set.
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Retrieves the arguments stored in the instance.
     *
     * @return array[] The arguments stored in the instance.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Sets the shared status of the instance.
     *
     * @param bool $shared The shared status of the instance.
     * @return self The instance with the updated shared status.
     */
    public function setShared(bool $shared): self
    {
        $this->shared = $shared;
        return $this;
    }

    /**
     * Checks if the current instance is shared.
     *
     * @return bool Returns true if the instance is shared, false otherwise.
     */
    public function isShared(): bool
    {
        return $this->shared;
    }
}