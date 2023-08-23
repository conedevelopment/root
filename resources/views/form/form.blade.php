@if($errors->isNotEmpty())
    <x-root::alert type="danger">
        {{ __('Some error occurred when submitting the form!') }}
    </x-root::alert>
@endif

<form method="POST" action="{{ $url }}" id="{{ $key }}" autocomplete="off">
    @csrf
    @method($method)

    <div class="l-row l-row--sidebar">
        <div class="l-row__column">
            <div class="app-card app-card--edit">
                <div class="app-card__header">
                    <h2 class="app-card__title">General</h2>
                </div>
                <div class="app-card__body">
                    <div class="form-group-stack form-group-stack--bordered form-group-container">
                        @foreach($fields as $field)
                            {!! $field !!}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
