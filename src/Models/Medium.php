<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\MediumFactory;
use Cone\Root\Interfaces\Models\Medium as Contract;
use Cone\Root\Support\Facades\Conversion;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * @var array
     */
    protected $appends = [
        'is_image',
        'urls',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
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
     * @var array
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
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_media';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(static function (self $medium): void {
            Storage::disk($medium->disk)->deleteDirectory($medium->id);
        });
    }

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return MediumFactory::new();
    }

    /**
     * Make a new medium instance from the given path.
     *
     * @param  string  $path
     * @return static
     */
    public static function makeFrom(string $path): static
    {
        $name = preg_replace('/[\w]{5}__/iu', '', basename($path, '.chunk'));

        $type = mime_content_type($path);

        if (! Str::is('image/svg*', $type) && Str::is('image/*', $type)) {
            [$width, $height] = getimagesize($path);
        }

        return static::make([
            'file_name' => $name,
            'mime_type' => $type,
            'width' => isset($width) ? $width : null,
            'height' => isset($height) ? $height : null,
            'disk' => Config::get('root.media.disk', 'public'),
            'size' => max(round(filesize($path) / 1024), 1),
            'name' => pathinfo($name, PATHINFO_FILENAME),
        ]);
    }

    /**
     * Create a new medium from the given path.
     *
     * @param  string  $path
     * @return static
     */
    public static function createFrom(string $path): static
    {
        $medium = static::makeFrom($path);

        $medium->save();

        return $medium;
    }

    /**
     * Get the user for the medium.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::getProxiedClass());
    }

    /**
     * Determine if the file is image.
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool
    {
        return Str::is('image/*', $this->mime_type);
    }

    /**
     * Get the conversion URLs.
     *
     * @return array
     */
    public function getUrlsAttribute(): array
    {
        $urls = ['original' => $this->getUrl()];

        if (! $this->convertible()) {
            return $urls;
        }

        return array_reduce($this->properties['conversions'] ?? [], function (array $urls, string $conversion): array {
            return array_merge($urls, [$conversion => $this->getUrl($conversion)]);
        }, $urls);
    }

    /**
     * Get the formatted size attribute.
     *
     * @return string
     */
    public function getFormattedSizeAttribute(): string
    {
        if ($this->size === 0) {
            return '0 KB';
        }

        $units = ['KB', 'MB', 'GB', 'TB', 'PB'];

        $index = floor(log($this->size, 1024));

        return sprintf('%.1f %s', round($this->size / pow(1024, $index), 1), $units[$index]);
    }

    /**
     * Get the dimensions attribute.
     *
     * @return string|null
     */
    public function getDimensionsAttribute(): ?string
    {
        if (is_null($this->width) || is_null($this->height)) {
            return null;
        }

        return sprintf('%dx%d px', $this->width, $this->height);
    }

    /**
     * Determine if the medium should is convertible.
     *
     * @return bool
     */
    public function convertible(): bool
    {
        return $this->isImage && ! Str::is(['image/svg*', 'image/gif'], $this->mime_type);
    }

    /**
     * Perform the conversions on the medium.
     *
     * @return $this
     */
    public function convert(): static
    {
        Conversion::perform($this);

        return $this;
    }

    /**
     * Get the path to the conversion.
     *
     * @param  string|null  $conversion
     * @param  bool  $absolute
     * @return string
     */
    public function getPath(?string $conversion = null, bool $absolute = false): string
    {
        $path = sprintf('%s/%s', $this->id, $this->file_name);

        if (! is_null($conversion)) {
            $path = substr_replace(
                $path, "-{$conversion}", -(mb_strlen(Str::afterLast($path, '.')) + 1), -mb_strlen("-{$conversion}")
            );
        }

        return $absolute ? Storage::disk($this->disk)->path($path) : $path;
    }

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getAbsolutePath(?string $conversion = null): string
    {
        return $this->getPath($conversion, true);
    }

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getUrl(?string $conversion = null): string
    {
        return URL::to(Storage::disk($this->disk)->url($this->getPath($conversion)));
    }

    /**
     * Scope the query only to the given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, ?string $value = null): Builder
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where($query->qualifyColumn('name'), 'like', "%{$value}%");
    }

    /**
     * Scope the query only to the given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
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

    /**
     * Sort the query by the given order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array|null  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, ?array $value = []): Builder
    {
        $value = array_replace(['by' => 'id', 'order' => 'desc'], (array) $value);

        if ($value['by'] === 'id') {
            $value['by'] = $query->getModel()->getKeyName();
        }

        return $query->orderBy($query->qualifyColumn($value['by']), $value['order']);
    }
}
