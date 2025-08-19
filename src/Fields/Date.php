<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Root;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date as DateFactory;

class Date extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.date';

    /**
     * The date format.
     */
    protected string $format = 'Y-m-d';

    /**
     * The timezone.
     */
    protected string $timezone;

    /**
     * Indicates if the field should include time.
     */
    protected bool $withTime = false;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('date');
        $this->step(1);
        $this->timezone(Root::instance()->getTimezone());
    }

    /**
     * Set the "min" HTML attribute.
     */
    public function min(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('min', is_string($value) ? $value : $value->format('Y-m-d'));
    }

    /**
     * Set the "max" HTML attribute.
     */
    public function max(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('max', is_string($value) ? $value : $value->format('Y-m-d'));
    }

    /**
     * Set the "step" HTML attribute.
     */
    public function step(int $value): static
    {
        return $this->setAttribute('step', $value);
    }

    /**
     * Set the with time attribute.
     */
    public function withTime(bool $value = true): static
    {
        $this->withTime = $value;

        $this->format = $value ? 'Y-m-d H:i:s' : 'Y-m-d';

        $this->type($value ? 'datetime-local' : 'date');

        return $this;
    }

    /**
     * Set the timezone.
     */
    public function timezone(string|DateTimeZone $value): static
    {
        $this->timezone = $value instanceof DateTimeZone ? $value->getName() : $value;

        $this->suffix($this->timezone);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): ?string
    {
        $value = parent::getValueForHydrate($request);

        if (! is_null($value)) {
            $value = DateFactory::parse($value, $this->timezone)
                ->setTimezone(Config::get('app.timezone'))
                ->toISOString();
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(Model $model): mixed
    {
        $value = parent::getValue($model);

        return $this->parseValue($value);
    }

    /**
     * Parse the given value.
     */
    public function parseValue(mixed $value): ?DateTimeInterface
    {
        if (is_null($value)) {
            return $value;
        }

        return DateFactory::parse($value, Config::get('app.timezone'))
            ->setTimezone($this->timezone);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                return is_null($value) ? $value : $value->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Set the filterable attribute.
     */
    public function filterable(bool|Closure $value = true, ?Closure $callback = null): static
    {
        $callback ??= function (Request $request, Builder $query, mixed $value, string $attribute): Builder {
            return $query->whereDate($query->qualifyColumn($attribute), $value);
        };

        return parent::filterable($value, $callback);
    }

    /**
     * Get the form component data.
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return array_merge(parent::toDisplay($request, $model), [
            'value' => $this->resolveFormat($request, $model),
        ]);
    }

    /**
     * Get the filter representation of the field.
     */
    public function toFilter(): Filter
    {
        return new class($this) extends RenderableFilter
        {
            protected Date $field;

            public function __construct(Date $field)
            {
                parent::__construct($field->getModelAttribute());

                $this->field = $field;
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $this->field->resolveFilterQuery($request, $query, $value);
            }

            public function toField(): Field
            {
                return Date::make($this->field->getLabel(), $this->getRequestKey())
                    ->value(function (Request $request): ?DateTimeInterface {
                        return $this->field->parseValue($this->getValue($request));
                    })
                    ->suffix('');
            }
        };
    }
}
