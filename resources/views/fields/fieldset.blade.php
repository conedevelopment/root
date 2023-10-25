<fieldset>
    <legend>{{ $label }}</legend>
    @foreach($fields as $field)
        @include($field['template'], $field)
    @endforeach
</fieldset>
