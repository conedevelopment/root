<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Fields\Options\FileOption;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

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
     * {@inheritdoc}
     */
    public function resolveDisplay(Model $related): mixed
    {
        if (is_null($this->displayResolver)) {
            $this->display('file_name');
        }

        return parent::resolveDisplay($related);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(Request $request): array
    {
        return $this->resolveValue($request)
            ->map(function (Medium $medium): FileOption {
                return $this->toOption($medium)->selected();
            })
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function newOption(Model $value, string $label): FileOption
    {
        $name = sprintf(
            '%s[%s][%s]',
            $this->getAttribute('name'),
            $value->getKey(),
            $this->getRelation()->getRelatedPivotKeyName()
        );

        return FileOption::make($value, $label)->setAttribute('name', $name);
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
    public function store(Request $request, UploadedFile $file): FileOption
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => Config::get('root.media.tmp_dir'),
        ]);

        $disk->put($file->getClientOriginalName(), $file);

        return $this->stored($request, $disk->path($file->getClientOriginalName()));
    }

    /**
     * Handle the stored event.
     */
    protected function stored(Request $request, string $path): FileOption
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

        return $this->toOption($medium);
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
    public function persist(Request $request, mixed $value): void
    {
        $this->getModel()->saved(function () use ($request, $value): void {
            $files = Arr::wrap($request->file($this->getRequestKey(), []));

            $ids = array_map(function (UploadedFile $file) use ($request): int {
                return $this->store($request, $file)->model->getKey();
            }, $files);

            $value = array_merge((array) $value, $ids);

            $this->resolveHydrate($request, $value);

            $keys = $this->getRelation()->sync($value);

            if ($this->prunable && ! empty($keys['detached'])) {
                $this->prune($request, $keys['detached']);
            }
        });
    }

    /**
     * Prune the related models.
     */
    protected function prune(Request $request, array $keys): int
    {
        $count = 0;

        $this->resolveRelatableQuery($request)
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
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'options' => $this->resolveOptions($request),
                ];
            })
        );
    }
}
