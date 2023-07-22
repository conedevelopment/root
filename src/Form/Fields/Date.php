<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;
use DateTimeInterface;

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
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $name = null)
    {
        parent::__construct($form, $label, $name);

        $this->type('date');
    }

    /**
     * Set the "min" HTML attribute.
     */
    public function min(string|DateTimeInterface $value): static
    {
        return $this->setAttribute('min', (string) $value);
    }

    /**
     * Set the "max" HTML attribute.
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

        $this->type($value ? 'datetime-local' : 'date');

        return $this;
    }

    /**
     * Set the timezone.
     */
    public function timezone(string $value = null): static
    {
        $this->timezone = $value;

        return $this;
    }
}
