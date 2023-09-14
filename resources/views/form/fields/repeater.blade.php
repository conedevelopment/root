<div class="form-group">
    <span class="form-label">{{ $label }}</span>
    <div class="repeater-container">
        @foreach($options as $option)
            {!! $option !!}
        @endforeach
    </div>
    <div class="btn-dropdown">
        <button type="button" class="btn btn--primary btn--icon">
            {{ $addNewLabel }}
            <x-root::icon name="plus" class="btn__icon" />
        </button>
    </div>
</div>
