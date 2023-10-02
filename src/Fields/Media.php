<?php

namespace Cone\Root\Fields;

use Cone\Root\Fields\Options\FileOption;
use Cone\Root\Fields\Options\PendingFileOption;
use Cone\Root\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
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
     * Paginate the results.
     */
    public function paginate(Request $request): array
    {
        return $this->resolveRelatableQuery($request)
            ->latest()
            ->paginate($request->input('per_page'))
            ->withQueryString()
            ->setPath($this->apiUri)
            ->through(function (Medium $related): array {
                return $this->toOption($related)->toRenderedArray();
            })
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->getModel()->saved(function () use ($request, $value): void {
            $this->resolveHydrate($request, $value);

            $keys = $this->getRelation()->sync($value);

            if ($this->prunable && ! empty($keys['detached'])) {
                $this->prune($request, $keys['detached']);
            }
        });
    }

    /**
     * Handle the file upload.
     */
    public function upload(Request $request): JsonResponse
    {
        $accept = $this->getAttribute('accept');

        $data = $request->validate(['file' => [
            'required',
            'file',
            Rule::when(! is_null($accept), ['mimetypes:'.$accept]),
        ]]);

        $option = $this->store($request, $data['file']);

        return new JsonResponse($option->toRenderedArray(), JsonResponse::HTTP_CREATED);
    }

    /**
     * {@inheritdoc}
     */
    public function store(Request $request, UploadedFile $file): FileOption
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => Config::get('root.media.tmp_dir'),
        ]);

        $disk->append($file->getClientOriginalName(), $file->get());

        if ($request->header('X-Chunk-Index') !== $request->header('X-Chunk-Total')) {
            return new PendingFileOption(new Medium(), '');
        }

        return $this->stored($request, $disk->path($file->getClientOriginalName()));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        return array_merge($data, [
            'modalKey' => $this->getModalKey(),
            'config' => [
                'accept' => $this->getAttribute('accept', '*'),
                'multiple' => $this->multiple,
                'chunk_size' => Config::get('root.media.chunk_size'),
            ],
            'selection' => array_map(function (FileOption $option): array {
                return $option->toRenderedArray();
            }, $data['options'] ?? []),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): JsonResponse
    {
        return match ($request->method()) {
            'GET' => new JsonResponse($this->paginate($request)),
            'POST' => $this->upload($request),
            'DELETE' => new JsonResponse(['deleted' => $this->prune($request, $request->input('ids', []))]),
            default => parent::toResponse($request),
        };
    }
}
