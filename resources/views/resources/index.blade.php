@extends('root::app')

{{-- Title --}}
@section('title', $resource->getName())

{{-- Content --}}
@section('content')
@if($widgets->isNotEmpty())
    <div class="l-row l-row--column:sm:2 l-row--column:lg:3">
        @foreach($widgets as $widtet)
            <x-dynamic-component :component="$widget->getComponent()" :widget="$widget" />
        @endforeach
    </div>
@endif

{!! $table->render() !!}
@endsection
