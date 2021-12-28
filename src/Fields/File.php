<?php

namespace Cone\Root\Fields;

use Cone\Root\Models\Medium;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

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

        $this->rules = array_merge($this->rules, ['*' => ['file']]);
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
     * Regsiter the routes for the async component.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    protected function routes(Resource $resource, string $uri): void
    {
        //
    }
}
