<div class="form-group--row">
    <label class="form-label" for="{{ $attributes->get('id') }}">{{ $label }}</label>
    <input {{ $attributes->merge(['class' => 'form-control']) }} value="{{ $value }}">
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
