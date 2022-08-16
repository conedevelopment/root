<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Editor;
use Cone\Root\Tests\TestCase;

class EditorTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Editor('Content');
    }

    /** @test */
    public function an_editor_field_has_editor_component()
    {
        $this->assertSame('Editor', $this->field->getComponent());
    }

    /** @test */
    public function an_editor_field_has_configuration()
    {
        $this->field->withConfig(function () {
            return ['key' => 'value'];
        });

        $this->assertSame(['key' => 'value'], $this->field->getConfig());
    }

    /** @test */
    public function an_editor_field_resolves_media_field()
    {
        $this->field->withMedia(function ($media) {
            $media->label('Attachments');
        });

        $this->assertSame('Attachments', $this->field->getMedia()->label);
    }

    /** @test */
    public function an_editor_field_register_routes()
    {
        $this->field->withMedia();

        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($this->request, $router);
        });

        $this->assertSame('/posts/fields/content', $this->field->getUri());

        $this->assertArrayHasKey(
            trim($this->field->getMedia()->getUri(), '/'),
            $this->app['router']->getRoutes()->get('GET')
        );
    }
}
