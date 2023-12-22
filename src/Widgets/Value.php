<?php

namespace Cone\Root\Widgets;

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

        return $this->range($from, $this->getCurrentRange($request));
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

                return $query->whereBetween($column, [$previous, $to])->groupByRaw(sprintf(
                    "(case when %s between '%s' and '%s' then 0 else 1 end)",
                    $query->getQuery()->getGrammar()->wrap($column),
                    (string) $previous,
                    (string) $from
                ));
            });
    }

    /**
     * Count values.
     */
    public function count(Request $request, string $column = '*'): array
    {
        return $this->resolveQuery($request)
            ->getQuery()
            ->selectRaw(sprintf('count(%s) as `total`', $column))
            ->get()
            ->pluck('total')
            ->toArray();
    }

    /**
     * Calculate the metric data.
     */
    public function calculate(Request $request): array
    {
        $data = $this->count($request);

        $previous = count($data) === 1 ? 0 : $data[0];

        $current = count($data) === 1 ? $data[0] : $data[1];

        return [
            'previous' => $previous,
            'current' => $current,
            'trend' => $previous === 0 ? 0 : round(($current - $previous) / (($current + $previous) / 2) * 100, 1),
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
