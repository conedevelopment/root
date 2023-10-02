<optgroup {{ $attrs }}>
    @foreach($options as $option)
        {!! $option->render() !!}
    @endforeach
</optgroup>
