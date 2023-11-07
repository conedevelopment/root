<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class File extends MorphToMany
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.file';

    /**
     * The storage resolver callback.
     */
    protected ?Closure $storageResolver = null;

    /**
     * Indicates whether the file input is prunable.
     */
    protected bool $prunable = false;

    /**
     * The storage disk.
     */
    protected string $disk;

    /**
     * The displayable conversion name.
     */
    protected ?string $displayConversion = 'original';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->type('file')->multiple(false);

        $this->disk(Config::get('root.media.disk', 'public'));
    }

    /**
     * Set the "multiple" HTML attribute.
     */
    public function multiple(bool $value = true): static
    {
        $this->setAttribute('multiple', $value);

        return $this;
    }

    /**
     * Set the "accept" HTML attribute.
     */
    public function accept(string $value): static
    {
        $this->setAttribute('accept', $value);

        return $this;
    }

    /**
     * Set the disk attribute.
     */
    public function disk(string $value): static
    {
        $this->disk = $value;

        return $this;
    }

    /**
     * Set the collection pivot value.
     */
    public function collection(string $value): static
    {
        $this->pivotValues['collection'] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDisplay(Model $related): mixed
    {
        if (is_null($this->displayResolver)) {
            $this->display(function (Medium $related): string {
                return $related->isImage
                    ? sprintf('<img src="%s" width="30" height="30">', $related->getUrl($this->displayConversion))
                    : sprintf('<a href="%s">%s</a>', $related->getUrl(), $related->file_name);
            });
        }

        return parent::resolveDisplay($related);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return $this->resolveValue($request, $model)
            ->map(function (Medium $medium) use ($request, $model): array {
                $option = $this->toOption($request, $model, $medium);

                return array_merge($option, [
                    'html' => View::make('root::fields.file-option', $option)->render(),
                ]);
            })
            ->all();
    }

    /**
     * Set the storage resolver callback.
     */
    public function storeUsing(Closure $callback): static
    {
        $this->storageResolver = $callback;

        return $this;
    }

    /**
     * Store the uploaded file.
     */
    public function store(Request $request, Model $model, UploadedFile $file): array
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => Config::get('root.media.tmp_dir'),
        ]);

        $disk->put($file->getClientOriginalName(), $file);

        return $this->stored($request, $model, $disk->path($file->getClientOriginalName()));
    }

    /**
     * Handle the stored event.
     */
    protected function stored(Request $request, Model $model, string $path): array
    {
        $target = str_replace($request->header('X-Chunk-Hash', ''), '', $path);

        $medium = (Medium::proxy())::makeFromPath($path, [
            'disk' => $this->disk,
            'file_name' => $name = basename($target),
            'name' => pathinfo($name, PATHINFO_FILENAME),
        ]);

        if (! is_null($this->storageResolver)) {
            call_user_func_array($this->storageResolver, [$request, $medium, $path]);
        }

        $request->user()->uploads()->save($medium);

        MoveFile::withChain($medium->convertible() ? [new PerformConversions($medium)] : [])
            ->dispatch($medium, $path, false);

        return $this->toOption($request, $model, $medium);
    }

    /**
     * Set the prunable attribute.
     */
    public function prunable(bool $value = true): static
    {
        $this->prunable = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $model->saved(function (Model $model) use ($request, $value): void {
            $files = Arr::wrap($request->file($this->getRequestKey(), []));

            $ids = array_map(function (UploadedFile $file) use ($request, $model): int {
                return $this->store($request, $model, $file)['value'];
            }, $files);

            $value = array_merge((array) $value, $ids);

            $this->resolveHydrate($request, $model, $value);

            $keys = $this->getRelation($model)->sync($value);

            if ($this->prunable && ! empty($keys['detached'])) {
                $this->prune($request, $model, $keys['detached']);
            }
        });
    }

    /**
     * Prune the related models.
     */
    public function prune(Request $request, Model $model, array $keys): int
    {
        $count = 0;

        $this->resolveRelatableQuery($request, $model)
            ->whereIn('id', $keys)
            ->cursor()
            ->each(static function (Medium $medium) use (&$count): void {
                $medium->delete();

                $count++;
            });

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(Request $request, Model $model, Model $related): array
    {
        $option = parent::toOption($request, $model, $related);

        $name = sprintf(
            '%s[%s][%s]',
            $this->getAttribute('name'),
            $related->getKey(),
            $this->getRelation($model)->getRelatedPivotKeyName()
        );

        $option['attrs']->merge(['name' => $name]);

        return array_merge($option, [
            'fileName' => $related->file_name,
            'isImage' => $related->isImage,
            'processing' => false,
            'url' => $related->hasConversion('thumbnail') ? $related->getUrl('thumbnail') : $related->getUrl(),
            'uuid' => $related->uuid,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'options' => $this->resolveOptions($request, $model),
        ]);
    }
}
