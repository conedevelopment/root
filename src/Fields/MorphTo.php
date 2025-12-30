<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @extends \Cone\Root\Fields\BelongsTo<\Illuminate\Database\Eloquent\Relations\MorphTo>
 */
class MorphTo extends BelongsTo
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.relation';

    /**
     * The morph types.
     */
    protected array $types = [];

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }

    /**
     * {@inheritdoc}
     */
    public function filters(Request $request): array
    {
        $filters = parent::filters($request);

        if ($this->isAsync()) {
            $typeFilter = Select::make(__('Type'), 'type')
                ->options(array_map(
                    static function (string $type): string {
                        return __(Str::of($type)->classBasename()->headline()->value());
                    },
                    array_combine($this->types, $this->types)
                ))
                ->toFilter();

            array_unshift($filters, $typeFilter);
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRelatableQuery(Request $request, Model $model): Builder
    {
        $relation = $this->getRelation($model);

        $type = Str::before((string) $this->getOldValue($request), ':') ?: $request->query($relation->getMorphType());

        $model->setAttribute(
            $relation->getMorphType(),
            $type ?: $model->getAttribute($relation->getMorphType()) ?: ($this->types[0] ?? null)
        );

        return parent::resolveRelatableQuery($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        $value = is_null($value) ? $value : explode(':', $value);

        $related = match (true) {
            ! is_null($value) => tap(new $value[0], static function (Model $related) use ($value): void {
                $related->forceFill([$related->getKeyName() => $value[1]]);
            }),
            default => $value,
        };

        parent::resolveHydrate($request, $model, $related);
    }

    /**
     * {@inheritdoc}
     */
    public function newOption(Model $related, string $label): Option
    {
        return new Option(sprintf('%s:%s', $related::class, $related->getKey()), $label);
    }

    /**
     * Set the morph types.
     */
    public function types(array $types): static
    {
        $this->types = $types;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'url' => sprintf('%s/search', $this->replaceRoutePlaceholders($request->route())),
            'morphTypeName' => $name = $this->getRelation($model)->getMorphType(),
            'morphType' => $model->getAttribute($name),
        ]);
    }
}
