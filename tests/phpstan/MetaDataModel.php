<?php

declare(strict_types=1);

namespace Cone\Root\Tests\phpstan;

use Cone\Root\Traits\HasMetaData;
use Illuminate\Database\Eloquent\Model;

class MetaDataModel extends Model
{
    use HasMetaData;
}
