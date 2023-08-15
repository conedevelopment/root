<?php

namespace Cone\Root\Form\Fields;

class PendingFileOption extends FileOption
{
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
