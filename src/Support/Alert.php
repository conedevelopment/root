<?php

namespace Cone\Root\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Stringable;

class Alert implements Arrayable, Jsonable, Stringable
{
    public const INFO = 'info';
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'warning';

    /**
     * The alert message.
     *
     * @var string
     */
    protected string $message;

    /**
     * The alert type.
     *
     * @var string
     */
    protected string $type;

    /**
     * Create a new alert instance.
     *
     * @param  string  $message
     * @param  string  $type
     * @return void
     */
    public function __construct(string $message, string $type = self::INFO)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the array representation of the object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    /**
     * Get the JSON representation of the object.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Get the string representation of the object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
