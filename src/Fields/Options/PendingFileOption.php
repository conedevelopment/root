<?php

namespace Cone\Root\Fields\Options;

class PendingFileOption extends FileOption
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.pending-file-option';

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'processing' => true,
            'file_name' => null,
        ];
    }
}
