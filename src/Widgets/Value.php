<?php

namespace Cone\Root\Widgets;

use Cone\Root\Widgets\Results\ValueResult;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
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
     * Get the previous period.
     */
    public function previousPeriod(string $range): DatePeriod
    {
        $current = $this->currentPeriod($range);

        $range = $range === 'TODAY' ? 'DAY' : $range;

        return new DatePeriod(
            (new DateTimeImmutable())->setTimestamp(
                $this->rangeToTimestamp($range, $current->getStartDate()->getTimestamp())
            ),
            new DateInterval('P1D'),
            new DateTimeImmutable($current->getStartDate()->format('c'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function resolveQuery(Request $request): Builder
    {
        $range = $this->getCurrentRange($request);

        return parent::resolveQuery($request)
            ->when(! empty($this->ranges()) && $range !== 'ALL', function (Builder $query) use ($range): Builder {
                $current = $this->currentPeriod($range);

                $previous = $this->previousPeriod($range);

                $column = $query->qualifyColumn($this->dateColumn);

                return $query->selectRaw(sprintf(
                    "(case when %s between '%s' and '%s' then 'previous' else 'current' end) as `__interval`",
                    $query->getQuery()->getGrammar()->wrap($column),
                    $previous->getStartDate()->format('Y-m-d H:i:s'),
                    $current->getStartDate()->format('Y-m-d H:i:s')
                ))->whereBetween($column, [
                    $previous->getStartDate()->format('Y-m-d H:i:s'),
                    $current->getEndDate()->format('Y-m-d H:i:s'),
                ])->groupBy('__interval');
            });
    }

    /**
     * Convert the query to result.
     */
    public function toResult(Request $request, Builder $query): ValueResult
    {
        $data = $query->getQuery()->get()->pluck('__value', '__interval')->all();

        return new ValueResult(
            $data['current'] ?? array_values($data)[0] ?? 0,
            $data['previous'] ?? null
        );
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
