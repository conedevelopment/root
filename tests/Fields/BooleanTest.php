<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use App\Models\User;
use Cone\Root\Fields\Boolean;
use Cone\Root\Tests\TestCase;
use Illuminate\Http\Request;

final class BooleanTest extends TestCase
{
    protected Boolean $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Boolean('Admin');
    }

    public function test_a_boolean_field_has_boolean_template(): void
    {
        $this->assertSame('root::fields.boolean', $this->field->getTemplate());
    }

    public function test_a_boolean_field_has_checkbox_type(): void
    {
        $this->assertSame('checkbox', $this->field->getAttribute('type'));
    }

    public function test_a_boolean_field_has_checked_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('checked'));

        $this->field->checked();
        $this->assertTrue($this->field->getAttribute('checked'));

        $this->field->checked(false);
        $this->assertFalse($this->field->getAttribute('checked'));
    }

    public function test_a_boolean_field_gets_value_for_hydrate(): void
    {
        $request = Request::createFrom($this->app['request']);

        $request->merge(['admin' => 0]);
        $this->assertFalse($this->field->getValueForHydrate($request));

        $request->merge(['admin' => 1]);
        $this->assertTrue($this->field->getValueForHydrate($request));
    }

    public function test_a_boolean_field_resolves_value(): void
    {
        $model = new User;
        $this->assertFalse($this->field->resolveValue($this->app['request'], $model));

        $model->forceFill(['admin' => false]);
        $this->assertFalse($this->field->resolveValue($this->app['request'], $model));

        $model->forceFill(['admin' => true]);
        $this->assertTrue($this->field->resolveValue($this->app['request'], $model));
    }

    public function test_a_boolean_field_resolves_format(): void
    {
        $model = new User;

        $model->forceFill(['admin' => false]);
        $this->assertSame(
            '<span class="status status--danger">No</span>',
            $this->field->resolveFormat($this->app['request'], $model)
        );

        $model->forceFill(['admin' => true]);
        $this->assertSame(
            '<span class="status status--success">Yes</span>',
            $this->field->resolveFormat($this->app['request'], $model)
        );
    }
}
