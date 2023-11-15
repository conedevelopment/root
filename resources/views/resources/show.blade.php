@extends('root::app')

{{-- Title --}}
@section('title', $title)

{{-- Actions --}}
@section('actions')
    @can('update', $model)
        <a href="{{ $action }}/edit" class="btn btn--primary btn--icon">
            <x-root::icon name="edit" class="btn__icon" />
            {{ __('Edit') }}
        </a>
    @endcan
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
                                <tr>
                                    <th>{{ $field['label'] }}</th>
                                    @include('root::resources.table.cell', $field)
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
