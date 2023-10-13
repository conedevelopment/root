<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <select {{ $attrs }}>
        @if($nullable)
            <option value="">--- {{ $label }} ---</option>
        @endif
        @foreach($options as $option)
            @if(isset($option['options']))
                <optgroup label="{{ $option['label'] }}">
                    @foreach($option as $o)
                        <option {{ $o['attrs'] }}>{{ $o['label'] }}</option>
                    @endforeach
                </optgroup>
            @else
                <option {{ $option['attrs'] }}>{{ $option['label'] }}</option>
            @endif
        @endforeach
    </select>
</div>
