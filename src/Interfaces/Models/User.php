<?php

declare(strict_types = 1);

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface User
{
    /**
     * Get the uploads for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads(): HasMany;

    /**
     * Get the records for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records(): HasMany;

    /**
     * Get the Root representation of the model.
     *
     * @return array
     */
    public function toRoot(): array;
}
