<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Repeater;
use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;

class RepeaterTest extends TestCase
{
    protected Repeater $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = Repeater::make('Properties')
            ->withFields(function () {
                return [
                    new Text('Name'),
                ];
            });
    }

    public function test_a_repeater_field_has_repeater_template(): void
    {
        $this->assertSame('root::fields.repeater', $this->field->getTemplate());
    }

    public function test_a_repeater_field_registers_routes(): void
    {
        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($this->app['request'], $router);
        });

        $this->assertSame('/posts/fields/properties', $this->field->getUri());

        $this->assertArrayHasKey(
            trim($this->field->getUri(), '/'),
            $this->app['router']->getRoutes()->get('POST')
        );
    }
}
