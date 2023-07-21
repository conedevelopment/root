<div class="form-group--row">
    <label class="form-label" for="{{ $attributes->get('id') }}">{{ $label }}</label>
    <input {{ $attributes->class(['form-control', 'form-control--invalid' => $invalid]) }} value="{{ $value }}">
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
