<?php

declare(strict_types = 1);

namespace Cone\Root\Interfaces;

use Cone\Root\Resources\Resource;

interface Resourceable
{
    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource;
}
