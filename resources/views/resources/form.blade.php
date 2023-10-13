@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
@if($errors->isNotEmpty())
    <x-root::alert type="danger">
        {{ __('Some error occurred when submitting the form!') }}
    </x-root::alert>
@endif

<form id="{{ $key }}" method="POST" action="{{ $action }}" autocomplete="off">
    @csrf
    @method($method)

    <div class="l-row l-row--sidebar">
        <div class="l-row__column">
            <div class="app-card app-card--edit">
                <div class="app-card__header">
                    <h2 class="app-card__title">General</h2>
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

<div class="app-actions app-actions--sidebar">
    <div class="app-actions__column">
        <button type="submit" class="btn btn--primary" form="{{ $key }}">Save</button>
        <button class="btn btn--light">Cancel</button>
    </div>
    @if($model->exists)
        <div class="app-actions__column">
            <form method="POST" action="#">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--delete">Delete</button>
            </form>
        </div>
    @endif
</div>
@endsection
