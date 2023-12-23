<?php

namespace Cone\Root\Widgets;

use Cone\Root\Widgets\Results\ValueResult;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
        $this->setAttribute('class', 'app-widget app-widget--summary');
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
     * Get the previous period.
     */
    public function previous(Request $request): DateTimeInterface
    {
        $from = $this->from($request);

        $range = $this->getCurrentRange($request);

        return $this->range($from, $range === 'TODAY' ? 'DAY' : $range);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveQuery(Request $request): Builder
    {
        return parent::resolveQuery($request)
            ->when(! empty($this->ranges()) && $this->getCurrentRange($request) !== 'ALL', function (Builder $query) use ($request): Builder {
                $from = $this->from($request);

                $to = $this->to($request);

                $previous = $this->previous($request);

                $column = $query->getModel()->getQualifiedCreatedAtColumn();

                return $query->selectRaw(sprintf(
                    "(case when %s between '%s' and '%s' then 'previous' else 'current' end) as `__interval`",
                    $query->getQuery()->getGrammar()->wrap($column),
                    (string) $previous,
                    (string) $from
                ))->whereBetween($column, [$previous, $to])->groupBy('__interval');
            });
    }

    /**
     * Aggregate count values.
     */
    protected function count(Request $request, Builder $query, string $column = '*'): ValueResult
    {
        return $this->toResult(
            $this->aggregate($query, 'count', $column)
        );
    }

    /**
     * Aggregate average values.
     */
    protected function avg(Request $request, Builder $query, string $column): ValueResult
    {
        return $this->toResult(
            $this->aggregate($query, 'avg', $column)
        );
    }

    /**
     * Aggregate min values.
     */
    protected function min(Request $request, Builder $query, string $column): ValueResult
    {
        return $this->toResult(
            $this->aggregate($query, 'min', $column)
        );
    }

    /**
     * Aggregate max values.
     */
    protected function max(Request $request, Builder $query, string $column): ValueResult
    {
        return $this->toResult(
            $this->aggregate($query, 'max', $column)
        );
    }

    /**
     * Aggregate sum values.
     */
    protected function sum(Request $request, Builder $query, string $column): ValueResult
    {
        return $this->toResult(
            $this->aggregate($query, 'sum', $column)
        );
    }

    /**
     * Apply the aggregate function on the query.
     */
    protected function aggregate(Builder $query, string $fn, string $column): Builder
    {
        return $query->selectRaw(
            sprintf('%s(%s) as `__value`', $fn, $query->getQuery()->getGrammar()->wrap($column))
        );
    }

    /**
     * Calculate the metric data.
     */
    public function calculate(Request $request): ValueResult
    {
        return $this->count($request, $this->resolveQuery($request));
    }

    /**
     * Convert the query to result.
     */
    public function toResult(Builder $query): ValueResult
    {
        $data = $query->getQuery()->get()->pluck('__value', '__interval')->all();

        return new ValueResult($data['current'] ?? 0, $data['previous'] ?? 0);
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
