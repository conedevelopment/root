<div class="form-group--row" x-data="{ selection: {{ json_encode($options) }} }">
    <span class="form-label">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </span>
    <div class="file-list">
        <div class="form-group">
            @unless($attrs->get('readonly') || $attrs->get('disabled'))
                <button
                    @if(! $attrs->get('multiple')) x-show="selection.length === 0" @endif
                    type="button"
                    class="btn btn--primary btn--lg btn--block"
                    x-on:click="$dispatch('open-{{ $modalKey }}')"
                >
                    {{ __('Select :model', ['model' => $label]) }}
                </button>
            @endunless
            <ul class="file-list__items" x-show="selection.length > 0" x-cloak>
                <template x-for="(item, index) in selection" :key="item.value">
                    <li x-html="item.html"></li>
                </template>
            </ul>
        </div>
        @if($invalid)
            <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
        @endif
        @if($help)
            <span class="form-description">{!! $help !!}</span>
        @endif
    </div>

    @unless($attrs->get('readonly') || $attrs->get('disabled'))
        <x-root::modal
            :title="$label"
            :key="$modalKey"
            x-data="relation('{{ $url }}', {{ json_encode($config) }})"
        >
            <form
                action="{{ $url }}"
                method="GET"
                autocomplete="off"
                x-ref="form"
                x-on:change.debounce.300ms="fetch"
                x-on:submit.prevent
                x-on:open-{{ $modalKey }}.window.once="fetch"
            >
                <div class="form-group-stack form-group-stack--bordered form-group-container">
                    @foreach($filters as $filter)
                        @include($filter['template'], $filter)
                    @endforeach
                </div>
            </form>
            <ul class="file-list__items" x-show="items.length > 0" x-cloak>
                <template x-for="(item, index) in items" :key="item.value">
                    <li class="file-list-item">
                        <div class="file-list-item__column">
                            <span x-html="item.label"></span>
                        </div>
                        <div class="file-list-item__actions">
                            <div class="form-group form-group--vertical-check">
                                <label class="form-check">
                                    <input
                                        type="checkbox"
                                        class="form-check__control"
                                        x-bind:checked="selected(item)"
                                        x-on:change="toggle(item)"
                                    >
                                </label>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
            <x-slot:footer class="modal__footer--space-between">
                <div class="modal__column"></div>
                <div class="modal__column">
                    <button type="button" class="btn btn--primary" x-on:click="open = false">
                        {{ __('Close') }}
                    </button>
                </div>
            </x-slot:footer>
        </x-root::modal>
    @endunless
</div>

{{-- Scripts --}}
@unless($attrs->get('readonly') || $attrs->get('disabled'))
    @pushOnce('scripts')
        {{
            Vite::withEntryPoints('resources/js/relation.js')
                ->useBuildDirectory('vendor/root/build')
                ->useHotFile(public_path('vendor/root/hot'))
        }}
    @endPushOnce
@endunless
