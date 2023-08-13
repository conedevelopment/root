<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Models\Medium;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

class FileOption extends Option implements Arrayable
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.file-option';

    /**
     * The Medium instance.
     */
    protected Medium $medium;

    /**
     * Create a new option instance.
     */
    public function __construct(Medium $medium, string $label)
    {
        $this->label = $label;
        $this->medium = $medium;
    }

    /**
     * Render the option.
     */
    public function render(): View
    {
        return App::make('view')->make($this->template, [
            'attrs' => $this->newAttributeBag(),
            'label' => $this->label,
            'value' => $this->medium,
        ]);
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return [
            //
        ];
    }
}
