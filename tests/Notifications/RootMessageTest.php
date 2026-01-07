<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Notifications;

use Cone\Root\Notifications\RootMessage;
use Cone\Root\Tests\TestCase;

final class RootMessageTest extends TestCase
{
    public function test_root_message_can_be_created(): void
    {
        $message = new RootMessage('Test Subject', 'Test Message');

        $this->assertInstanceOf(RootMessage::class, $message);
    }

    public function test_root_message_can_set_subject(): void
    {
        $message = new RootMessage;
        $message->subject('Test Subject');

        $this->assertSame('Test Subject', $message->toArray()['subject']);
    }

    public function test_root_message_can_set_message(): void
    {
        $message = new RootMessage;
        $message->message('Test Message');

        $this->assertSame('Test Message', $message->toArray()['message']);
    }

    public function test_root_message_can_set_data(): void
    {
        $message = new RootMessage;
        $message->data(['key' => 'value']);

        $this->assertSame(['key' => 'value'], $message->toArray()['data']);
    }

    public function test_root_message_can_be_converted_to_array(): void
    {
        $message = new RootMessage('Subject', 'Message');
        $message->data(['foo' => 'bar']);

        $this->assertSame([
            'subject' => 'Subject',
            'message' => 'Message',
            'data' => ['foo' => 'bar'],
        ], $message->toArray());
    }

    public function test_root_message_methods_are_fluent(): void
    {
        $message = new RootMessage;

        $result = $message->subject('Test')
            ->message('Message')
            ->data(['key' => 'value']);

        $this->assertSame($message, $result);
    }
}
