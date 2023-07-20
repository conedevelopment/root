@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
{!! $form->render() !!}

<div class="app-actions app-actions--sidebar">
    <div class="app-actions__column">
        <button class="btn btn--primary">Save</button>
        <button class="btn btn--light">Cancel</button>
    </div>
    <div class="app-actions__column">
        <button class="btn btn--delete">Delete</button>
    </div>
</div>
@endsection
