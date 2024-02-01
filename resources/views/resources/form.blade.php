@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
    <form
        id="{{ $key }}"
        method="POST"
        action="{{ $action }}"
        autocomplete="off"
        @if($uploads) enctype="multipart/form-data" @endif
    >
        @csrf
        @method($method)
        <div class="l-row">
            <div class="l-row__column">
                <div class="app-card app-card--edit">
                    <div class="app-card__header">
                        <h2 class="app-card__title">
                            {{ __(':resource Details', ['resource' => $modelName]) }}
                        </h2>
                    </div>
                    <div class="app-card__body">
                        <div class="form-group-stack form-group-stack--bordered form-group-container">
                            @foreach($fields as $field)
                                @include($field['template'], $field)
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="app-actions">
        <div class="app-actions__column">
            <button type="submit" class="btn btn--primary" form="{{ $key }}">{{ __('Save') }}</button>
            <a href="{{ $action }}" class="btn btn--light">{{ __('Cancel') }}</a>
        </div>
        @if($model->exists && $abilities['delete'])
            <div class="app-actions__column">
                <form method="POST" action="{{ $action }}" onsubmit="return window.confirm('{{ __('Are you sure?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--delete">{{ __('Delete') }}</button>
                </form>
            </div>
        @endif
    </div>
@endsection
