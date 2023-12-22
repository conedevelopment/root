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
     * The modal subtitle.
     */
    protected ?string $subtitle = null;

    /**
     * The modal open state.
     */
    protected bool $open = false;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title, ?string $subtitle = null, ?string $key = null, bool $open = false)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->key = strtolower($key ?: Str::random());
        $this->open = $open;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.modal', [
            'key' => $this->key,
            'subtitle' => $this->subtitle,
            'title' => $this->title,
            'open' => $this->open,
        ]);
    }
}
