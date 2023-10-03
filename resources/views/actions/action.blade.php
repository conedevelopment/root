<x-root::modal :title="$name" :key="$modalKey">
    <form method="POST" action="{{ $url }}">
        @csrf
        <div class="form-group-stack form-group-stack--bordered form-group-container">
            @foreach($fields as $field)
                {!! $field !!}
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
