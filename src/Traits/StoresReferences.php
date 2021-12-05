<?php

namespace Cone\Root\Traits;

trait StoresReferences
{
    /**
     * The stored references.
     *
     * @var array
     */
    protected array $references = [];

    /**
     * Set the value for the given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function setReference(string $key, mixed $value): void
    {
        $this->references[$key] =& $value;
    }

    /**
     * Get the value of the given key.
     *
     * @param  string  $key
     * @return void
     */
    public function getReference(string $key): mixed
    {
        return $this->references[$key] ?? null;
    }

    /**
     * Determine if the given key exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasReference(string $key): bool
    {
        return isset($this->references[$key]);
    }

    /**
     * Unset the reference value of the given key.
     *
     * @param  string  $key
     * @return void
     */
    public function unsetReference(string $key): void
    {
        unset($this->references[$key]);
    }

    /**
     * Flush the references.
     *
     * @return void
     */
    public function flushReferences(): void
    {
        $this->references = [];
    }
}
