@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Actions --}}
@section('actions')
    @if(! empty($actions))
        <div x-data="{ selection: [{{ $model->getKey() }}], selectedAllMatchingQuery: false }">
            @include('root::actions.actions')
        </div>
    @endif
    @if($abilities['delete'])
        <form method="POST" action="{{ $action }}" onsubmit="return window.confirm('{{ __('Are you sure?') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--delete btn--icon">
                <x-root::icon name="trash" class="btn__icon" />
                {{ __('Delete') }}
            </button>
        </form>
    @endif
    @if($abilities['update'])
        <a href="{{ $action }}/edit" class="btn btn--primary btn--icon">
            <x-root::icon name="edit" class="btn__icon" />
            {{ __('Edit') }}
        </a>
    @endif
@endsection

{{-- Content --}}
@section('content')
    <div class="l-row">
        <div class="l-row__column">
            <div class="app-card">
                <div class="app-card__body">
                    <div class="table-responsive">
                        <table class="table">
                            @foreach($fields as $field)
                                <tr class="vertical-align:top">
                                    <th>{{ $field['label'] }}</th>
                                    @include('root::table.cell', $field)
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($model->exists && isset($relations))
            <div class="l-row__column">
                @foreach($relations as $relation)
                    @if($relation['abilities']['viewAny'])
                        <turbo-frame id="relation-{{ $relation['attribute'] }}" src="{{ $relation['url'] }}">
                            <div class="app-card">
                                <div class="app-card__header">
                                    <h2 class="app-card__title">{{ $relation['label'] }}</h2>
                                </div>
                                <div class="app-card__body">
                                    <div class="data-table">
                                        <x-root::alert>
                                            {{ __('Loading') }}...
                                        </x-root::alert>
                                    </div>
                                </div>
                            </div>
                        </turbo-frame>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection
