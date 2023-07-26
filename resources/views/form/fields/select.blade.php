<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <select {{ $attrs->class(['form-control', 'form-control--invalid' => $invalid]) }}>
        @if($nullable)
            <option value="">--- {{ $label }} ---</option>
        @endif
        @foreach($options as $option)
            {!! $option->render() !!}
        @endforeach
    </select>
</div>
