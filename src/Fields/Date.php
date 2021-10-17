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
     * Set the timezone.
     *
     * @param  string  $value
     * @return $this
     */
    public function timezone(?string $value = null): self
    {
        $this->timezone = $value;

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
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): string {
                return BaseDate::parse($value)->tz($this->timezone)->format($this->format);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
