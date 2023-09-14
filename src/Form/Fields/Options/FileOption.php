<?php

namespace Cone\Root\Form\Fields\Options;

use Cone\Root\Models\Medium;

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
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'fields' => $this->fields?->all() ?: [],
            'file_name' => $this->model->file_name,
            'is_image' => $this->model->isImage,
            'medium' => $this->model,
            'processing' => false,
            'url' => $this->model->getUrl('thumbnail') ?: $this->model->getUrl('original'),
            'uuid' => $this->model->uuid,
        ]);
    }
}
