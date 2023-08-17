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
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toFragment();
    }

    /**
     * {@inheritdoc}
     */
    public function render(): View
    {
        return App::make('view')->make($this->template, [
            'attrs' => $this->newAttributeBag(),
            'medium' => $this->model,
            'label' => $this->label,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'file_name' => $this->model->file_name,
            'is_image' => $this->model->isImage,
            'processing' => false,
            'url' => $this->model->getUrl('thumbnail') ?: $this->model->getUrl('original'),
            'uuid' => $this->model->uuid,
        ]);
    }
}
