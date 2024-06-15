<?php

namespace Tests\phpstan;

use Cone\Root\Fields\Field;
use Cone\Root\Traits\RegistersRoutes;

class FieldWithRoute extends Field
{
    use RegistersRoutes;

    /**
     * The Blade template.
     */
    protected string $template = 'field.with.route';
}
