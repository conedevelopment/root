@extends('root::app')

{{-- Title --}}
@section('title', 'Dashboard')

{{-- Content --}}
@section('content')
    <div class="l-row">
        @foreach($widgets as $widget)
            @include($widget['template'], $widget)
        @endforeach
    </div>
@endsection
