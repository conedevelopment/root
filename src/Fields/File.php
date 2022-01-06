<?php

namespace Cone\Root\Fields;

use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;

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
        $this->rules(['file']);
    }

    /**
     * Persist the request value on the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function persist(Request $request, Model $model): void
    {
        $media = array_map(static function (UploadedFile $file) use ($request): array {
            $medium = $request->user()->uploads()->save(
                (Medium::proxy())::makeFrom($file->getRealPath())
            );

            $file->storeAs($medium->id, $medium->file_name, $medium->disk);

            return $medium->id;
        }, $this->getValueForHydrate($request, $model));

        $this->hydrate($request, $model, $media);
    }

    /**
     * Get the value for hydrating the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function getValueForHydrate(Request $request, Model $model): mixed
    {
        return $request->file($this->name);
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        //
    }
}
