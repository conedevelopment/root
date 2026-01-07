<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Support;

use Cone\Root\Support\Alert;
use Cone\Root\Tests\TestCase;

final class AlertTest extends TestCase
{
    public function test_an_alert_has_message_and_type(): void
    {
        $alert = new Alert('Test message', Alert::INFO);

        $this->assertSame(['message' => 'Test message', 'type' => Alert::INFO], $alert->toArray());
    }

    public function test_an_alert_can_be_info(): void
    {
        $alert = Alert::info('Info message');

        $this->assertSame(['message' => 'Info message', 'type' => Alert::INFO], $alert);
    }

    public function test_an_alert_can_be_success(): void
    {
        $alert = Alert::success('Success message');

        $this->assertSame(['message' => 'Success message', 'type' => Alert::SUCCESS], $alert);
    }

    public function test_an_alert_can_be_error(): void
    {
        $alert = Alert::error('Error message');

        $this->assertSame(['message' => 'Error message', 'type' => Alert::ERROR], $alert);
    }

    public function test_an_alert_can_be_warning(): void
    {
        $alert = Alert::warning('Warning message');

        $this->assertSame(['message' => 'Warning message', 'type' => Alert::WARNING], $alert);
    }

    public function test_an_alert_can_be_converted_to_json(): void
    {
        $alert = new Alert('Test message', Alert::INFO);

        $this->assertSame('{"message":"Test message","type":"info"}', $alert->toJson());
    }

    public function test_an_alert_can_be_converted_to_html(): void
    {
        $alert = new Alert('Test message', Alert::INFO);

        $this->assertSame('Test message', $alert->toHtml());
    }

    public function test_an_alert_can_be_converted_to_string(): void
    {
        $alert = new Alert('Test message', Alert::INFO);

        $this->assertSame('Test message', (string) $alert);
    }
}
