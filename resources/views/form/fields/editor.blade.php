<div
    class="form-group--row form-group--row:vertical-start"
    x-data="editor({{ json_encode($config) }})"
>
    <label class="form-label" for="{{ $attrs->get('id') }}" x-on:click="$refs.editor.firstChild.focus()">
        {{ $label }}
        @if($attrs->get('required'))
            <span class="required-marker">*</span>
        @endif
    </label>
    <div class="editor">
        <template x-if="editor()">
            <div class="editor__controls">
                @include('root::form.fields.editor.heading')
                @include('root::form.fields.editor.format')
                @include('root::form.fields.editor.align')
                @include('root::form.fields.editor.list')
                @include('root::form.fields.editor.link')
                @isset($media)
                    {!! $media !!}
                @endisset
                @include('root::form.fields.editor.blocks')
                @include('root::form.fields.editor.history')
            </div>
        </template>
        <div class="editor__body" x-ref="editor"></div>
        <textarea style="width: 0; height: 0;" hidden readonly x-ref="input" name="{{ $attrs->get('name') }}">{{ $value }}</textarea>
    </div>
    @if($invalid)
        <span class="field-feedback field-feedback--invalid">{!! $error !!}</span>
    @endif
    @if($help)
        <span class="form-description">{!! $help !!}</span>
    @endif
</div>

{{-- Modal --}}

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/editor.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
