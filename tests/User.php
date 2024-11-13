<?php

namespace Cone\Root\Tests;

use Cone\Root\Models\User as Model;
use Cone\Root\Traits\HasRootEvents;
use Cone\Root\Traits\Translatable;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class User extends Model implements MustVerifyEmail
{
    use HasFactory;
    use HasRootEvents;
    use SoftDeletes;
    use Translatable;

    protected $guarded = [];

    protected $table = 'users';

    protected static function newFactory(): UserFactory
    {
        return new class extends UserFactory
        {
            protected $model = User::class;

            public function definition(): array
            {
                return array_merge(parent::definition(), [
                    'employer_id' => null,
                    'employer_type' => null,
                    'deleted_at' => null,
                    'settings' => null,
                ]);
            }
        };
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'json',
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
            ->withPivot(['id', 'role']);
    }

    public function employer(): MorphTo
    {
        return $this->morphTo();
    }

    public function shouldTwoFactorAuthenticate(Request $request): bool
    {
        return $this->email === 'twofactor@root.local';
    }
}
