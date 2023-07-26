<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span style="color: var(--spruce-alert-color-danger);">*</span>
        @endif
    </label>
    <input {{ $attrs->class(['form-file']) }}>
    @foreach($value as $attached)
        <input type="hidden" name="{{ $attrs->get('name') }}__attached[]" value="{{ $attached->getKey() }}">
    @endforeach
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
