<?php

namespace Cone\Root\Settings;

use Cone\Root\Interfaces\Settings\Registry as Contract;
use Cone\Root\Interfaces\Settings\Repository;

class Registry implements Contract
{
    /**
     * The repository instance.
     */
    protected Repository $repository;

    /**
     * Create a new registry instance.
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function __call(string $name, array $arguments): mixed
    {
        return call_user_func_array([$this->repository, $name], $arguments);
    }
}
