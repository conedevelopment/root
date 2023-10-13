<?php

namespace Cone\Root\Fields;

use Cone\Root\Models\Medium;
use Cone\Root\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class Media extends File
{
    /**
     * Indicates if the component is async.
     */
    protected bool $async = true;

    /**
     * Indicates if the component is multiple.
     */
    protected bool $multiple = true;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.media';

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
     * Paginate the results.
     */
    public function paginate(Request $request, Model $model): array
    {
        return $this->resolveRelatableQuery($request, $model)
            ->latest()
            ->paginate($request->input('per_page'))
            ->withQueryString()
            ->setPath($this->apiUri)
            ->through(function (Medium $related) use ($request, $model): array {
                $option = $this->toOption($request, $model, $related);

                $option['fields'] = array_map(static function (Field $field) use ($request, $model): array {
                    return $field->toFormComponent($request, $model);
                }, $option['fields']);

                return array_merge($option, [
                    'html' => View::make('root::fields.file-option', $option)->render(),
                ]);
            })
            ->toArray();
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
                'file_name' => null,
            ]);
        }

        return $this->stored($request, $model, $disk->path($file->getClientOriginalName()));
    }

    /**
     * {@inheritdoc}
     */
    public function toFormComponent(Request $request, Model $model): array
    {
        $data = parent::toFormComponent($request, $model);

        return array_merge($data, [
            'modalKey' => $this->getModalKey(),
            'config' => [
                'accept' => $this->getAttribute('accept', '*'),
                'multiple' => $this->multiple,
                'chunk_size' => Config::get('root.media.chunk_size'),
            ],
            'selection' => array_map(static function (array $option) use ($request, $model): array {
                $option['fields'] = $option['fields']->mapToFormComponents($request, $model);

                return array_merge($option, [
                    'html' => View::make('root::fields.file-option', $option)->render(),
                ]);
            }, $data['options'] ?? []),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handleApiRequest(Request $request, Model $model): JsonResponse
    {
        return match ($request->method()) {
            'GET' => new JsonResponse($this->paginate($request, $model)),
            'POST' => new JsonResponse($this->upload($request, $model), JsonResponse::HTTP_CREATED),
            'DELETE' => new JsonResponse(['deleted' => $this->prune($request, $model, $request->input('ids', []))]),
            default => parent::handleApiRequest($request, $model),
        };
    }
}
