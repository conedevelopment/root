<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Collections\FieldCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Resource implements Arrayable
{
    /**
     * The model class.
     *
     * @var string
     */
    protected string $model;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected array $with = [];

    /**
     * The fields resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model for the resource.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the key for the resource.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of($this->getModel())->classBasename()->lower()->kebab();
    }

    /**
     * Get the model instance of the query.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelInstance(): Model
    {
        static $instance;

        if (! isset($instance)) {
            $instance = new $this->getModel();
        }

        return $instance;
    }

    /**
     * Make a new eloquent query instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with);
    }

    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withFields(Closure $callback): self
    {
        $this->fieldsResolver = $callback;

        return $this;
    }

    /**
     * Collect the resolved fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Collections\FieldCollection
     */
    protected function collectFields(Request $request): FieldCollection
    {
        $fields = FieldCollection::make($this->fields($request));

        if (is_callable($this->withFieldsResolver)) {
            $fields->push(
                ...call_user_func_array($this->withFieldsResolver, [$request])
            );
        }

        return $fields;
    }

    /**
     * Define the filters for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Define the actions for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
        ];
    }

    /**
     * Get the index representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toIndex(Request $request): array
    {
        //

        return [];
    }
}
