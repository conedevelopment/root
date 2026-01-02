<turbo-frame id="form-{{ $key }}">
    <form
        id="{{ $key }}"
        method="POST"
        action="{{ $action }}"
        autocomplete="off"
        x-on:submit="($event) => {
            if ($event.currentTarget.getAttribute('action') !== '{{ $hydrateUrl }}') {
                $event.currentTarget.parentElement.setAttribute('target', '_top');
            }
        }"
        x-on:hydrate.stop="($event) => {
            $event.currentTarget.parentElement.removeAttribute('target');
            $event.currentTarget.setAttribute('action', '{{ $hydrateUrl }}');
            $event.currentTarget.requestSubmit();
        }"
        @if($uploads) enctype="multipart/form-data" @endif
    >
        @csrf
        @method($method)
        <div class="l-row">
            <div class="l-row__column">
                <div class="app-card app-card--edit">
                    <div class="app-card__header">
                        <h2 class="app-card__title">
                            {{ __(':resource Details', ['resource' => $modelName]) }}
                        </h2>
                    </div>
                    <div class="app-card__body">
                        <div class="form-group-stack form-group-stack--bordered form-group-container">
                            @foreach($fields as $field)
                                @include($field['template'], $field)
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</turbo-frame>
