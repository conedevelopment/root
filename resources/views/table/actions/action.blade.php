<x-root::modal :title="$name" :key="$modalKey">
    {!! $form !!}

    <x-slot:footer>
        <button form="{{ $form->getAttribute('id') }}" type="submit" class="btn btn-primary">
            {{ __('Run') }}
        </button>
        <button type="button" class="btn btn--outline-primary" x-on:click="open = false">
            {{ __('Cancel') }}
        </button>
    </x-slot>
</x-root::modal>
