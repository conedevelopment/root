<div class="app-card" x-data="{ selection: [], all: false }">
    <div class="app-card__header">
        <h2 class="app-card__title">{{ $title }} </h2>
        {!! $form !!}
    </div>
    <div class="app-card__body">
        <div class="data-table">
            @includeWhen(! empty($actions), 'root::table.actions')
            <div class="table-responsive">
                <table class="table table--hover">
                    <thead>
                        <tr>
                            @foreach($columns as $column)
                                {!! $column !!}
                            @endforeach
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
                        <label class="sr-only" for="{{ $attrs->get('id') }}:per_page">
                            {{ __('Number of results') }}
                        </label>
                        <select
                            form="{{ $form->getAttribute('id') }}"
                            class="form-control form-control--sm"
                            id="{{ $attrs->get('id') }}:per_page"
                            name="{{ $attrs->get('id') }}:per_page"
                        >
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}" @selected($option === $items->perPage())>
                                    {{ $option }}
                                </option>
                            @endforeach
                            @if(! in_array($items->perPage(), $perPageOptions))
                                <option value="{{ $items->perPage() }}" selected>
                                    {{ __('Custom (:perPage)', ['perPage' => $items->perPage()]) }}
                                </option>
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
