<div class="form-group--row" x-data="{ value: {{ $value ?: 'null' }} }">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <div class="range-group">
        <div class="range-group__inner">
            <button
                type="button"
                class="btn btn--primary btn--sm btn--icon"
                aria-label="{{ __('Decrement') }}"
                x-on:click="value--"
                x-bind:disabled="value <= {{ $attrs->get('min') }}"
            >
                <x-root::icon name="minus" class="btn__icon" />
            </button>
            <input {{ $attrs->class(['form-range', 'range-group__control']) }} value="{{ $value }}" x-model="value">
            <button
                type="button"
                class="btn btn--primary btn--sm btn--icon"
                aria-label="{{ __('Increment') }}"
                x-on:click="value++"
                x-bind:disabled="value >= {{ $attrs->get('max') }}"
            >
                <x-root::icon name="plus" class="btn__icon" />
            </button>
        </div>
        <span class="form-label">
            @if($prefix)
                <span class="form-label__prefix">{!! $prefix !!}</span>
            @endif
            <span class="form-label__value" x-text="value">{{ $value }}</span>
            @if($suffix)
                <span class="form-label__suffix">{!! $suffix !!}</span>
            @endif
        </span>
    </div>
</div>
