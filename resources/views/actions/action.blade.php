<x-root::modal :title="$name" :key="$modalKey">
    <form id="{{ $key }}" method="POST" action="{{ $url }}">
        @csrf
        <div class="form-group-stack form-group-stack--bordered form-group-container">
            @foreach($fields as $field)
                @include($field['template'], $field)
            @endforeach
        </div>
    </form>

    <x-slot:footer>
        <button form="{{ $key }}" type="submit" class="btn btn--primary">
            {{ __('Run') }}
        </button>
        <button type="button" class="btn btn--outline-primary" x-on:click="open = false">
            {{ __('Cancel') }}
        </button>
    </x-slot>
</x-root::modal>
