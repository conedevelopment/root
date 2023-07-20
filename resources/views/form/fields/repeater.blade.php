<div class="form-group">
    <span class="form-label">Section builder</span>
    <div class="repeater-container">
        <div class="repeater">
            <div class="repeater__heading">
                <div class="repeater__column">
                    <button class="btn btn--primary btn--sm btn--icon repeater__toggle" data-action="toggle" aria-label="Toggle section" aria-expanded="true" aria-describedby="repeater-item-1">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <line class='vertical-line' x1='12' y1='5' x2='12' y2='19'></line>
                            <line x1='5' y1='12' x2='19' y2='12'></line>
                        </svg>
                    </button>
                    <h3 id="repeater-item-1" class="repeater__title">
                        <span class="repeater__order">#1</span>
                        YouTube Video
                    </h3>
                </div>
                <div class="repeater__actions">
                    <button class="btn btn--light btn--sm btn--icon" aria-describedby="repeater-item-1" aria-label="Move one up" disabled>
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='18 15 12 9 6 15'></polyline>
                        </svg>
                    </button>
                    <button class="btn btn--light btn--sm btn--icon" aria-describedby="repeater-item-1" aria-label="Move one down">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='6 9 12 15 18 9'></polyline>
                        </svg>
                    </button>
                    <button class="btn btn--delete btn--sm btn--icon" aria-describedby="repeater-item-1" aria-label="Remove">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <line x1='18' y1='6' x2='6' y2='18'></line>
                            <line x1='6' y1='6' x2='18' y2='18'></line>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="repeater__body">
                <div class="form-group-stack form-group-stack--bordered form-group-container">
                    <div class="form-group--row">
                        <label class="form-label" for="youtube-title">Title</label>
                        <input class="form-control" id="youtube-title" name="youtube-title" type="text" />
                    </div>
                    <div class="form-group--row">
                        <label class="form-label" for="youtube-thumbnail">Thumbnail</label>
                        <input class="form-file form-file" type="file" id="youtube-thumbnail" accept="image/png, image/jpeg" />
                    </div>
                    <div class="form-group--row">
                        <label class="form-label" for="youtube-id">YouTube ID</label>
                        <input class="form-control" id="youtube-id" name="youtube-id" type="text" />
                    </div>
                </div>
            </div>
        </div>
        <div class="repeater">
            <div class="repeater__heading">
                <div class="repeater__column">
                    <button class="btn btn--primary btn--sm btn--icon repeater__toggle" data-action="toggle" aria-label="Toggle section" aria-expanded="false" aria-describedby="repeater-item-2">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <line class='vertical-line' x1='12' y1='5' x2='12' y2='19'></line>
                            <line x1='5' y1='12' x2='19' y2='12'></line>
                        </svg>
                    </button>
                    <h3 id="repeater-item-2" class="repeater__title">
                        <span class="repeater__order">#2</span>
                        Testimonial
                    </h3>
                </div>
                <div class="repeater__actions">
                    <button class="btn btn--light btn--sm btn--icon" aria-describedby="repeater-item-2" aria-label="Move one up">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='18 15 12 9 6 15'></polyline>
                        </svg>
                    </button>
                    <button class="btn btn--light btn--sm btn--icon" aria-describedby="repeater-item-2" aria-label="Move one down" disabled>
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='6 9 12 15 18 9'></polyline>
                        </svg>
                    </button>
                    <button class="btn btn--delete btn--sm btn--icon" aria-describedby="repeater-item-2" aria-label="Remove">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <line x1='18' y1='6' x2='6' y2='18'></line>
                            <line x1='6' y1='6' x2='18' y2='18'></line>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="repeater__body" hidden>
                <div class="form-group-stack form-group-stack--bordered form-group-container">
                    <div class="form-group--row">
                        <label class="form-label" for="testimonial-title">Title</label>
                        <input class="form-control" id="testimonial-title" name="testimonial-title" type="text" />
                    </div>
                    <div class="form-group--row">
                        <label class="form-label" for="testimonial-description">Description</label>
                        <textarea class="form-control" id="testimonial-description" name="testimonial-description" rows="4"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-dropdown">
            <button class="btn btn--primary btn--icon" aria-expanded="false" data-action="context" data-context-target="repeater">
                Add section
                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                    <polyline points='6 9 12 15 18 9'></polyline>
                </svg>
            </button>
            <ul class="context-menu context-menu--inline-start" data-state="closed" data-context-id="repeater">
                <li>
                    <button class="context-menu__item">YouTube Video</button>
                </li>
                <li>
                    <button class="context-menu__item">Testimonial</button>
                </li>
            </ul>
        </div>
    </div>
</div>
