<?php

namespace Cone\Root\Notifications;

use Illuminate\Contracts\Support\Arrayable;

class RootMessage implements Arrayable
{
    /**
     * The message subject.
     */
    protected ?string $subject = null;

    /**
     * The message content.
     */
    protected ?string $message = null;

    /**
     * Create a new message instance.
     */
    public function __construct(?string $subject = null, ?string $message = null)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Set the message subject.
     */
    public function subject(string $value): static
    {
        $this->subject = $value;

        return $this;
    }

    /**
     * Set the message message.
     */
    public function message(string $value): static
    {
        $this->message = $value;

        return $this;
    }

    /**
     * Get the array form of the message.
     */
    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}
