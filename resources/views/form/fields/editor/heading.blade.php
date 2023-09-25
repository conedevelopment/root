<select
    class="form-control form-control--sm"
    aria-label="{{ __('Heading level') }}"
    x-on:change="($event) => {
        $event.target.value
            ? editor().chain().focus().setHeading({ level: parseInt($event.target.value) }).run()
            : editor().commands.setParagraph()
    }"
>
    <option value="" x-bind:selected="! isActive('heading', {}, updatedAt)">{{ __('Paragraph') }}</option>
    <option value="1" x-bind:selected="isActive('heading', { level: 1 }, updatedAt)">{{ __('Heading 1') }}</option>
    <option value="2" x-bind:selected="isActive('heading', { level: 2 }, updatedAt)">{{ __('Heading 2') }}</option>
    <option value="3" x-bind:selected="isActive('heading', { level: 3 }, updatedAt)">{{ __('Heading 3') }}</option>
    <option value="4" x-bind:selected="isActive('heading', { level: 4 }, updatedAt)">{{ __('Heading 4') }}</option>
    <option value="5" x-bind:selected="isActive('heading', { level: 5 }, updatedAt)">{{ __('Heading 5') }}</option>
    <option value="6" x-bind:selected="isActive('heading', { level: 6 }, updatedAt)">{{ __('Heading 6') }}</option>
</select>
