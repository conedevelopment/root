<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Settings;

interface Registry
{
    /**
     * Get the repository instance.
     */
    public function getRepository(): Repository;
}
