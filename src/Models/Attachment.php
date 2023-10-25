<?php

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
     * @var array<int, string>
     */
    protected $fillable = [
        'collection',
        'meta',
    ];
}
