<?php

namespace Cone\Root\Tests;

use App\Models\User as Model;
use Cone\Root\Interfaces\Models\User as RootUser;
use Cone\Root\Traits\AsRootUser;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\HasMetaData;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model implements MustVerifyEmail, RootUser
{
    use AsRootUser;
    use HasMedia;
    use HasMetaData;

    protected static function newFactory(): UserFactory
    {
        return new class() extends UserFactory
        {
            protected $model = User::class;
        };
    }

    public function latestUpload(): HasOne
    {
        return $this->uploads()->one()->ofMany();
    }
}
