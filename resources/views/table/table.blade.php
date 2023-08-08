<div class="app-card" x-data="{ selection: [], all: false }">
    <div class="app-card__header">
        <h2 class="app-card__title">Items</h2>
        {!! $form !!}
    </div>
    <div class="app-card__body">
        <div class="data-table">
            <div x-cloak x-show="selection.length > 0" class="alert alert--info data-table-alert">
                <span><span x-text="selection.length"></span> items selected.</span>
                <div class="data-table-alert__actions">
                    <button
                        type="button"
                        class="btn btn--primary btn--sm"
                        x-on:click="selection = {{ $items->pluck('id')->toJson() }}"
                    >
                        Select all ({{ $items->total() }})
                    </button>
                    <button
                        type="button"
                        class="btn btn--primary btn--sm"
                        x-on:click="selection = []; all = false"
                    >
                        {{ __('Clear') }}
                    </button>
                    @if(! empty($actions))
                        <select
                            class="form-control form-control--sm"
                            aria-label="{{ __('Actions') }}"
                            x-on:change="$dispatch('open-'+$event.target.value)"
                        >
                            <option value="">--- {{ __('Select Action') }} ---</option>
                            @foreach($actions as $action)
                                <option value="{{ $action->getModalKey() }}">{{ $action->getName() }}</option>
                            @endforeach
                        </select>
                        @foreach($actions as $action)
                            {!! $action !!}
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table--hover">
                    <thead>
                        <tr>
                            @if(! empty($actions))
                                <th style="width: 3.25rem;">
                                    <span class="sr-only">{{ __('Select') }}</span>
                                    <label class="form-check" aria-label="{{ __('Select all items') }}">
                                        <input class="form-check__control" type="checkbox">
                                    </label>
                                </th>
                            @endif
                            @foreach($columns as $column)
                                {!! $column !!}
                            @endforeach
                            <th scope="col">
                                <span class="sr-only">{{ __('Actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                @foreach($item['cells'] as $cell)
                                    {!! $cell !!}
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="data-table__footer">
                <div class="data-table__footer-column">
                    <div class="form-group">
                        <label class="sr-only" for="{{ $key }}:per_page">{{ __('Number of results') }} </label>
                        <select form="{{ $key }}-filters" class="form-control form-control--sm" id="{{ $key }}:per_page" name="{{ $key }}:per_page">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}" @selected($option === $items->perPage())>{{ $option }}</option>
                            @endforeach
                            @if(! in_array($items->perPage(), $perPageOptions))
                                <option value="{{ $items->perPage() }}" selected>{{ __('Custom (:perPage)', ['perPage' => $items->perPage()]) }}</option>
                            @endif
                        </select>
                    </div>
                    <p>{{ __('Showing :from to :to of :total results', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()]) }}</p>
                </div>
                {!! $items->links('root::table.pagination') !!}
            </div>
        </div>
    </div>
</div>
