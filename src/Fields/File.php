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
use Illuminate\Support\Facades\URL;
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
    public function __construct(string $label, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->name($this->modelAttribute);
        $this->type('file');
        $this->multiple(false);
        $this->class(['form-file']);
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
    public function resolveDisplay(Model $related): ?string
    {
        if (is_null($this->displayResolver)) {
            $this->display(fn(Medium $related): string => $related->isImage
                ? sprintf('<img src="%s" width="40" height="40" alt="%s">', $related->getUrl($this->displayConversion), $related->name)
                : $related->file_name);
        }

        return parent::resolveDisplay($related);
    }

    /**
     * {@inheritdoc}
     */
    public function formatRelated(Request $request, Model $model, Model $related): ?string
    {
        $value = $this->resolveDisplay($related);

        if ($related->isImage || ! $this->resolveAbility('view', $request, $model, $related)) {
            return $value;
        }

        return sprintf('<a href="%s" download>%s</a>', URL::signedRoute('root.download', $related), $value);
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
        $disk = Storage::build(Config::get('root.media.tmp_dir'));

        $disk->putFileAs('', $file, $file->getClientOriginalName());

        return $this->stored($request, $model, $disk->path($file->getClientOriginalName()));
    }

    /**
     * Handle the stored event.
     */
    protected function stored(Request $request, Model $model, string $path): array
    {
        $target = str_replace($request->header('X-Chunk-Hash', ''), '', $path);

        $medium = (Medium::proxy())::fromPath($path, [
            'disk' => $this->disk,
            'file_name' => $name = basename($target),
            'name' => pathinfo($name, PATHINFO_FILENAME),
        ]);

        if (! is_null($this->storageResolver)) {
            call_user_func_array($this->storageResolver, [$request, $medium, $path]);
        }

        /** @var \Illuminate\Foundation\Auth\User&\Cone\Root\Interfaces\Models\User $user */
        $user = $request->user();

        $user->uploads()->save($medium);

        MoveFile::withChain($medium->convertible() ? [new PerformConversions($medium)] : [])
            ->dispatch($medium, $path, false);

        $option = $this->toOption($request, $model, $medium);

        return array_merge($option, [
            'html' => View::make('root::fields.file-option', $option)->render(),
        ]);
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

            $ids = array_map(fn(UploadedFile $file): int => $this->store($request, $model, $file)['value'], $files);

            $value += $this->mergePivotValues($ids);

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
    public function prune(Request $request, Model $model, array $keys): array
    {
        $deleted = [];

        $this->resolveRelatableQuery($request, $model)
            ->whereIn('id', $keys)
            ->cursor()
            ->each(static function (Medium $medium) use ($request, &$deleted): void {
                if ($request->user()->can('delete', $medium)) {
                    $medium->delete();

                    $deleted[] = $medium->getKey();
                }
            });

        return $deleted;
    }

    /**
     * Determine if the relation is a subresource.
     */
    public function isSubResource(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(Request $request, Model $model, Model $related): array
    {
        /** @var \Cone\Root\Models\Medium $related */
        $option = parent::toOption($request, $model, $related);

        $name = sprintf(
            '%s[%s][%s]',
            $this->getAttribute('name'),
            $related->getKey(),
            $this->getRelation($model)->getRelatedPivotKeyName()
        );

        $option['attrs']->merge(['name' => $name]);

        /** @var \Cone\Root\Models\Medium $related */

        return array_merge($option, [
            'fileName' => $related->file_name,
            'isImage' => $related->isImage,
            'processing' => false,
            'url' => ! is_null($this->displayConversion) && $related->hasConversion($this->displayConversion)
                ? $related->getUrl($this->displayConversion)
                : $related->getUrl(),
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
