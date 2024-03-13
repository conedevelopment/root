<?php

namespace Cone\Root\Fields;

use Closure;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date as DateFactory;

class Date extends Field
{
    /**
     * The date format.
     */
    protected string $format = 'Y-m-d';

    /**
     * The timezone.
     */
    protected string $timezone = 'UTC';

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
    public function timezone(string $value): static
    {
        $this->timezone = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                return is_null($value) ? $value : DateFactory::parse($value)->setTimezone($this->timezone)->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
