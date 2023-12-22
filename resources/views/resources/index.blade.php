@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Actions --}}
@section('actions')
    @can('create', $model)
        <a href="{{ $url }}/create" class="btn btn--primary btn--icon">
            <x-root::icon name="plus" class="btn__icon" />
            {{ __('Add :resource', ['resource' => $modelName]) }}
        </a>
    @endcan
@endsection

{{-- Content --}}
@section('content')
    @if(! empty($widgets))
        <div class="l-row l-row--column:sm:2 l-row--column:lg:3">
            @foreach($widgets as $widget)
                <turbo-frame id="widget-{{ $widget['key'] }}" src="{{ $widget['url'] }}">
                    @include('root::widgets.pending-widget', $widget)
                </furbo-frame>
            @endforeach
        </div>
    @endif

    @include('root::table.table')
@endsection
