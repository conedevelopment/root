<div class="form-group--row" x-data="{ selection: {{ json_encode($options) }} }">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <div class="file-list">
        <div class="form-group">
            <input {{ $attrs }}>
        </div>
        <ul class="file-list__items" x-show="selection.length > 0" x-cloak>
            <template x-for="(item, index) in selection" :key="item.uuid">
                <li x-html="item.html"></li>
            </template>
        </ul>
    </div>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>
