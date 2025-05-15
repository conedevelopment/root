<div class="form-group--row" x-data>
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <div class="form-group--stacked">
        @if($prefix)
            <div class="form-group-label" style="white-space: nowrap;">{!! $prefix !!}</div>
        @endif
        <input {{ $attrs }} value="{{ $value }}" x-ref="input">
        @if($suffix)
            <div class="form-group-label" style="white-space: nowrap;">{!! $suffix !!}</div>
        @endif
    </div>
    <span class="form-description">
        <button type="button" class="btn btn--light btn--sm" x-on:click="$refs.input.value = ''">
            {{ __('Clear') }}
        </button>
    </span>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
