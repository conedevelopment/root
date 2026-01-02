@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
    @include('root::resources.form-turbo-frame')

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
