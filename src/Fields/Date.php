<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date as BaseDate;

class Date extends Field
{
    /**
     * The date format.
     *
     * @var string
     */
    protected string $format = 'Y-m-d H:i';

    /**
     * The timezone.
     *
     * @var string|null
     */
    protected ?string $timezone = null;

    /**
     * The Vue compoent.
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
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                return is_null($value) ? $value : BaseDate::parse($value)->tz($this->timezone)->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
