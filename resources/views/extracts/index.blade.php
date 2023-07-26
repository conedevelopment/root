@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
@if($widgets->isNotEmpty())
    <div class="l-row l-row--column:sm:2 l-row--column:lg:3">
        @foreach($widgets as $widtet)
            {!! $widget->render() !!}
        @endforeach
    </div>
@endif

{!! $table->render() !!}
@endsection
