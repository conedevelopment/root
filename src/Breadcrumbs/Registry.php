<?php

namespace Cone\Root\Breadcrumbs;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class Registry
{
    /**
     * The registered patterns.
     */
    protected array $patterns = [];

    /**
     * Register a set of patterns.
     */
    public function patterns(array $patterns): void
    {
        $this->patterns = array_merge($this->patterns, $patterns);
    }

    /**
     * Register a pattern.
     */
    public function pattern(string $pattern, Closure|string $label): void
    {
        $this->patterns[$pattern] = $label;
    }

    /**
     * Resolve the breadcrumbs using the given request.
     */
    public function resolve(Request $request): array
    {
        $items = [];

        $route = $request->path();

        foreach ($this->patterns as $uri => $label) {
            if (str_starts_with($route, trim($this->replaceRoutePlaceholders($uri, $request->route()), '/'))) {
                $items[] = [
                    'uri' => $this->replaceRoutePlaceholders($uri, $request->route()),
                    'label' => $label instanceof Closure ? call_user_func_array($label, [$request]) : $label,
                ];
            }
        }

        usort($items, fn (array $a, array $b): int => strlen($a['uri']) - strlen($b['uri']));

        return $items;
    }

    /**
     * Replace the route placeholder for the given uri.
     */
    protected function replaceRoutePlaceholders(string $uri, Route $route): string
    {
        foreach (array_merge($route->defaults, $route->originalParameters()) as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return $uri;
    }
}
