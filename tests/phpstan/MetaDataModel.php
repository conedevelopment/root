<?php

namespace Tests\phpstan;

use Cone\Root\Traits\HasMetaData;
use Illuminate\Database\Eloquent\Model;

class MetaDataModel extends Model
{
    use HasMetaData;
}
