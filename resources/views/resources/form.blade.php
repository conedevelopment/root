@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
{!! $form->render() !!}

<div class="app-actions app-actions--sidebar">
    <div class="app-actions__column">
        <button type="button" class="btn btn--primary">Save</button>
        <button class="btn btn--light">Cancel</button>
    </div>
    @if($model->exists)
        <div class="app-actions__column">
            <button class="btn btn--delete">Delete</button>
        </div>
    @endif
</div>
@endsection
