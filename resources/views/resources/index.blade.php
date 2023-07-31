@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Content --}}
@section('content')
@if($widgets->isNotEmpty())
    <div class="l-row l-row--column:sm:2 l-row--column:lg:3">
        @foreach($widgets->all() as $widget)
            {!! $widget !!}
        @endforeach
    </div>
@endif

{!! $table !!}
@endsection
