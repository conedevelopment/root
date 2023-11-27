<div class="app-card" x-data="table()">
    <div class="app-card__header">
        <h2 class="app-card__title">{{ $title }}</h2>
        <div class="app-card__actions">
            @include('root::table.filters')
        </div>
    </div>
    <div class="app-card__body">
        <div class="data-table">
            @includeWhen(! empty($actions), 'root::table.actions')
            @if($data->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table--hover">
                        <thead>
                            <tr>
                                @if(! empty($actions))
                                    <th style="inline-size: 3.25rem;" scope="col">
                                        <span class="sr-only">{{ __('Select') }}</span>
                                        <label class="form-check" aria-label="{{ __('Select all items') }}">
                                            <input
                                                class="form-check__control"
                                                type="checkbox"
                                                x-on:change="selection = event.target.checked ? {{ $data->pluck('id')->toJson() }} : []"
                                            >
                                        </label>
                                    </th>
                                @endif
                                @foreach($data[0]['fields'] as $column)
                                    @include('root::table.column', $column)
                                @endforeach
                                <th scope="col">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                                <tr>
                                    @if(! empty($actions))
                                        <td>
                                            <label class="form-check" aria-label="">
                                                <input
                                                    class="form-check__control"
                                                    type="checkbox"
                                                    value="{{ $row['id'] }}"
                                                    x-model="selection"
                                                >
                                            </label>
                                        </td>
                                    @endif
                                    @foreach($row['fields'] as $cell)
                                        @include('root::table.cell', $cell)
                                    @endforeach
                                    <td>
                                        <div class="data-table__actions">
                                            @can('view', $row['model'])
                                                <a href="{{ $row['url'] }}" class="btn btn--light btn--sm btn--icon" aria-label="{{ __('View') }}" data-turbo-frame="_top">
                                                    <x-root::icon name="eye" class="btn__icon" />
                                                </a>
                                            @endcan
                                            @can('update', $row['model'])
                                                <a href="{{ $row['url'] }}/edit" class="btn btn--light btn--sm btn--icon" aria-label="{{ __('Edit') }}" data-turbo-frame="_top">
                                                    <x-root::icon name="edit" class="btn__icon" />
                                                </a>
                                            @endcan
                                            @can('delete', $row['model'])
                                                <form action="{{ $row['url'] }}" method="POST" onsubmit="return window.confirm('{{ __('Are you sure?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn--delete btn--sm btn--icon" aria-label="{{ __('Delete') }}">
                                                        <x-root::icon name="trash" class="btn__icon" />
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="data-table__footer">
                    <div class="data-table__footer-column">
                        <div class="form-group">
                            <label class="sr-only" for="per_page">
                                {{ __('Number of results') }}
                            </label>
                            <select
                                form="{{ $key }}"
                                class="form-control form-control--sm"
                                id="per_page"
                                name="{{ $perPageKey }}"
                                onchange="this.form.requestSubmit()"
                            >
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected($option === $data->perPage())>
                                        {{ $option }}
                                    </option>
                                @endforeach
                                @if(! in_array($data->perPage(), $perPageOptions))
                                    <option value="{{ $data->perPage() }}" selected>
                                        {{ __('Custom (:perPage)', ['perPage' => $data->perPage()]) }}
                                    </option>
                                @endif
                            </select>
                        </div>
                        <p>{{ __('Showing :from to :to of :total results', ['from' => $data->firstItem(), 'to' => $data->lastItem(), 'total' => $data->total()]) }}</p>
                    </div>
                    {!! $data->links('root::table.pagination') !!}
                </div>
            @else
                <x-root::alert>
                    {{ __('No results found.') }}
                </x-root::alert>
            @endif
        </div>
    </div>
</div>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/table.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
