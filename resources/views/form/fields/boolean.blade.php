<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span style="color: var(--spruce-alert-color-danger);">*</span>
        @endif
    </label>
    <div class="form-group">
        <label class="form-switch form-switch--sm">
            <input {{ $attrs->class(['form-switch__control']) }} value="1">
        </label>
    </div>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
