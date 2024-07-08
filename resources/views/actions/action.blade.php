<x-root::modal :title="$name" :key="$modalKey">
    @if(! empty($fields))
        <div class="form-group-stack form-group-stack--bordered form-group-container">
            @foreach($fields as $field)
                @include($field['template'], $field)
            @endforeach
        </div>
    @endif
    <x-slot:footer>
        <form id="{{ $key }}" method="POST" action="{{ $url }}" x-on:submit="open = false">
            @csrf
            <button type="submit" class="btn btn--primary">
                {{ __('Run') }}
            </button>
            <button type="button" class="btn btn--outline-primary" x-on:click="open = false">
                {{ __('Cancel') }}
            </button>
            <template x-for="selected in selection">
                <input type="hidden" name="models[]" x-bind:value="selected">
            </template>
            <input type="hidden" name="all" x-bind:value="selectedAllMatchingQuery">
        </form>
    </x-slot:footer>
</x-root::modal>
