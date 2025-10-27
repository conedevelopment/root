<?php

declare(strict_types=1);

namespace Cone\Root\Models;

use Closure;
use Cone\Root\Database\Factories\MediumFactory;
use Cone\Root\Interfaces\Models\Medium as Contract;
use Cone\Root\Interfaces\Models\User;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Support\Facades\Conversion;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Medium extends Model implements Contract
{
    use Filterable;
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
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
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
    protected static function newFactory(): MediumFactory
    {
        return MediumFactory::new();
    }

    /**
     * Upload the given file.
     */
    public static function upload(UploadedFile $file, ?Closure $callback = null): static
    {
        $medium = static::fromPath($file->getPathname(), [
            'file_name' => $file->getClientOriginalName(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        ]);

        if (is_callable($callback)) {
            call_user_func_array($callback, [$medium, $file]);
        }

        $medium->save();

        $path = Storage::disk('local')->path(Storage::disk('local')->putFile('root-tmp', $file));

        MoveFile::withChain($medium->convertible() ? [new PerformConversions($medium)] : [])
            ->dispatch($medium, $path, false);

        return $medium;
    }

    /**
     * Make a new medium instance from the given path.
     */
    public static function fromPath(string $path, array $attributes = []): static
    {
        $type = mime_content_type($path);

        if (! Str::is('image/svg*', $type) && Str::is('image/*', $type)) {
            [$width, $height] = getimagesize($path);
        }

        return new static(array_merge([
            'file_name' => $name = basename($path),
            'mime_type' => $type,
            'width' => $width ?? null,
            'height' => $height ?? null,
            'disk' => Config::get('root.media.disk', 'public'),
            'size' => max(round(filesize($path) / 1024), 1),
            'name' => pathinfo($name, PATHINFO_FILENAME),
        ], $attributes));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array{'values':'\Illuminate\Database\Eloquent\Casts\AsArrayObject'}
     */
    protected function casts(): array
    {
        return [
            'properties' => AsArrayObject::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
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
        return $this->belongsTo(App::make(User::class)::class);
    }

    /**
     * Determine if the file is image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isImage(): Attribute
    {
        return new Attribute(
            get: static fn (mixed $value, array $attributes): bool => Str::is('image/*', $attributes['mime_type'])
        );
    }

    /**
     * Get the conversion URLs.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<string, string>, never>
     */
    protected function urls(): Attribute
    {
        return new Attribute(
            get: fn (): array => array_reduce(
                $this->properties['conversions'] ?? [],
                fn (array $urls, string $conversion): array => array_merge($urls, [$conversion => $this->getUrl($conversion)]),
                ['original' => $this->getUrl()]
            )
        );
    }

    /**
     * Get the formatted size attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedSize(): Attribute
    {
        return new Attribute(
            get: fn (): string => Number::fileSize($this->size ?: 0)
        );
    }

    /**
     * Get the dimensions attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function dimensions(): Attribute
    {
        return new Attribute(
            get: static fn (mixed $value, array $attributes): ?string => isset($attributes['width'], $attributes['height'])
                ? sprintf('%dx%d px', $attributes['width'], $attributes['height'])
                : null
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
    public function getPath(?string $conversion = null, bool $absolute = false): string
    {
        $path = sprintf('%s/%s', $this->uuid, $this->file_name);

        if (! is_null($conversion) && $conversion !== 'original') {
            $path = substr_replace(
                $path, "-{$conversion}", -(mb_strlen(Str::afterLast($path, '.')) + 1), -mb_strlen("-{$conversion}")
            );
        }

        return $absolute ? Storage::disk($this->disk)->path($path) : $path;
    }

    /**
     * Get the full path to the conversion.
     */
    public function getAbsolutePath(?string $conversion = null): string
    {
        return $this->getPath($conversion, true);
    }

    /**
     * Get the url to the conversion.
     */
    public function getUrl(?string $conversion = null): string
    {
        return URL::to(Storage::disk($this->disk)->url($this->getPath($conversion)));
    }

    /**
     * Check if the medium has the given conversion.
     */
    public function hasConversion(string $conversion): bool
    {
        return in_array($conversion, $this->properties['conversions'] ?? []);
    }

    /**
     * Download the medium.
     */
    public function download(?string $conversion = null): BinaryFileResponse
    {
        return Response::download($this->getAbsolutePath($conversion));
    }

    /**
     * Scope the query only to the given search term.
     */
    #[Scope]
    protected function search(Builder $query, string $value): Builder
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where($query->qualifyColumn('name'), 'like', "%{$value}%");
    }

    /**
     * Scope the query only to the given type.
     */
    #[Scope]
    protected function type(Builder $query, string $value): Builder
    {
        return match ($value) {
            'image' => $query->where($query->qualifyColumn('mime_type'), 'like', 'image%'),
            'file' => $query->where($query->qualifyColumn('mime_type'), 'not like', 'image%'),
            default => $query,
        };
    }
}
