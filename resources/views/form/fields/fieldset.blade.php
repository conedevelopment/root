<fieldset>
    <legend>{{ $label }}</legend>
    @foreach($fields as $field)
        {!! $field->render() !!}
    @endforeach
</fieldset>
