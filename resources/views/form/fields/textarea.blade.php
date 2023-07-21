<div class="form-group--row">
    <label class="form-label" for="{{ $attributes->get('id') }}">{{ $label }}</label>
    <textarea {{ $attributes->merge(['class' => 'form-control']) }}>{{ $value }}</textarea>
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
