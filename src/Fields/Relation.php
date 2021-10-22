<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Relation extends Field
{
    /**
     * The relation name on the model.
     *
     * @var string
     */
    protected string $relation;

    /**
     * Indicates if the field should be nullable.
     *
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * The display key name.
     *
     * @var string
     */
    protected string $displayKeyName = 'id';

    /**
     * Create a new relation field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @param  string|null  $relation
     * @return void
     */
    public function __construct(string $label, ?string $name = null, ?string $relation = null)
    {
        parent::__construct($label, $name);

        $this->relation = $relation ?: Str::camel($label);
    }

    /**
     * Set the nullable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function nullable(bool $value = true): self
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * Set the display key name.
     *
     * @param  string  $value
     * @return $this
     */
    public function display(string $value): self
    {
        // $model->getDisplayKeyName

        $this->displayKeyName = $value;

        return $this;
    }

    /**
     * Format the value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatter)) {
            $default = parent::resolveDefault($request, $model);

            $this->formatter = function () use ($default): mixed {
                if ($default instanceof Model) {
                    return $default->getAttribute($this->displayKeyName);
                } elseif ($default instanceof Collection) {
                    return $default->map->getAttribute($this->displayKeyName)->toArray();
                }

                return $default;
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Resolve the default value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveDefault(Request $request, Model $model): mixed
    {
        $default = parent::resolveDefault($request, $model);

        if ($default instanceof Model) {
            return $default->getKey();
        } elseif ($default instanceof Collection) {
            return $default->map->getKey()->toArray();
        }

        return $default;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            '_nullable' => $this->nullable,
        ]);
    }
}
