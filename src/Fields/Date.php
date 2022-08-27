<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date as BaseDate;

class Date extends Field
{
    /**
     * The date format.
     *
     * @var string
     */
    protected string $format = 'Y-m-d';

    /**
     * The timezone.
     *
     * @var string|null
     */
    protected ?string $timezone = null;

    /**
     * Indicates if the field should include time.
     *
     * @var bool
     */
    protected bool $withTime = false;

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'DateTime';

    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('date');
    }

    /**
     * Set the "min" attribute.
     *
     * @param  \DateTimeInterface|string  $value
     * @return $this
     */
    public function min(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('min', (string) $value);
    }

    /**
     * Set the "max" attribute.
     *
     * @param  \DateTimeInterface|string  $value
     * @return $this
     */
    public function max(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('min', (string) $value);
    }

    /**
     * Set the with time attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function withTime(bool $value = true): static
    {
        $this->format = $value ? 'Y-m-d H:i:s' : 'Y-m-d';

        $this->withTime = $value;

        return $this;
    }

    /**
     * Set the timezone.
     *
     * @param  string|null  $value
     * @return $this
     */
    public function timezone(?string $value = null): static
    {
        $this->timezone = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (RootRequest $request, Model $model, mixed $value): ?string {
                return is_null($value) ? $value : BaseDate::parse($value)->tz($this->timezone)->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'with_time' => $this->withTime,
        ]);
    }
}
