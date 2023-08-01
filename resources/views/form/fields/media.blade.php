<div class="form-group--row">
    <span class="form-label">{{ $label }}</span>
    <div class="file-list">
        <button type="button" class="btn btn--primary btn--lg btn--block" x-on:click="$dispatch('open-{{ $modalKey }}')">
            Choose file(s)
        </button>
        <ul class="file-list__items">
            <li class="file-list-item">
                <div class="file-list-item__column">
                    <img class="file-list-item__thumbnail" src="https://picsum.photos/80/80" alt="">
                    <span id="list-item-1" class="file-list-item__name">pic1234.jpg</span>
                </div>
                <div class="file-list-item__actions">
                    <button class="btn btn--light btn--sm btn--icon" aria-describedby="list-item-1" aria-label="Move one up" disabled>
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='18 15 12 9 6 15'></polyline>
                        </svg>
                    </button>
                    <button class="btn btn--light btn--sm btn--icon" aria-describedby="list-item-1" aria-label="Move one down">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='6 9 12 15 18 9'></polyline>
                        </svg>
                    </button>
                    <button class="btn btn--delete btn--sm btn--icon" aria-describedby="list-item-1" aria-label="Remove">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <line x1='18' y1='6' x2='6' y2='18'></line>
                            <line x1='6' y1='6' x2='18' y2='18'></line>
                        </svg>
                    </button>
                </div>
            </li>
        </ul>
    </div>
</div>

@push('modals')
    <x-root::modal :key="$modalKey">
        asdasdasd
    </x-root::modal>
@endpush
