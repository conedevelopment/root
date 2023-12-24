<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Widgets\Results\TrendResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

abstract class Trend extends Metric
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::widgets.trend';

    /**
     * The trend chart config.
     */
    protected array $config = [];

    /**
     * Create a new trend chart instance.
     */
    public function __construct()
    {
        $this->config = Config::get('root.widgets.trend', []);
    }

    /**
     * Set the configuration.
     */
    public function withConfig(Closure $callback): static
    {
        $this->config = call_user_func_array($callback, [$this->config]);

        return $this;
    }

    /**
     * Get the widget configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveQuery(Request $request): Builder
    {
        return parent::resolveQuery($request);
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(Request $request): TrendResult
    {
        return new TrendResult();
    }

    /**
     * {@inheritdoc}
     */
    public function ranges(): array
    {
        return [
            'TODAY' => __('Today'),
            'WEEK' => __('Week to today'),
            'MONTH' => __('Month to today'),
            'QUARTER' => __('Quarter to today'),
            'YEAR' => __('Year to today'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'config' => $this->config,
        ]);
    }
}
