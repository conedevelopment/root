<?php

declare(strict_types = 1);

namespace Cone\Root\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryJson extends Model
{
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    public function save(array $options = []): bool
    {
        return false;
    }
}
