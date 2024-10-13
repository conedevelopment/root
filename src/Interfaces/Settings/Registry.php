<?php

namespace Cone\Root\Interfaces\Settings;

interface Registry
{
    /**
     * Get the repository instance.
     */
    public function getRepository(): Repository;
}
