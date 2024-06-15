<?php

namespace Cone\Root\Tests;

use Cone\Root\Models\User as Model;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements MustVerifyEmail
{
    use HasFactory;
    // use SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): UserFactory
    {
        return new class() extends UserFactory
        {
            protected $model = User::class;
        };
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'updated_at' => 'datetime',
        ];
    }

    public function latestUpload(): HasOne
    {
        return $this->uploads()->one()->ofMany();
    }

    public function documents(): MorphToMany
    {
        return $this->media()->withPivotValue('collection', 'documents');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role');
    }
}
