<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

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
    protected string $template = 'root::form.fields.media';

    /**
     * Get the modal key.
     */
    public function getModalKey(): string
    {
        return sprintf('%s-field-%s', $this->form->getKey(), $this->getKey());
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
        $value = $this->resolveValue();

        return $this->resolveRelatableQuery()
            ->latest()
            ->paginate($request->input('per_page'))
            ->withQueryString()
            ->setPath($this->replaceRoutePlaceholders($request->route()))
            ->through(function (Medium $related) use ($value): FileOption {
                return $this->newOption($related, $this->resolveDisplay($related))
                    ->selected($value->contains($related));
            })
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function store(Request $request, UploadedFile $file): FileOption
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => Storage::disk('local')->path(Config::get('root.media.tmp_dir')),
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
    public function newOption(Model $value, string $label): FileOption
    {
        // $relation = $this->getRelation();

        // $pivot = $value->relationLoaded($relation->getPivotAccessor())
        //     ? $value->getRelation($relation->getPivotAccessor())
        //     : $relation->newPivot();

        return parent::newOption($value, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        $data = parent::data($request);

        return array_merge($data, [
            'modalKey' => $key = $this->getModalKey(),
            'config' => [
                'accept' => $this->getAttribute('accept', '*'),
                'event' => 'update-'.$key,
                'multiple' => $this->multiple,
                'selection' => array_map(fn (FileOption $option): array => $option->toArray(), $data['options']),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->get('/', [MediaController::class, 'index']);
        $router->post('/', [MediaController::class, 'store']);
        $router->delete('/', [MediaController::class, 'destroy']);
    }
}
