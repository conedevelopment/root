<?php

namespace Cone\Root\Tests\Settings;

use Cone\Root\Settings\Registry;
use Cone\Root\Settings\Repository;
use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\Date;

class SettingsTest extends TestCase
{
    protected Registry $registry;

    public function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry(new Repository);
    }

    public function test_setting_can_be_set(): void
    {
        $value = $this->registry->set('foo', 'bar');
        $this->assertSame('bar', $value);

        $this->assertDatabaseHas('root_settings', ['key' => 'foo', 'value' => 'bar']);
    }

    public function test_setting_value_with_cast(): void
    {
        $this->registry->cast('ran_at', 'datetime');

        $value = $this->registry->get('ran_at');
        $this->assertNull($value);

        $value = $this->registry->set('ran_at', $now = Date::now());
        $this->assertSame(
            $now->__toString(),
            $this->registry->query()->firstWhere('key', 'ran_at')->value
        );

        $this->assertSame(
            $now->__toString(),
            $this->registry->get('ran_at')->__toString()
        );
    }

    public function test_setting_can_be_get(): void
    {
        $value = $this->registry->get('foo');
        $this->assertNull($value);

        $value = $this->registry->get('foo', 'bar');
        $this->assertSame('bar', $value);
    }

    public function test_setting_can_be_deleted(): void
    {
        $value = $this->registry->set('foo', 'bar');
        $this->assertSame('bar', $value);

        $value = $this->registry->get('foo');
        $this->assertSame('bar', $value);

        $this->registry->delete('foo');
        $this->assertNull($this->registry->get('foo'));

        $this->assertDatabaseMissing('root_settings', ['key' => 'foo']);
    }
}
