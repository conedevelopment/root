<form {{ $attrs }}>
    @csrf
    @method($method)
    <div class="form-group-stack form-group-stack--bordered form-group-container">
        @foreach($fields as $field)
            {!! $field !!}
        @endforeach
    </div>
</form>
