<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Models\Medium;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

class FileOption extends RelationOption
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.file-option';

    /**
     * Create a new option instance.
     */
    public function __construct(Medium $medium, string $label)
    {
        parent::__construct($medium, $label);
    }

    /**
     * Render the option.
     */
    public function render(): View
    {
        return App::make('view')->make($this->template, [
            'attrs' => $this->newAttributeBag(),
            'label' => $this->label,
            'value' => $this->model,
        ]);
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'dimensions' => $this->model->dimensions,
            'file_name' => $this->model->file_name,
            'formatted_created_at' => $this->model->created_at->format('Y-m-d H:i'),
            'formatted_size' => $this->model->formattedSize,
            'is_image' => $this->model->isImage,
            'mime_type' => $this->model->mime_type,
            'uuid' => $this->model->uuid,
            'url' => $this->model->urls['original'] ?? null,
            'pivot' => [],
        ]);
    }
}
