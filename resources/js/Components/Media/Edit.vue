<template>
    <div class="media-preview">
        <div>
            <img class="media-preview__image" src="http://root-app.test/storage/ea5ba9f2-630a-41e6-bbbd-1a167627fdc2/226917-P1ML5S-720.jpg" alt="226917-P1ML5S-720"/>
        </div>
        <div class="media-preview__sidebar">
            <div class="media-preview__sidebar-section">
                <h2 class="media-preview__title">Adatok</h2>
                <ul class="media-sidebar__list mt-3 mb-3">
                    <li><strong>Created At</strong>: 2022-04-07 07:35</li>
                    <li><strong>Size</strong>: 648 KB</li>
                    <li><strong>Dimensions</strong>: <span>1920&times;1080 px</span></li>
                </ul>
            </div>
            <div class="media-preview__sidebar-section">
                <h2 class="media-preview__title">Szerkesztés</h2>
                <div class="form-group-stack">
                    <div class="form-group">
                        <label class="form-label" for="media.2af3748c-b847-4314-b4c9-772d30451f65.type">
                            <span>Type</span>
                        </label>
                        <select class="form-control" id="media.2af3748c-b847-4314-b4c9-772d30451f65.type" name="media.2af3748c-b847-4314-b4c9-772d30451f65.type">
                            <option disabled="">Select Type</option>
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="media.2af3748c-b847-4314-b4c9-772d30451f65.alt">
                            <span>Alt</span>
                        </label>
                        <input class="form-control" id="media.2af3748c-b847-4314-b4c9-772d30451f65.alt" type="text" name="media.2af3748c-b847-4314-b4c9-772d30451f65.alt">
                    </div>
                    <div>
                        <button type="button" class="btn btn--delete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        //
    }
</script>
