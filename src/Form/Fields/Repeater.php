<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class Repeater extends Fieldset
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.repeater';

    /**
     * Get the add new label.
     */
    public function addNewLabel(): string
    {
        return __('Add :name', ['name' => Str::singular($this->label)]);
    }

    /**
     * {@inheritdoc}
     */
    public function withFields(Closure $callback): static
    {
        //

        return parent::withFields($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'addNewLabel' => $this->addNewLabel(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): JsonResponse
    {
        return parent::toResponse($request);
    }
}
