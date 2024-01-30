<?php

namespace Cone\Root\Notifications;

use Illuminate\Contracts\Support\Arrayable;

class RootMessage implements Arrayable
{
    /**
     * The subject.
     */
    protected ?string $subject = null;

    /**
     * The message.
     */
    protected ?string $message = null;

    /**
     * The data.
     */
    protected array $data = [];

    /**
     * Create a new message instance.
     */
    public function __construct(?string $subject = null, ?string $message = null)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Set the subject.
     */
    public function subject(string $value): static
    {
        $this->subject = $value;

        return $this;
    }

    /**
     * Set the message.
     */
    public function message(string $value): static
    {
        $this->message = $value;

        return $this;
    }

    /**
     * Set the data.
     */
    public function data(array $value): static
    {
        $this->data = $value;

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
            'data' => $this->data,
        ];
    }
}
