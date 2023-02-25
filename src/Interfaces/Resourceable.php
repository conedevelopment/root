<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Resources\Resource;

interface Resourceable
{
    /**
     * Get the resource representation of the model.
     */
    public static function toResource(): Resource;
}
