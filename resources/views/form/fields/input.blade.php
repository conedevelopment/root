<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <div class="form-group--stacked">
        @if($prefix)
            <div class="form-group-label">{!! $prefix !!}</div>
        @endif
        <input {{ $attrs->class(['form-control', 'form-control--invalid' => $invalid]) }} value="{{ $value }}">
        @if($suffix)
            <div class="form-group-label">{!! $suffix !!}</div>
        @endif
    </div>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
