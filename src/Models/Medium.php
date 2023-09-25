<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\MediumFactory;
use Cone\Root\Interfaces\Models\Medium as Contract;
use Cone\Root\Interfaces\Models\User;
use Cone\Root\Support\Facades\Conversion;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Medium extends Model implements Contract
{
    use Filterable;
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'is_image',
        'urls',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'properties' => '{"conversions":[]}',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'disk',
        'file_name',
        'height',
        'mime_type',
        'name',
        'properties',
        'size',
        'width',
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 25;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_media';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(static function (self $medium): void {
            Storage::disk($medium->disk)->deleteDirectory($medium->uuid);
        });
    }

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
    protected static function newFactory(): Factory
    {
        return MediumFactory::new();
    }

    /**
     * Make a new medium instance from the given path.
     */
    public static function makeFromPath(string $path, array $attributes = []): static
    {
        $type = mime_content_type($path);

        if (! Str::is('image/svg*', $type) && Str::is('image/*', $type)) {
            [$width, $height] = getimagesize($path);
        }

        return new static(array_merge([
            'file_name' => $name = basename($path),
            'mime_type' => $type,
            'width' => isset($width) ? $width : null,
            'height' => isset($height) ? $height : null,
            'disk' => Config::get('root.media.disk', 'public'),
            'size' => max(round(filesize($path) / 1024), 1),
            'name' => pathinfo($name, PATHINFO_FILENAME),
        ], $attributes));
    }

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the user for the medium.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(get_class(App::make(User::class)));
    }

    /**
     * Determine if the file is image.
     */
    protected function isImage(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): bool {
                return Str::is('image/*', $attributes['mime_type']);
            }
        );
    }

    /**
     * Get the conversion URLs.
     */
    protected function urls(): Attribute
    {
        return new Attribute(
            get: function (): array {
                return array_reduce(
                    $this->properties['conversions'] ?? [],
                    function (array $urls, string $conversion): array {
                        return array_merge($urls, [$conversion => $this->getUrl($conversion)]);
                    },
                    ['original' => $this->getUrl()]
                );
            }
        );
    }

    /**
     * Get the formatted size attribute.
     */
    protected function formattedSize(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                if ($attributes['size'] === 0) {
                    return '0 KB';
                }

                $units = ['KB', 'MB', 'GB', 'TB', 'PB'];

                $index = floor(log($attributes['size'], 1024));

                return sprintf('%.1f %s', round($attributes['size'] / pow(1024, $index), 1), $units[$index]);
            }
        );
    }

    /**
     * Get the dimensions attribute.
     */
    protected function dimensions(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): ?string {
                return isset($attributes['width'], $attributes['height'])
                    ? sprintf('%dx%d px', $attributes['width'], $attributes['height'])
                    : null;
            }
        );
    }

    /**
     * Determine if the medium should is convertible.
     */
    public function convertible(): bool
    {
        return $this->isImage && ! Str::is(['image/svg*', 'image/gif'], $this->mime_type);
    }

    /**
     * Perform the conversions on the medium.
     */
    public function convert(): static
    {
        Conversion::perform($this);

        return $this;
    }

    /**
     * Get the path to the conversion.
     */
    public function getPath(string $conversion = null, bool $absolute = false): string
    {
        $path = sprintf('%s/%s', $this->uuid, $this->file_name);

        if (! is_null($conversion)) {
            $path = substr_replace(
                $path, "-{$conversion}", -(mb_strlen(Str::afterLast($path, '.')) + 1), -mb_strlen("-{$conversion}")
            );
        }

        return $absolute ? Storage::disk($this->disk)->path($path) : $path;
    }

    /**
     * Get the full path to the conversion.
     */
    public function getAbsolutePath(string $conversion = null): string
    {
        return $this->getPath($conversion, true);
    }

    /**
     * Get the url to the conversion.
     */
    public function getUrl(string $conversion = null): string
    {
        return URL::to(Storage::disk($this->disk)->url($this->getPath($conversion)));
    }

    /**
     * Scope the query only to the given search term.
     */
    public function scopeSearch(Builder $query, string $value = null): Builder
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where($query->qualifyColumn('name'), 'like', "%{$value}%");
    }

    /**
     * Scope the query only to the given type.
     */
    public function scopeType(Builder $query, string $value): Builder
    {
        switch ($value) {
            case 'image':
                return $query->where($query->qualifyColumn('mime_type'), 'like', 'image%');
            case 'file':
                return $query->where($query->qualifyColumn('mime_type'), 'not like', 'image%');
            default:
                return $query;
        }
    }
}
