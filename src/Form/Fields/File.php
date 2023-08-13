<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class File extends MorphToMany
{
    use ResolvesFields;

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.file';

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
    public function __construct(Form $form, string $label, string $key = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $key, $relation);

        $this->type('file')->multiple(false);

        $this->disk(Config::get('root.media.disk', 'public'));

        $this->fields = new Fields($form, $this->fields());
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
    public function resolveOptions(): array
    {
        return $this->resolveValue()
            ->map(function (Medium $medium): FileOption {
                return $this->newOption($medium, $this->resolveDisplay($medium));
            })
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function newOption(Model $value, string $label): FileOption
    {
        return FileOption::make($value, $label)
            ->setAttribute('name', sprintf('%s__attached[]', $this->getAttribute('name')));
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
    public function store(Request $request, UploadedFile $file): Medium
    {
        $path = $file->store('root-uploads', ['disk' => 'local']);

        $medium = (Medium::proxy())::makeFromPath($path, ['disk' => $this->disk]);

        if (! is_null($this->storageResolver)) {
            call_user_func_array($this->storageResolver, [$request, $medium, $path]);
        }

        $request->user()->uploads()->save($medium);

        MoveFile::withChain($medium->convertible() ? [new PerformConversions($medium)] : [])
            ->dispatch($medium, $path, false);

        return $medium;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return $request->input($this->getKey().'__attached');
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
        $this->resolveModel()->saved(function () use ($request, $value): void {
            $files = Arr::wrap($request->file($this->getKey(), []));

            $ids = array_map(function (UploadedFile $file) use ($request): string {
                return $this->store($request, $file)->getKey();
            }, $files);

            $value = array_merge((array) $value, $ids);

            $this->resolveHydrate($request, $value);

            $keys = $this->getRelation()->sync($value);

            if ($this->prunable) {
                // delete $keys['detached'];
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'options' => $this->resolveOptions(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(Router $router): void
    {
        parent::registerRoutes($router);

        $router->prefix($this->getUriKey())->group(function (Router $router): void {
            $this->fields->registerRoutes($router);
        });
    }
}
