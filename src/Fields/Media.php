<?php

namespace Cone\Root\Fields;

use Cone\Root\Filters\MediaSearch;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Models\Medium;
use Cone\Root\Traits\HasMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class Media extends File
{
    /**
     * Indicates if the component is multiple.
     */
    protected bool $multiple = true;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.media';

    /**
     * Get the route parameter name.
     */
    public function getRouteParameterName(): string
    {
        return 'field';
    }

    /**
     * Get the modal key.
     */
    public function getModalKey(): string
    {
        return sprintf('media-field-%s', $this->getModelAttribute());
    }

    /**
     * Set the multiple attribute.
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
    }

    /**
     * Get the model.
     */
    public function getModel(): Model
    {
        return $this->model ?: new class() extends Model
        {
            use HasMedia;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            //
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filters(Request $request): array
    {
        return [
            new MediaSearch(),
        ];
    }

    /**
     * Paginate the relatable models.
     */
    public function paginateRelatable(Request $request, Model $model): LengthAwarePaginator
    {
        return $this->resolveFilters($request)
            ->apply($request, $this->resolveRelatableQuery($request, $model))
            ->paginate($request->input('per_page'))
            ->withQueryString()
            ->through(function (Medium $related) use ($request, $model): array {
                $option = $this->toOption($request, $model, $related);

                return array_merge($option, [
                    'html' => View::make('root::fields.file-option', $option)->render(),
                ]);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $model->saved(function (Model $model) use ($request, $value): void {
            $this->resolveHydrate($request, $model, $value);

            $keys = $this->getRelation($model)->sync($value);

            if ($this->prunable && ! empty($keys['detached'])) {
                $this->prune($request, $model, $keys['detached']);
            }
        });
    }

    /**
     * Handle the file upload.
     */
    public function upload(Request $request, Model $model): array
    {
        $accept = $this->getAttribute('accept');

        $data = $request->validate(['file' => [
            'required',
            'file',
            Rule::when(! is_null($accept), ['mimetypes:'.$accept]),
        ]]);

        return $this->store($request, $model, $data['file']);
    }

    /**
     * {@inheritdoc}
     */
    public function store(Request $request, Model $model, UploadedFile $file): array
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => Config::get('root.media.tmp_dir'),
        ]);

        $disk->append($file->getClientOriginalName(), $file->get());

        if ($request->header('X-Chunk-Index') !== $request->header('X-Chunk-Total')) {
            return array_merge($this->toOption($request, $model, new Medium()), [
                'processing' => true,
                'fileName' => null,
            ]);
        }

        return $this->stored($request, $model, $disk->path($file->getClientOriginalName()));
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->match(['GET', 'POST', 'DELETE'], '/', MediaController::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $data = parent::toInput($request, $model);

        $filters = $this->resolveFilters($request)->authorized($request);

        return array_merge($data, [
            'modalKey' => $this->getModalKey(),
            'config' => [
                'accept' => $this->getAttribute('accept', '*'),
                'multiple' => $this->multiple,
                'chunk_size' => Config::get('root.media.chunk_size'),
                'query' => $filters->mapToData($request),
            ],
            'selection' => array_map(static function (array $option): array {
                return array_merge($option, [
                    'html' => View::make('root::fields.file-option', $option)->render(),
                ]);
            }, $data['options'] ?? []),
            'url' => $this->modelUrl($model),
            'filters' => $filters->renderable()
                ->map(function (RenderableFilter $filter) use ($request, $model): array {
                    return $filter->toField()
                        ->removeAttribute('name')
                        ->setAttributes([
                            'x-model.debounce.300ms' => $filter->getKey(),
                            'x-bind:readonly' => 'processing',
                        ])
                        ->toInput($request, $this->getRelation($model)->getRelated());
                })
                ->all(),
        ]);
    }
}
