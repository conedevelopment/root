<template>
    <div class="list-footer">
        <div class="list-footer__pager">
            <select class="form-control form-control--sm" id="per-page">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label for="per-page">of 15 items</label>
        </div>
        <ul class="list-footer__pagination">
            <li>
                <button class="btn btn--primary btn--sm" type="button">Previous</button>
            </li>
            <li>
                <button class="btn btn--secondary btn--sm" type="button" aria-current="page">1</button>
            </li>
            <li>
                <button class="btn btn--primary btn--sm" type="button">2</button>
            </li>
            <li>
                <button class="btn btn--primary btn--sm" type="button">3</button>
            </li>
            <li>
                <button class="btn btn--primary btn--sm" type="button">Next</button>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        //
    }
</script>
