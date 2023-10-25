<?php

namespace Tests;

use Cone\Root\Traits\HasMetaData;
use Illuminate\Database\Eloquent\Model;

class MetaDataModel extends Model
{
    use HasMetaData;

    /**
     * The Blade template.
     */
    protected string $template = 'meta.field';
}
