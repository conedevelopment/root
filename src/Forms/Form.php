<?php

namespace Cone\Root\Forms;

use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Form
{
    protected Model $model;

    protected Fields $fields;

    public function __construct(Model $model, Fields $fields)
    {
        //
    }

    public function handle(Request $request): void
    {
        //
    }

    public function validate(Request $request): array
    {
        return [];
    }

    public function build(Request $request): array
    {
        return [];
    }
}
