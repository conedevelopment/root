<div class="form-group">
    <span class="form-label">{{ $label }}</span>
    <div class="repeater-container">
        <div class="repeater">
            <div class="repeater__heading">
                <div class="repeater__column">
                    <button
                        type="button"
                        class="btn btn--primary btn--sm btn--icon repeater__toggle"
                        aria-label="Toggle section"
                        aria-expanded="true"
                        aria-describedby="repeater-item-1"
                    >
                        <x-root::icon name="minus" class="btn__icon"/>
                    </button>
                    <h3 id="repeater-item-1" class="repeater__title">
                        <span class="repeater__order">#1</span>
                        Section
                    </h3>
                </div>
                <div class="repeater__actions">
                    <button
                        type="button"
                        class="btn btn--light btn--sm btn--icon"
                        aria-describedby="repeater-item-1"
                        aria-label="Move one up"
                        disabled
                    >
                        <x-root::icon name="chevron-up" class="btn__icon" />
                    </button>
                    <button
                        type="button"
                        class="btn btn--light btn--sm btn--icon"
                        aria-describedby="repeater-item-1"
                        aria-label="Move one down"
                    >
                        <x-root::icon name="chevron-down" class="btn__icon" />
                    </button>
                    <button
                        type="button"
                        class="btn btn--delete btn--sm btn--icon"
                        aria-describedby="repeater-item-1"
                        aria-label="Remove"
                    >
                        <x-root::icon name="close" class="btn__icon" />
                    </button>
                </div>
            </div>
            <div class="repeater__body">
                <div class="form-group-stack form-group-stack--bordered form-group-container">
                    <div class="form-group--row">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
