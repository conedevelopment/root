<x-root::modal :title="$name" :key="$modalKey">
    {!! $form !!}

    <x-slot:footer>
        <button form="{{ $form->getKey() }}" type="submit" class="btn btn-primary">Run</button>
        <button type="button" class="btn btn--outline-primary" x-on:click="open = false">Cancel</button>
    </x-slot>
</x-root::modal>
