<div class="form-group--row" x-data="{ readonly: {{ json_encode($attrs->get('readonly')) }} }">
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
        <input {{ $attrs }} value="{{ $value }}" x-bind:readonly="readonly">
        @if($suffix)
            <div class="form-group-label" style="white-space: nowrap;">{!! $suffix !!}</div>
        @endif
        <button type="button" class="btn btn--outline-primary" x-on:click="readonly = false">
            {{ __('Edit') }}
        </button>
    </div>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
