<?php

declare(strict_types=1);

namespace Cone\Root\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Attachment extends MorphPivot
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'collection',
        'meta',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_mediables';

    /**
     * Get the attributes that should be cast.
     *
     * @return array{'meta':'json'}
     */
    protected function casts(): array
    {
        return [
            'meta' => 'json',
        ];
    }
}
