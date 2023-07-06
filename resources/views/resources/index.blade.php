@extends('root::app')

{{-- Title --}}
@section('title', $resource->getName())

{{-- Content --}}
@section('content')
<div class="l-row l-row--column:sm:2 l-row--column:lg:3">
    <div class="app-widget app-widget--summary">
        <div class="app-widget__icon">
            <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                <path d='M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z'></path>
                <line x1='3' y1='6' x2='21' y2='6'></line>
                <path d='M16 10a4 4 0 0 1-8 0'></path>
            </svg>
        </div>
        <div class="app-widget__column">
            <h2 class="app-widget__title">Total Products</h2>
            <div class="app-widget__data-row">
                <p class="app-widget__data">341</p>
                <div class="trending trending--up widget__trending">
                    <span class="trending__caption">+12%</span>
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='trending__icon'>
                        <polyline points='23 6 13.5 15.5 8.5 10.5 1 18'></polyline>
                        <polyline points='17 6 23 6 23 12'></polyline>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="app-widget app-widget--summary">
        <div class="app-widget__icon">
            <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                <polyline points='21 8 21 21 3 21 3 8'></polyline>
                <rect x='1' y='3' width='22' height='5'></rect>
                <line x1='10' y1='12' x2='14' y2='12'></line>
            </svg>
        </div>
        <div class="app-widget__column">
            <h2 class="app-widget__title">Total Inventory</h2>
            <div class="app-widget__data-row">
                <p class="app-widget__data">2,340</p>
            </div>
        </div>
    </div>
    <div class="app-widget app-widget--summary app-widget--small-data">
        <div class="app-widget__icon">
            <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                <polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'></polygon>
            </svg>
        </div>
        <div class="app-widget__column">
            <h2 class="app-widget__title">Best Selling</h2>
            <div class="app-widget__data-row">
                <p class="app-widget__data">ZephyrFit Fitness Tracker</p>
            </div>
        </div>
    </div>
</div>
<div class="app-card">
    <div class="app-card__header">
        <div class="app-card__actions">
            <form class="search-form ">
                <input class="form-control  search-form__control" type="text" placeholder="Search..." title="Search" />
                <button class="search-form__submit">
                    <span class="sr-only">Search</span>
                    <svg aria-hidden="true" class="search-form__icon" focusable="false" height="24" role="img" style="fill: currentColor" viewBox="0 0 24 24" width="24" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.501,9.75c-0,2.152 -0.699,4.14 -1.875,5.752l5.935,5.94c0.585,0.586 0.585,1.537 -0,2.123c-0.586,0.586 -1.538,0.586 -2.124,0l-5.935,-5.939c-1.612,1.181 -3.6,1.875 -5.752,1.875c-5.386,-0 -9.75,-4.364 -9.75,-9.751c0,-5.386 4.364,-9.75 9.75,-9.75c5.387,-0 9.751,4.364 9.751,9.75Zm-9.751,6.751c3.704,-0 6.751,-3.047 6.751,-6.751c-0,-3.703 -3.047,-6.75 -6.751,-6.75c-3.703,0 -6.75,3.047 -6.75,6.75c0,3.704 3.047,6.751 6.75,6.751Z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <x-root::table.table :items="$table['items']" :columns="$table['columns']" />
</div>
@endsection
