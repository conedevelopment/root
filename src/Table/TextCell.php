<?php

namespace Cone\Root\Table;

abstract class TextCell extends Cell
{
    /**
     * The blade template.
     */
    protected string $template = 'root::table.text-cell';
}
