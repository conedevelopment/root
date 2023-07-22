<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span style="color: var(--spruce-alert-color-danger);">*</span>
        @endif
    </label>
    <div class="form-group form-group--vertical-check">
        @foreach($options as $option)
            {!! $option->render() !!}
        @endforeach
    </div>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
