<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BelongsTo extends Relation
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'FormSelect';

    /**
     * Hydrate the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return void
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $model->saving(function (Model $model) use ($value): void {
            call_user_func([$model, $this->relation])->associate($value);
        });
    }

    /**
     * Resolve the options for the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function resolveOptions(Request $request, Model $model): array
    {
        if (! method_exists($model, $this->relation)) {
            return [];
        }

        $relation = call_user_func([$model, $this->relation]);

        return $relation->getModel()
                        ->newQuery()
                        ->get()
                        ->mapWithKeys(static function (Model $model): array {
                            return [$model->getKey() => $model->getKey()];
                        })
                        ->toArray();
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'options' => $this->resolveOptions($request, $model),
        ]);
    }
}
