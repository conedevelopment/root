<li
    class="media-item"
    tabindex="0"
>
    <span class="media-item__background">
        <div class="progressbar">
            <div
                class="progressbar__inner"
                role="progressbar"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-labelledby="progress-caption"
                x-bind:aria-valuenow="item.progress"
                x-bind:aria-labelledby="item.hash"
            >
                <div class="progressbar__indicator" x-bind:style="item.progress && { inlineSize: item.progress + '%' }"></div>
            </div>
            <div x-bind:id="item.hash" class="progressbar__caption" x-text="item.name"></div>
        </div>
    </span>
</li>
