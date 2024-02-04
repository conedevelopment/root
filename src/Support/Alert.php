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
     */
    protected string $message;

    /**
     * The alert type.
     */
    protected string $type;

    /**
     * Create a new alert instance.
     */
    public function __construct(string $message, string $type = self::INFO)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Make a new info alert instance.
     */
    public static function info(string $message): array
    {
        return (new static($message, static::INFO))->toArray();
    }

    /**
     * Make a new success alert instance.
     */
    public static function success(string $message): array
    {
        return (new static($message, static::SUCCESS))->toArray();
    }

    /**
     * Make a new error alert instance.
     */
    public static function error(string $message): array
    {
        return (new static($message, static::ERROR))->toArray();
    }

    /**
     * Make a new warning alert instance.
     */
    public static function warning(string $message): array
    {
        return (new static($message, static::WARNING))->toArray();
    }

    /**
     * Get the array representation of the object.
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
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Get the string representation of the object.
     */
    public function __toString(): string
    {
        return $this->message;
    }
}
