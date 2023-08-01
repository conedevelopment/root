<?php

namespace Cone\Root\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class Modal extends Component
{
    /**
     * The modal key.
     */
    protected string $key;

    /**
     * The modal title.
     */
    protected string $title;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title, string $key = null)
    {
        $this->title = $title;
        $this->key = strtolower($key ?: Str::random());
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.modal', [
            'title' => $this->title,
            'key' => $this->key,
        ]);
    }
}
