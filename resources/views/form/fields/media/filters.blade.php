<div class="modal__filter">
    <select class="form-control">
        <option value="all">All media items</option>
        <option value="images">Images</option>
        <option value="video">Video</option>
        <option value="audio">Audio</option>
        <option value="documents">Documents</option>
    </select>
    <select class="form-control">
        <option value="all">All dates</option>
        <option value="july-2023">July 2023</option>
        <option value="juni-2023">Juni 2023</option>
        <option value="may-2023">May 2023</option>
    </select>
    <div class="search-form">
        <input class="form-control  search-form__control" type="text" placeholder="Search..." title="Search" />
        <button type="button" class="search-form__submit">
            <span class="sr-only">Search</span>
            <x-root::icon name="search" class="search-form__icon" />
        </button>
    </div>
</div>
