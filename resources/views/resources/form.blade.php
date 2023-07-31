@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
{!! $form !!}

<div class="app-actions app-actions--sidebar">
    <div class="app-actions__column">
        <button type="submit" class="btn btn--primary" form="{{ $form->getKey() }}">Save</button>
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
