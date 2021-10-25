<?php

namespace Cone\Root\Fields;

use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

class ID extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label = 'ID', ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->visibility[] = static function (Request $request, string $action): bool {
            return in_array($action, [Resource::INDEX, Resource::SHOW]);
        };
    }

    /**
     * Indicates if the field is UUID.
     *
     * @var bool
     */
    protected bool $uuid = false;

    /**
     * Mark the field as UUID.
     *
     * @param  bool  $value
     * @return $this
     */
    public function uuid(bool $value = true): self
    {
        $this->uuid = $value;

        return $this;
    }
}
