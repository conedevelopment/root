<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Editor;
use Cone\Root\Tests\TestCase;

final class EditorTest extends TestCase
{
    protected Editor $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Editor('Content');
    }

    public function test_an_editor_field_has_configuration(): void
    {
        $this->field->withConfig(function () {
            return ['key' => 'value'];
        });

        $this->assertSame(['key' => 'value'], $this->field->getConfig());
    }

    public function test_an_editor_field_resolves_media_field(): void
    {
        $this->field->withMedia(function ($media) {
            $media->label('Attachments');
        });

        $this->assertSame('content-media', $this->field->getMedia()->getModelAttribute());
    }

    public function test_an_editor_field_register_routes(): void
    {
        $this->field->withMedia();

        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($this->app['request'], $router);
        });

        $this->assertSame('/posts/fields/content', $this->field->getUri());

        $this->assertArrayHasKey(
            trim($this->field->getMedia()->getUri(), '/'),
            $this->app['router']->getRoutes()->get('GET')
        );
    }
}
