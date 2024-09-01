<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\URL;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class URLTest extends TestCase
{
    protected URL $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new URL('Profile URL');
    }

    public function test_an_url_field_has_url_type(): void
    {
        $this->assertSame('url', $this->field->getAttribute('type'));
    }

    public function test_a_url_field_resolves_format(): void
    {
        $model = new User;

        $model->forceFill(['profile_url' => 'https://github.com/conedevelopment']);
        $this->assertSame(
            '<a href="https://github.com/conedevelopment" title="https://github.com/conedevelopment" data-turbo="false" target="_blank">URL</a>',
            $this->field->resolveFormat($this->app['request'], $model)
        );

        $model->forceFill(['profile_url' => $this->app['url']->to('/root')]);
        $this->assertSame(
            '<a href="http://localhost/root" title="http://localhost/root">URL</a>',
            $this->field->resolveFormat($this->app['request'], $model)
        );
    }
}
