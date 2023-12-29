<div class="search-form">
    <input {{ $attrs }} value="{{ $value }}">
    <button type="submit" class="search-form__submit">
        <span class="sr-only">{{ $label }}</span>
        <x-root::icon name="search" class="search-form__icon" />
    </button>
</div>
