<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Slug;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class SlugTest extends TestCase
{
    protected Slug $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Slug('Slug', 'name');
    }

    public function test_a_slug_field_has_slug_template(): void
    {
        $this->assertSame('root::fields.slug', $this->field->getTemplate());
    }

    public function test_a_slug_field_can_be_nullable(): void
    {
        $this->assertFalse($this->field->isNullable());
        $this->field->nullable();
        $this->assertTrue($this->field->isNullable());
    }

    public function test_a_slug_field_generates_value_from_model_attributes(): void
    {
        $user = new User([
            'email' => 'test@cone.test',
            'name' => 'Test',
            'password' => 'secret',
        ]);

        $this->field->from('email')->unique()->separator('_');

        $this->field->persist(
            $this->app['request'], $user, $this->field->getValueForHydrate($this->app['request'])
        );

        $user->save();

        $this->assertSame('test_at_conetest', $user->name);
    }
}
