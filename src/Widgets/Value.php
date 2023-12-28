<?php

namespace Cone\Root\Widgets;

use DatePeriod;
use Illuminate\Database\Eloquent\Builder;

abstract class Value extends Metric
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::widgets.value';

    /**
     * The widget icon.
     */
    protected ?string $icon = null;

    /**
     * Create a new widget instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->class('app-widget--summary');
    }

    /**
     * Set the icon.
     */
    public function icon(string $value): static
    {
        $this->icon = $value;

        return $this;
    }

    /**
     * Apply the aggregate function on the query.
     */
    protected function aggregate(Builder $query, DatePeriod $period, string $fn, string $column, ?string $dateColumn = null): Builder
    {
        $dateColumn ??= $query->getModel()->getQualifiedCreatedAtColumn();

        $extended = $this->period(
            $period->getStartDate()->sub($period->getEndDate()->diff($period->getStartDate(), true))->format('c'),
            $period->getEndDate()->format('c')
        );

        return parent::aggregate($query, $extended, $fn, $column, $dateColumn)->when(
            $period->getStartDate()->getTimestamp() > 0,
            function (Builder $query) use ($period, $dateColumn): Builder {
                return $query->selectRaw(sprintf(
                    "(case when %s between '%s' and '%s' then 'current' else 'previous' end) as `__interval`",
                    $query->getQuery()->getGrammar()->wrap($dateColumn),
                    $period->getStartDate()->format('Y-m-d H:i:s'),
                    $period->getEndDate()->format('Y-m-d H:i:s')
                ))->groupBy('__interval');
            }
        );
    }

    /**
     * Get the query result.
     */
    public function result(Builder $query): array
    {
        $result = array_merge(['current' => 0, 'pervious' => 0], parent::result($query));

        return [
            'current' => round($result['current'], 2),
            'pervious' => round($result['previous'], 2),
            'trend' => round(($result['current'] - $result['previous']) / (($result['current'] + $result['previous']) / 2) * 100, 1),
        ];
    }

    /**
     * Convert the widget to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'icon' => $this->icon,
        ]);
    }
}
