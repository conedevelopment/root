<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Metric extends Widget
{
    /**
     * The metric timezone.
     */
    protected string $timezone = 'UTC';

    /**
     * Indicates whether the widget is async loaded.
     */
    protected bool $async = true;

    /**
     * The query resolver.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Resolve the query.
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException;
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Set the query resolver callback.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Get the current range.
     */
    public function getCurrentRange(Request $request): string
    {
        $default = $this->getDefaultRange();

        $range = $request->input('range', $default);

        return array_key_exists($range, $this->ranges()) ? $range : $default;
    }

    /**
     * Get the default range.
     */
    public function getDefaultRange(): string
    {
        return 'MONTH';
    }

    /**
     * Create a new date period.
     */
    public function period(string $start, string $end, string $duration = 'P1D'): DatePeriod
    {
        return new DatePeriod(
            new DateTime($start, $this->timezone()),
            new DateInterval($duration),
            new DateTime($end, $this->timezone()),
            DatePeriod::INCLUDE_END_DATE
        );
    }

    /**
     * Make a new timezone instance.
     */
    public function timezone(): DateTimeZone
    {
        return new DateTimeZone($this->timezone);
    }

    /**
     * Create a new period form the given request.
     */
    public function periodFromRequest(Request $request): DatePeriod
    {
        return $this->period(
            $this->rangeToDateTime($this->getCurrentRange($request)),
            'now'
        );
    }

    /**
     * Convert the range to date time object.
     */
    protected function rangeToDateTime(string|int $range): string
    {
        $value = match ($range) {
            'TODAY' => strtotime('today'),
            'DAY' => strtotime('-1 day'),
            'WEEK' => strtotime('-1 week'),
            'MONTH' => strtotime('-1 month'),
            'QUARTER' => strtotime('-3 months'),
            'YEAR' => strtotime('-1 year'),
            'ALL' => strtotime('1970-01-01 00:00:00'),
            default => strtotime(sprintf('-%d days', (int) $range)),
        };

        return date('c', $value);
    }

    /**
     * Get the available ranges.
     */
    public function ranges(): array
    {
        return [
            'TODAY' => __('Today'),
            'WEEK' => __('Week to today'),
            'MONTH' => __('Month to today'),
            'QUARTER' => __('Quarter to today'),
            'YEAR' => __('Year to today'),
            'ALL' => __('All time'),
        ];
    }

    /**
     * Get the data.
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'ranges' => $this->ranges(),
            'currentRange' => $this->getCurrentRange($request),
            'data' => (! $this->async || $request->isTurboFrameRequest()) ? $this->calculate($request) : [],
        ]);
    }

    /**
     * Aggregate count values.
     */
    protected function count(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->result(
            $this->aggregate($query, $this->periodFromRequest($request), 'count', $column, $dateColumn)
        );
    }

    /**
     * Aggregate average values.
     */
    protected function avg(Request $request, Builder $query, string $column, ?string $dateColumn = null): array
    {
        return $this->result(
            $this->aggregate($query, $this->periodFromRequest($request), 'avg', $column, $dateColumn)
        );
    }

    /**
     * Aggregate min values.
     */
    protected function min(Request $request, Builder $query, string $column, ?string $dateColumn = null): array
    {
        return $this->result(
            $this->aggregate($query, $this->periodFromRequest($request), 'min', $column, $dateColumn)
        );
    }

    /**
     * Aggregate max values.
     */
    protected function max(Request $request, Builder $query, string $column, ?string $dateColumn = null): array
    {
        return $this->result(
            $this->aggregate($query, $this->periodFromRequest($request), 'max', $column, $dateColumn)
        );
    }

    /**
     * Aggregate sum values.
     */
    protected function sum(Request $request, Builder $query, string $column, ?string $dateColumn = null): array
    {
        return $this->result(
            $this->aggregate($query, $this->periodFromRequest($request), 'sum', $column, $dateColumn)
        );
    }

    /**
     * Apply the aggregate function on the query.
     */
    protected function aggregate(Builder $query, DatePeriod $period, string $fn, string $column, ?string $dateColumn = null): Builder
    {
        $dateColumn ??= $query->getModel()->getCreatedAtColumn();

        $column = $column === '*' ? $column : $query->qualifyColumn($column);

        return $query->whereBetween($query->qualifyColumn($dateColumn), [
            $period->getStartDate()->format('Y-m-d H:i:s'),
            $period->getEndDate()->format('Y-m-d H:i:s'),
        ])->selectRaw(sprintf(
            '%s(%s) as `__value`',
            $fn,
            $query->getQuery()->getGrammar()->wrap($column)
        ));
    }

    /**
     * Get the query result.
     */
    public function result(Builder $query): array
    {
        return $query->toBase()->pluck('__value', '__interval')->all();
    }

    /**
     * Calculate the results.
     */
    public function calculate(Request $request): array
    {
        return $this->count($request, $this->resolveQuery($request));
    }
}
