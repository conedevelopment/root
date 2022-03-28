<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

class File extends Media
{
    /**
     * Indicates if the component is async.
     *
     * @var bool
     */
    protected bool $async = false;

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'File';

    /**
     * Create a new file field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @param  string|null  $relation
     * @return void
     */
    public function __construct(string $label, ?string $name = null, ?string $relation = null)
    {
        parent::__construct($label, $name, $relation);

        $this->type('file');
        $this->rules(['array']);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model): void
    {
        $media = array_map(function (UploadedFile $file) use ($request): array {
            $medium = $this->store($request, $file->getRealPath());

            $file->storeAs($medium->getKey(), $medium->file_name, $medium->disk);

            return $medium->getKey();
        }, $this->getValueForHydrate($request, $model));

        $this->hydrate($request, $model, $media);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request, Model $model): mixed
    {
        return Arr::wrap($request->file($this->name));
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        //
    }
}
