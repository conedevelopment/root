<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\OptionFactory;
use Cone\Root\Interfaces\Models\Option as Contract;
use Cone\Root\Root;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_options';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): OptionFactory
    {
        return OptionFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => match (true) {
                is_null($this->key) => 'string',
                default => Root::instance()->options->resolveCast($this->key),
            },
        ];
    }
}
