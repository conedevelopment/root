<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Relations\Pivot as Model;

class Pivot extends Model
{
    public function save(array $options = [])
    {
        $this->setAttribute($this->getKeyName(), $this->getKey());

        return true;
    }

    public function delete()
    {
        return true;
    }
}
