<div class="form-group">
    <fieldset>
        <legend>{{ $label }}</legend>
        @if(empty($fields))
            <x-root::alert>{{ __('No available fields.') }}</x-root::alert>
        @else
            @foreach($fields as $field)
                @include($field['template'], $field)
            @endforeach
        @endif
    </fieldset>
</div>
