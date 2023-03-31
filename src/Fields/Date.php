<?php

namespace Cone\Root\Fields;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date as BaseDate;

class Date extends Field
{
    /**
     * The date format.
     */
    protected string $format = 'Y-m-d';

    /**
     * The timezone.
     */
    protected ?string $timezone = null;

    /**
     * Indicates if the field should include time.
     */
    protected bool $withTime = false;

    /**
     * The Vue component.
     */
    protected string $component = 'DateTime';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('date');
    }

    /**
     * Set the "min" attribute.
     */
    public function min(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('min', (string) $value);
    }

    /**
     * Set the "max" attribute.
     */
    public function max(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('max', (string) $value);
    }

    /**
     * Set the with time attribute.
     */
    public function withTime(bool $value = true): static
    {
        $this->format = $value ? 'Y-m-d H:i:s' : 'Y-m-d';

        $this->withTime = $value;

        return $this;
    }

    /**
     * Set the timezone.
     */
    public function timezone(?string $value = null): static
    {
        $this->timezone = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                return is_null($value) ? $value : BaseDate::parse($value)->tz($this->timezone)->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Get the input representation of the field.
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'with_time' => $this->withTime,
        ]);
    }
}
