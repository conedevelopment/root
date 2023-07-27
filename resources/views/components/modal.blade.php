<div class="modal-backdrop" x-data="{ open: false }" x-show="open" x-on:keydown.escape="open = false">
    <div role="dialog" aria-modal="true" tabindex="0" class="modal" x-on:click.away="open = false" x-trap.noscroll="open">
        <div class="modal__header">
            <div class="modal__header-caption">
                <h2 class="modal__title">Notification settings</h2>
                <p class="modal__subtitle">Set notifications for this post</p>
            </div>
            <button class="btn btn--icon btn--light" aria-label="Close modal" x-on:click="open = false">
                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                    <line x1='18' y1='6' x2='6' y2='18'></line>
                    <line x1='6' y1='6' x2='18' y2='18'></line>
                </svg>
            </button>
        </div>
        <div class="modal__body">
            <fieldset>
                <div class="form-group--row form-group--row:vertical-start" style="--spruce-gap: 1rem 3rem; --spruce-label-inline-size: 14rem;">
                    <legend>
                        Email notifications
                        <span class="form-description">Configure tools that integrate with Code Scanning to keep the quality of your code under control.</span>
                    </legend>
                    <div class="form-group form-group--vertical-check">
                        <label class="form-switch form-switch--vertical-start">
                            <input class="form-switch__control" type="checkbox" value="true" checked />
                            <span class="form-label form-switch__label">
                                News and updates
                                <span class="form-description">Quam at orci ullamcorper fringilla.</span>
                            </span>
                        </label>
                        <label class="form-switch form-switch--vertical-start">
                            <input class="form-switch__control" type="checkbox" value="true" checked />
                            <span class="form-label form-switch__label">Tips and tutorials</span>
                        </label>
                        <label class="form-switch form-switch--vertical-start">
                            <input class="form-switch__control" type="checkbox" value="true" />
                            <span class="form-label form-switch__label">Comments</span>
                        </label>
                        <label class="form-switch form-switch--vertical-start">
                            <input class="form-switch__control" type="checkbox" value="true" />
                            <span class="form-label form-switch__label">Reminders</span>
                        </label>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="modal__footer">
            <button type="button" class="btn btn--outline-primary" x-on:click="open = false">Cancel</button>
            <button type="button" class="btn btn--primary">Save</button>
        </div>
    </div>
</div>
