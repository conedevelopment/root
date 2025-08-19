<?php

declare(strict_types=1);

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\SettingFactory;
use Cone\Root\Interfaces\Models\Setting as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
    protected $table = 'root_settings';

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
    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }

    /**
     * Cast the value attribute to the given type.
     */
    public function castValue(?string $type = null): static
    {
        if (! is_null($type)) {
            $this->casts['value'] = $type;
        } else {
            unset($this->casts['value']);
        }

        return $this;
    }
}
