<?php

namespace Cone\Root\Tests;

use Cone\Root\Models\User as Model;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class User extends Model implements MustVerifyEmail
{
    use HasFactory;

    protected $guarded = [];

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

    public function documents(): MorphToMany
    {
        return $this->media()->withPivotValue('collection', 'documents');
    }
}
