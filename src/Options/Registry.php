<?php

namespace Cone\Root\Options;

use Cone\Root\Interfaces\Options\Registry as Contract;
use Cone\Root\Interfaces\Options\Repository;
use Illuminate\Support\Str;

class Registry implements Contract
{
    /**
     * The repository instance.
     */
    public readonly Repository $repository;

    /**
     * The option groups.
     */
    protected array $groups = [];

    /**
     * Create a new registry instance.
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get or create a new group.
     */
    public function group(string $key): Group
    {
        $this->groups[$key] ??= new Group(Str::headline($key), $key);

        return $this->groups[$key];
    }

    /**
     * Get the option groups.
     */
    public function groups(): array
    {
        return $this->groups;
    }
}
