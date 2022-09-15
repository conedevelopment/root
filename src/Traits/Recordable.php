<?php

declare(strict_types = 1);

namespace Cone\Root\Traits;

use Cone\Root\Models\Record;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Recordable
{
    /**
     * Get the records for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function records(): MorphMany
    {
        return $this->morphMany(Record::class, 'target');
    }
}
