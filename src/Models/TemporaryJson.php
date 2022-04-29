<?php

namespace Cone\Root\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryJson extends Model
{
    /**
     * {@inheritdoc}
     */
    public function save(array $options = []): bool
    {
        return false;
    }
}
