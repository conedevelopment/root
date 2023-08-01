<form method="POST" action="{{ $url }}" id="{{ $key }}">
    @csrf
    @method($method)
    <div class="form-group-stack form-group-stack--bordered form-group-container">
        @foreach($fields as $field)
            {!! $field !!}
        @endforeach
    </div>
</form>
