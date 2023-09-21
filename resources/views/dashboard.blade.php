@extends('root::app')

{{-- Title --}}
@section('title', 'Dashboard')

{{-- Content --}}
@section('content')
    <div class="l-row l-row--column:sm:2 l-row--column:lg:3">
        <div class="app-widget app-widget--primary">
            <div class="app-widget__column">
                <h2 class="app-widget__title">Orders</h2>
                <p class="app-widget__data">65</p>
                <div class="trending trending--up app-widget__trending">
                    <span class="trending__caption">+12%</span>
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='trending__icon'>
                        <polyline points='23 6 13.5 15.5 8.5 10.5 1 18'></polyline>
                        <polyline points='17 6 23 6 23 12'></polyline>
                    </svg>
                </div>
            </div>
            <div class="app-widget__chart">
                <div id="chart01"></div>
            </div>
        </div>
        <div class="app-widget ">
            <div class="app-widget__column">
                <h2 class="app-widget__title">Customers</h2>
                <p class="app-widget__data">54</p>
                <div class="trending trending--up app-widget__trending">
                    <span class="trending__caption">+6%</span>
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='trending__icon'>
                        <polyline points='23 6 13.5 15.5 8.5 10.5 1 18'></polyline>
                        <polyline points='17 6 23 6 23 12'></polyline>
                    </svg>
                </div>
            </div>
            <div class="app-widget__chart">
                <div id="chart02"></div>
            </div>
        </div>
        <div class="app-widget ">
            <div class="app-widget__column">
                <h2 class="app-widget__title">Users</h2>
                <p class="app-widget__data">184</p>
                <div class="trending trending--down app-widget__trending">
                    <span class="trending__caption">-12%</span>
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='trending__icon'>
                        <polyline points='23 18 13.5 8.5 8.5 13.5 1 6'></polyline>
                        <polyline points='17 18 23 18 23 12'></polyline>
                    </svg>
                </div>
            </div>
            <div class="app-widget__chart">
                <div id="chart03"></div>
            </div>
        </div>
    </div>
@endsection
