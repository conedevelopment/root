<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    @if(! empty($options))
        <div class="form-group--stacked">
            @if($prefix)
                <div class="form-group-label" style="white-space: nowrap;">{!! $prefix !!}</div>
            @endif
            <select {{ $attrs }}>
                @if($nullable)
                    <option value="">--- {{ $label }} ---</option>
                @endif
                @foreach($options as $option)
                    @if(isset($option['options']))
                        <optgroup label="{{ $option['label'] }}">
                            @foreach($option['options'] as $o)
                                <option {{ $o['attrs'] }}>{{ $o['label'] }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option {{ $option['attrs'] }}>{{ $option['label'] }}</option>
                    @endif
                @endforeach
            </select>
            @if($suffix)
                <div class="form-group-label" style="white-space: nowrap;">{!! $suffix !!}</div>
            @endif
        </div>
        @if($invalid)
            <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
        @endif
        @if($help)
            <span class="form-description">{!! $help !!}</span>
        @endif
    @else
        <x-root::alert>{{ __('No available options.') }}</x-root::alert>
    @endif
</div>
